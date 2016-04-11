<?php

namespace NilPortugues\MessageBus\EventBus\Contracts;

/**
 * Interface EventTranslator.
 */
interface EventTranslator
{
    /**
     * Given a event, find the EventHandler names.
     *
     * @param Event $event
     *
     * @return string[]
     */
    public function handlerName(Event $event) : array;
}
