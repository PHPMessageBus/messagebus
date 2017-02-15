<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 29/03/16
 * Time: 23:03.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\MessageBus\QueryBus;

use NilPortugues\MessageBus\QueryBus\Contracts\QueryResponse;
use NilPortugues\MessageBus\QueryBus\QueryBusMiddleware;
use NilPortugues\MessageBus\QueryBus\Resolver\SimpleArrayResolver;
use NilPortugues\MessageBus\QueryBus\Translator\AppendStrategy;

class QueryBusMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @var QueryBusMiddleware */
    protected $queryBus;

    public function setUp()
    {
        $handlers = [
            DummyQueryHandler::class => function () {
                return new DummyQueryHandler();
            },
        ];

        $this->queryBus = new QueryBusMiddleware(new AppendStrategy('Handler'), new SimpleArrayResolver($handlers));
    }

    public function testItCanHandle()
    {
        $response = $this->queryBus->__invoke(new DummyQuery());
        $this->assertInstanceOf(QueryResponse::class, $response);
    }
}
