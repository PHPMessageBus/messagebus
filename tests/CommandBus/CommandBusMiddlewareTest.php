<?php

namespace NilPortugues\Tests\MessageBus\CommandBus;

use NilPortugues\MessageBus\CommandBus\CommandBusMiddleware;
use NilPortugues\MessageBus\CommandBus\Resolver\SimpleArrayResolver;
use NilPortugues\MessageBus\CommandBus\Translator\AppendStrategy;

class CommandBusMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @var CommandBusMiddleware */
    protected $commandBus;

    public function setUp()
    {
        $handlers = [
            DummyCommandHandler::class => function () {
                return new DummyCommandHandler();
            },
        ];

        $this->commandBus = new CommandBusMiddleware(new AppendStrategy('Handler'), new SimpleArrayResolver($handlers));
    }

    public function testItCanHandle()
    {
        $this->commandBus->__invoke(new DummyCommand(), function () {
            return;
        });
        $this->assertTrue(true);
    }
}
