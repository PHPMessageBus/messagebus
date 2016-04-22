<?php

namespace NilPortugues\MessageBus\EventBus;

use NilPortugues\Assert\Assert;
use NilPortugues\MessageBus\EventBus\Contracts\Event;
use NilPortugues\MessageBus\EventBus\Contracts\EventBusMiddleware as EventBusMiddlewareInterface;

/**
 * Class EventBus.
 */
class EventBus implements EventBusMiddlewareInterface
{
    /** @var EventBusMiddleware[] */
    protected $middleware;

    /**
     * StackedEventBus constructor.
     *
     * @param array $middleware
     */
    public function __construct(array $middleware = [])
    {
        foreach ($middleware as $eventBusMiddleware) {
            Assert::isInstanceOf($eventBusMiddleware, EventBusMiddlewareInterface::class);
        }

        $this->middleware = $middleware;
    }

    /**
     * @param Event         $event
     * @param callable|null $next
     */
    public function __invoke(Event $event, callable $next = null)
    {
        $middleware = $this->middleware;
        $current = array_shift($middleware);

        if (empty($middleware)) {
            $current->__invoke($event);
            return;
        }
        
        foreach ($middleware as $eventBusMiddleware) {
            $callable = function ($event) use ($eventBusMiddleware) {
                return $eventBusMiddleware($event);
            };

            $current->__invoke($event, $callable);
            $current = $eventBusMiddleware;
        }
        unset($middleware);
    }
}
