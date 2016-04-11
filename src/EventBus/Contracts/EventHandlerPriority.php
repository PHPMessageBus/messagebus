<?php

namespace NilPortugues\MessageBus\EventBus\Contracts;

interface EventHandlerPriority
{
    const HIGH_PRIORITY = PHP_INT_MAX;
    const LOW_PRIORITY = PHP_INT_MIN;

    /**
     * Returns the priority of the event.
     *
     * @return int
     */
    public static function priority() : int;
}
