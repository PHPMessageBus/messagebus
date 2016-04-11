<?php

namespace NilPortugues\Tests\MessageBus\EventBus;

use NilPortugues\MessageBus\EventBus\Contracts\EventHandlerResolver;
use NilPortugues\MessageBus\EventBus\Contracts\EventTranslator;
use NilPortugues\MessageBus\EventBus\LoggerEventBusMiddleware;
use NilPortugues\MessageBus\EventBus\Resolver\SimpleArrayResolver;
use NilPortugues\MessageBus\EventBus\Translator\EventFullyQualifiedClassNameStrategy;
use NilPortugues\Tests\MessageBus\InMemoryLogger;

class LoggerEventBusMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    protected $handlers;

    /** @var EventTranslator */
    protected $translator;

    /** @var EventHandlerResolver */
    protected $resolver;

    public function setUp()
    {
        $this->handlers = $handlers = [
            DummyEventHandler::class => function () {
                return new DummyEventHandler();
            },
        ];

        $this->translator = new EventFullyQualifiedClassNameStrategy([
            DummyEventHandler::class,
        ]);

        $this->resolver = new SimpleArrayResolver($handlers);
    }

    public function testItCanLog()
    {
        $logger = new InMemoryLogger();

        $eventBus = new LoggerEventBusMiddleware($logger);
        $eventBus->__invoke(new DummyEvent(), function ($event) {

        });

        $this->assertNotEmpty($logger->logs());
        $this->assertArrayHasKey('info', $logger->logs());
    }

    public function testItCanLogError()
    {
        $logger = new InMemoryLogger();

        $eventBus = new LoggerEventBusMiddleware($logger);
        $eventBus->__invoke(new DummyEvent(), function () {
            throw new \Exception('Fail happens.');
        });

        $this->assertNotEmpty($logger->logs());
        $this->assertArrayHasKey('alert', $logger->logs());
    }
}
