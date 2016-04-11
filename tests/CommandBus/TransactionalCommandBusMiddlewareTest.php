<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 29/03/16
 * Time: 21:56.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\MessageBus\CommandBus;

use NilPortugues\MessageBus\CommandBus\Contracts\Command;
use NilPortugues\MessageBus\CommandBus\TransactionalCommandBusMiddleware;
use PDO;
use PDOException;

class TransactionalCommandBusMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @var PDO */
    protected $pdo;
    /** @var Command */
    protected $command;

    public function setUp()
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER, name TEXT);');
        $this->command = new SqliteCommand($this->pdo);
    }

    public function testItCanRunTransaction()
    {
        $middleware = new TransactionalCommandBusMiddleware($this->pdo);
        $middleware->__invoke($this->command, function ($command) {
            return new DummyCommandHandler($command);
        });
        $this->assertTrue(true);
    }

    public function testItCanCatchTransactionalError()
    {
        $middleware = new TransactionalCommandBusMiddleware($this->pdo);

        $this->expectException(PDOException::class);
        $middleware->__invoke($this->command, function () {
            throw new PDOException('Sometimes things fail');
        });
    }
}
