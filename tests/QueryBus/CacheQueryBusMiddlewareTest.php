<?php

namespace NilPortugues\Tests\MessageBus\QueryBus;

use InvalidArgumentException;
use NilPortugues\MessageBus\QueryBus\CacheQueryBusMiddleware;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryHandlerResolver;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryResponse;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryTranslator;
use NilPortugues\MessageBus\QueryBus\QueryBusMiddleware;
use NilPortugues\MessageBus\QueryBus\Resolver\SimpleArrayResolver;
use NilPortugues\MessageBus\QueryBus\Translator\AppendStrategy;
use NilPortugues\MessageBus\Serializer\NativeSerializer;
use NilPortugues\Tests\MessageBus\InMemoryCache;

class CacheQueryBusMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    protected $handlers;

    /** @var QueryTranslator */
    protected $translator;

    /** @var QueryHandlerResolver */
    protected $resolver;

    /** @var CacheQueryBusMiddleware */
    protected $cacheMiddleware;

    public function setUp()
    {
        $this->handlers = $handlers = [
            DummyQueryHandler::class => function () {
                return new DummyQueryHandler();
            },
        ];

        $this->translator = new AppendStrategy('Handler');
        $this->resolver = new SimpleArrayResolver($handlers);

        $this->cacheMiddleware = new CacheQueryBusMiddleware(new NativeSerializer(), new InMemoryCache(), 60);
    }

    public function testItCanCache()
    {
        $response = $this->cacheMiddleware->__invoke(new DummyQuery(), function ($command) {
            $queryBus = new QueryBusMiddleware($this->translator, $this->resolver);

            return $queryBus->__invoke($command);
        });

        $this->assertInstanceOf(QueryResponse::class, $response);
    }

    public function testItHitsCache()
    {
        for ($i = 0; $i <= 2; ++$i) {
            $response = $this->cacheMiddleware->__invoke(new DummyQuery(), function ($command) {
                $queryBus = new QueryBusMiddleware($this->translator, $this->resolver);

                return $queryBus->__invoke($command);
            });

            $this->assertInstanceOf(QueryResponse::class, $response);
        }
    }

    public function testItThrowsExceptionIfCacheMiddlewareHasNoNextCallable()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->cacheMiddleware->__invoke(new DummyQuery());
    }
}
