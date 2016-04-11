<?php

namespace NilPortugues\Tests\MessageBus\QueryBus;

use NilPortugues\MessageBus\QueryBus\CacheQueryBusMiddleware;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryHandlerResolver;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryResponse;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryTranslator;
use NilPortugues\MessageBus\QueryBus\LoggerQueryBusMiddleware;
use NilPortugues\MessageBus\QueryBus\QueryBus;
use NilPortugues\MessageBus\QueryBus\QueryBusMiddleware;
use NilPortugues\MessageBus\QueryBus\Resolver\SimpleArrayResolver;
use NilPortugues\MessageBus\QueryBus\Translator\AppendStrategy;
use NilPortugues\MessageBus\Serializer\NativeSerializer;
use NilPortugues\Tests\MessageBus\InMemoryCache;
use NilPortugues\Tests\MessageBus\InMemoryLogger;

class QueryBusTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    protected $handlers;

    /** @var QueryTranslator */
    protected $translator;

    /** @var QueryHandlerResolver */
    protected $resolver;

    public function setUp()
    {
        $this->handlers = $handlers = [
            DummyQueryHandler::class => function () {
                return new DummyQueryHandler();
            },
        ];

        $this->translator = new AppendStrategy('Handler');
        $this->resolver = new SimpleArrayResolver($handlers);
    }

    public function testItCanStackMiddleware()
    {
        $logger = new InMemoryLogger();

        $middleware = [
            new LoggerQueryBusMiddleware($logger),
            new CacheQueryBusMiddleware(new NativeSerializer(), new InMemoryCache(), 60),
            new QueryBusMiddleware($this->translator, $this->resolver),
        ];

        $queryBus = new QueryBus($middleware);
        $response = $queryBus->__invoke(new DummyQuery());
        $this->assertNotEmpty($logger->logs());
        $this->assertInstanceOf(QueryResponse::class, $response);
    }
}
