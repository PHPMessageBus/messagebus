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
use PDO;

/**
 * Class SqliteEvent.
 */
class SqliteEvent implements Event
{
    /** @var PDO */
    protected $pdo;

    /**
     * SqliteEvent constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     *
     */
    public function __invoke()
    {
        $this->pdo->exec('INSERT INTO users(id, name) VALUES(1, "Username")');
    }

    /**
     * Returns a unique id.
     *
     * @return string
     */
    public function id() : string
    {
        return (string) uniqid();
    }
}
