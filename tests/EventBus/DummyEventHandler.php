<?php

namespace NilPortugues\Tests\MessageBus\EventBus;

use NilPortugues\MessageBus\EventBus\Contracts\Event;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandler;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandlerPriority;

class DummyEventHandler implements EventHandler, EventHandlerPriority
{
    /**
     * @param Event $event
     */
    public function __invoke(Event $event)
    {
    }

    /**
     * Returns the priority of the event.
     *
     * @return int
     */
    public static function priority() : int
    {
        return EventHandlerPriority::HIGH_PRIORITY;
    }

    /**
     * Returns the name of the event subscribed to.
     *
     * @return string
     */
    public static function subscribedTo() : string
    {
        return DummyEvent::class;
    }
}
