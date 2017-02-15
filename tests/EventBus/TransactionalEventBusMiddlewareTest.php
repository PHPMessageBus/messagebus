<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 29/03/16
 * Time: 21:56.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\MessageBus\EventBus;

use NilPortugues\MessageBus\EventBus\Contracts\Event;
use NilPortugues\MessageBus\EventBus\TransactionalEventBusMiddleware;
use PDO;
use PDOException;

class TransactionalEventBusMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @var PDO */
    protected $pdo;
    /** @var Event */
    protected $event;

    public function setUp()
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER, name TEXT);');
        $this->event = new SqliteEvent($this->pdo);
    }

    public function testItCanRunTransaction()
    {
        $middleware = new TransactionalEventBusMiddleware($this->pdo);
        $middleware->__invoke($this->event, function ($command) {
            return new SqliteEventHandler($command);
        });
        $this->assertTrue(true);
    }

    public function testItCanCatchTransactionalError()
    {
        $middleware = new TransactionalEventBusMiddleware($this->pdo);

        $this->expectException(PDOException::class);
        $middleware->__invoke($this->event, function () {
            throw new PDOException('Sometimes things fail');
        });
    }
}
