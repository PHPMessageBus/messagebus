<?php

namespace NilPortugues\MessageBus\QueryBus\Contracts;

/**
 * Interface QueryTranslator.
 */
interface QueryTranslator
{
    /**
     * Given a query, find the Query Handler's name.
     *
     * @param Query $query
     *
     * @return string
     */
    public function handlerName(Query $query) : string;
}
