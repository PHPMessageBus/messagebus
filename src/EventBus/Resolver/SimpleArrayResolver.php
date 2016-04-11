<?php

namespace NilPortugues\MessageBus\EventBus\Resolver;

use InvalidArgumentException;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandler;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandlerResolver;

class SimpleArrayResolver implements EventHandlerResolver
{
    /** @var array */
    protected $handlers = [];

    /**
     * ArrayResolver constructor.
     *
     * @param array $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * Given a string to identify the Event Handler, return the instance.
     *
     * @param string $handler
     *
     * @return EventHandler
     *
     * @throws InvalidArgumentException
     */
    public function instantiate(string $handler) : EventHandler
    {
        if (false === isset($this->handlers[$handler])) {
            throw new InvalidArgumentException(
                sprintf('Handler %s could not be found. Did you register it?', $handler)
            );
        }

        $callable = $this->handlers[$handler];

        return ($callable instanceof \Closure) ? $callable() : new $callable();
    }
}
