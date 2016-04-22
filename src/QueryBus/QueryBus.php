<?php

namespace NilPortugues\MessageBus\QueryBus;

use NilPortugues\Assert\Assert;
use NilPortugues\MessageBus\QueryBus\Contracts\Query;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryBusMiddleware as QueryBusMiddlewareInterface;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryResponse;

class QueryBus implements QueryBusMiddlewareInterface
{
    /** @var QueryBusMiddleware[] */
    protected $middleware;

    /**
     * StackedQueryBus constructor.
     *
     * @param array $middleware
     */
    public function __construct(array $middleware = [])
    {
        foreach ($middleware as $queryBusMiddleware) {
            Assert::isInstanceOf($queryBusMiddleware, QueryBusMiddlewareInterface::class);
        }

        $this->middleware = $middleware;
    }

    /**
     * @param Query         $query
     * @param callable|null $next
     *
     * @return QueryResponse
     */
    public function __invoke(Query $query, callable $next = null) : QueryResponse
    {
        $middleware = $this->middleware;
        $current = array_shift($middleware);

        if (!empty($middleware)) {
            foreach ($middleware as $queryBusMiddleware) {
                $callable = function ($query) use ($queryBusMiddleware) {
                    return $queryBusMiddleware->__invoke($query);
                };

                $response = $current->__invoke($query, $callable);
                $current = $queryBusMiddleware;
            }
        } elseif ($current) {
            $response = $current->__invoke($query);
        }

        return (!empty($response)) ? $response : EmptyResponse::create();
    }
}
