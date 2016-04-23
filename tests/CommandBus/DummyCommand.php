<?php

namespace NilPortugues\Tests\MessageBus\CommandBus;

use NilPortugues\MessageBus\CommandBus\Contracts\Command;

class DummyCommand implements Command
{
    public function __invoke()
    {
    }
}
