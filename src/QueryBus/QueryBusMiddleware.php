<?php

namespace NilPortugues\MessageBus\QueryBus;

use NilPortugues\MessageBus\QueryBus\Contracts\Query;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryBusMiddleware as QueryBusMiddlewareInterface;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryHandler;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryHandlerResolver;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryResponse;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryTranslator;

class QueryBusMiddleware implements QueryBusMiddlewareInterface
{
    /** @var QueryTranslator */
    protected $queryTranslator;

    /** @var QueryHandlerResolver */
    protected $handlerResolver;

    /**
     * @param QueryTranslator      $queryTranslator
     * @param QueryHandlerResolver $handlerResolver
     */
    public function __construct(QueryTranslator $queryTranslator, QueryHandlerResolver $handlerResolver)
    {
        $this->queryTranslator = $queryTranslator;
        $this->handlerResolver = $handlerResolver;
    }

    /**
     * @param Query         $query
     * @param callable|null $next
     *
     * @return QueryResponse
     */
    public function __invoke(Query $query, callable $next = null) : QueryResponse
    {
        $handlerName = $this->queryTranslator->handlerName($query);

        /** @var $handlerInstance QueryHandler */
        $handlerInstance = $this->handlerResolver->instantiate($handlerName);

        return $handlerInstance($query);
    }
}
