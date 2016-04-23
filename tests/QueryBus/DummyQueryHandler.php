<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 29/03/16
 * Time: 23:54.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Tests\MessageBus\QueryBus;

use NilPortugues\MessageBus\QueryBus\Contracts\Query;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryHandler;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryResponse;

/**
 * Class DummyQueryHandler.
 */
class DummyQueryHandler implements QueryHandler
{
    /**
     * @param Query $query
     *
     * @return QueryResponse
     */
    public function __invoke($query) : QueryResponse
    {
        return new DummyQueryResponse();
    }
}
