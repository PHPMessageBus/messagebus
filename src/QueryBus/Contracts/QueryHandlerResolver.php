<?php

namespace NilPortugues\MessageBus\QueryBus\Contracts;

/**
 * Interface QueryHandlerResolver.
 */
interface QueryHandlerResolver
{
    /**
     * Given a string to identify the Query Handler, return the instance.
     *
     * @param string $handler
     *
     * @return QueryHandler
     */
    public function instantiate(string $handler) : QueryHandler;
}
