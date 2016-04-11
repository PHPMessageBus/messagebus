<?php

namespace NilPortugues\Tests\MessageBus\QueryBus;

use NilPortugues\MessageBus\QueryBus\Contracts\QueryHandlerResolver;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryResponse;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryTranslator;
use NilPortugues\MessageBus\QueryBus\LoggerQueryBusMiddleware;
use NilPortugues\MessageBus\QueryBus\QueryBusMiddleware;
use NilPortugues\MessageBus\QueryBus\Resolver\SimpleArrayResolver;
use NilPortugues\MessageBus\QueryBus\Translator\AppendStrategy;
use NilPortugues\Tests\MessageBus\InMemoryLogger;

class LoggerQueryBusMiddlewareTest extends \PHPUnit_Framework_TestCase
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

    public function testItCanLog()
    {
        $logger = new InMemoryLogger();

        $loggerQueryBus = new LoggerQueryBusMiddleware($logger);

        $response = $loggerQueryBus->__invoke(new DummyQuery(), function ($command) {
            $queryBus = new QueryBusMiddleware($this->translator, $this->resolver);

            return $queryBus->__invoke($command);
        });

        $this->assertNotEmpty($logger->logs());
        $this->assertArrayHasKey('info', $logger->logs());
        $this->assertInstanceOf(QueryResponse::class, $response);
    }

    public function testItCanLogError()
    {
        $logger = new InMemoryLogger();

        $loggerQueryBus = new LoggerQueryBusMiddleware($logger);

        $response = $loggerQueryBus->__invoke(new DummyQuery(), function () {
            throw new \Exception('Fail happens.');
        });

        $this->assertNotEmpty($logger->logs());
        $this->assertArrayHasKey('alert', $logger->logs());
        $this->assertInstanceOf(QueryResponse::class, $response);
    }
}
