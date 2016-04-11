<?php

namespace NilPortugues\Tests\MessageBus\CommandBus;

use NilPortugues\MessageBus\CommandBus\Contracts\Command;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandHandler;

class DummyCommandHandler implements CommandHandler
{
    /**
     * @param Command $command
     */
    public function __invoke(Command $command)
    {
    }
}
