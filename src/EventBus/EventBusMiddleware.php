<?php

namespace NilPortugues\MessageBus\EventBus;

use NilPortugues\MessageBus\EventBus\Contracts\Event;
use NilPortugues\MessageBus\EventBus\Contracts\EventBusMiddleware as EventBusMiddlewareInterface;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandlerResolver;
use NilPortugues\MessageBus\EventBus\Contracts\EventTranslator;

/**
 * Class EventBusMiddleware.
 */
class EventBusMiddleware implements EventBusMiddlewareInterface
{
    /** @var EventTranslator */
    protected $eventTranslator;

    /** @var EventHandlerResolver */
    protected $handlerResolver;

    /**
     * @param EventTranslator      $eventTranslator
     * @param EventHandlerResolver $handlerResolver
     */
    public function __construct(EventTranslator $eventTranslator, EventHandlerResolver $handlerResolver)
    {
        $this->eventTranslator = $eventTranslator;
        $this->handlerResolver = $handlerResolver;
    }

    /**
     * @param Event         $event
     * @param callable|null $next
     */
    public function __invoke(Event $event, callable $next = null)
    {
        $handlerNames = $this->eventTranslator->handlerName($event);

        foreach ($handlerNames as $handlerLists) {
            foreach ($handlerLists as $handlerName) {
                $handlerInstance = $this->handlerResolver->instantiate($handlerName);
                $handlerInstance($event);
            }
        }

        if ($next) {
            $next($event);
        }
    }
}
