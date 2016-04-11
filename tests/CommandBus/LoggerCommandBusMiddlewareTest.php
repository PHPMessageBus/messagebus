<?php

namespace NilPortugues\Tests\MessageBus\CommandBus;

use NilPortugues\MessageBus\CommandBus\Contracts\CommandHandlerResolver;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandTranslator;
use NilPortugues\MessageBus\CommandBus\LoggerCommandBusMiddleware;
use NilPortugues\MessageBus\CommandBus\Resolver\SimpleArrayResolver;
use NilPortugues\MessageBus\CommandBus\Translator\AppendStrategy;
use NilPortugues\Tests\MessageBus\InMemoryLogger;

class LoggerCommandBusMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    protected $handlers;

    /** @var CommandTranslator */
    protected $translator;

    /** @var CommandHandlerResolver */
    protected $resolver;

    public function setUp()
    {
        $this->handlers = $handlers = [
            DummyCommandHandler::class => function () {
                return new DummyCommandHandler();
            },
        ];

        $this->translator = new AppendStrategy('Handler');
        $this->resolver = new SimpleArrayResolver($handlers);
    }

    public function testItCanLog()
    {
        $logger = new InMemoryLogger();

        $commandBus = new LoggerCommandBusMiddleware($logger);
        $commandBus->__invoke(new DummyCommand(), function ($command) {

        });

        $this->assertNotEmpty($logger->logs());
        $this->assertArrayHasKey('info', $logger->logs());
    }

    public function testItCanLogError()
    {
        $logger = new InMemoryLogger();

        $commandBus = new LoggerCommandBusMiddleware($logger);
        $commandBus->__invoke(new DummyCommand(), function () {
            throw new \Exception('Fail happens.');
        });

        $this->assertNotEmpty($logger->logs());
        $this->assertArrayHasKey('alert', $logger->logs());
    }
}
