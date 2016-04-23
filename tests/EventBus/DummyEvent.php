<?php

namespace NilPortugues\Tests\MessageBus\EventBus;

use NilPortugues\MessageBus\EventBus\Contracts\Event;

class DummyEvent implements Event
{
    public function __invoke()
    {
    }
}
