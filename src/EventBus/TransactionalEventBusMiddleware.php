<?php

namespace NilPortugues\MessageBus\EventBus;

use NilPortugues\MessageBus\EventBus\Contracts\Event;
use NilPortugues\MessageBus\EventBus\Contracts\EventBusMiddleware as EventBusMiddlewareInterface;
use PDO;

/**
 * Class TransactionalEventBusMiddleware.
 */
class TransactionalEventBusMiddleware implements EventBusMiddlewareInterface
{
    /** @var PDO */
    protected $pdo;

    /**
     * TransactionalEventBusMiddleware constructor.
     *
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param Event         $event
     * @param callable|null $next
     */
    public function __invoke(Event $event, callable $next = null)
    {
        try {
            $this->pdo->beginTransaction();
            $next($event);
            $this->pdo->commit();
        } catch (\PDOException $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }
}
