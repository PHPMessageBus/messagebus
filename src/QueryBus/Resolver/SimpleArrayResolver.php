<?php

namespace NilPortugues\MessageBus\QueryBus\Resolver;

use InvalidArgumentException;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryHandler;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryHandlerResolver;

class SimpleArrayResolver implements QueryHandlerResolver
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
     * Given a string to identify the Query Handler, return the instance.
     *
     * @param string $handler
     *
     * @return QueryHandler
     *
     * @throws InvalidArgumentException
     */
    public function instantiate(string $handler) : QueryHandler
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
