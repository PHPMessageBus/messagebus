<?php

namespace NilPortugues\Tests\MessageBus\CommandBus;

use NilPortugues\MessageBus\CommandBus\Contracts\CommandHandler;

class DummyCommandHandler implements CommandHandler
{
    /**
     * @param callable $command
     */
    public function __invoke($command)
    {
    }
}
