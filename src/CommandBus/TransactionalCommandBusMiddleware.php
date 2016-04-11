<?php

namespace NilPortugues\MessageBus\CommandBus;

use NilPortugues\MessageBus\CommandBus\Contracts\Command;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandBusMiddleware as CommandBusMiddlewareInterface;
use PDO;

class TransactionalCommandBusMiddleware implements CommandBusMiddlewareInterface
{
    /** @var PDO */
    protected $pdo;

    /**
     * TransactionalCommandBusMiddleware constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param Command       $command
     * @param callable|null $next
     */
    public function __invoke(Command $command, callable $next = null)
    {
        try {
            $this->pdo->beginTransaction();
            $next($command);
            $this->pdo->commit();
        } catch (\PDOException $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }
}
