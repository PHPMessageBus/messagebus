<?php

namespace NilPortugues\MessageBus\CommandBus\Resolver;

use InvalidArgumentException;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandHandler;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandHandlerResolver;

class SimpleArrayResolver implements CommandHandlerResolver
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
     * Given a string to identify the Command Handler, return the instance.
     *
     * @param string $handler
     *
     * @return CommandHandler
     *
     * @throws InvalidArgumentException
     */
    public function instantiate(string $handler) : CommandHandler
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
