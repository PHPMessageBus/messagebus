<?php

namespace NilPortugues\MessageBus\EventBus\Contracts;

/**
 * Interface EventHandlerResolver.
 */
interface EventHandlerResolver
{
    /**
     * Given a string to identify the Event Handler, return the instance.
     *
     * @param string $handler
     *
     * @return EventHandler
     */
    public function instantiate(string $handler) : EventHandler;
}
