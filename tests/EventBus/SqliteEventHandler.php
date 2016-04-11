<?php

namespace NilPortugues\Tests\MessageBus\EventBus;

use NilPortugues\MessageBus\EventBus\Contracts\Event;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandler;

class SqliteEventHandler implements EventHandler
{
    /**
     * Returns the name of the event subscribed to.
     *
     * @return string
     */
    public static function subscribedTo() : string
    {
        return SqliteEvent::class;
    }

    /**
     * @param Event $event
     */
    public function __invoke(Event $event)
    {
    }
}
