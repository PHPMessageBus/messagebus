<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 29/02/16
 * Time: 23:55.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\MessageBus\QueryBus\Contracts;

/**
 * Interface QueryBusMiddleware.
 */
interface QueryBusMiddleware
{
    /**
     * @param Query         $query
     * @param callable|null $next
     *
     * @return QueryResponse
     */
    public function __invoke(Query $query, callable $next = null) : QueryResponse;
}
