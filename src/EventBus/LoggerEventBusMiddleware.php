<?php

namespace NilPortugues\MessageBus\EventBus;

use Exception;
use NilPortugues\MessageBus\EventBus\Contracts\Event;
use NilPortugues\MessageBus\EventBus\Contracts\EventBusMiddleware as EventBusMiddlewareInterface;
use Psr\Log\LoggerInterface;

/**
 * Class LoggerEventBusMiddleware.
 */
class LoggerEventBusMiddleware implements EventBusMiddlewareInterface
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * CachingEventBus constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Event         $event
     * @param callable|null $next
     */
    public function __invoke(Event $event, callable $next = null)
    {
        try {
            if ($next) {
                $this->preEventLog($event);
                $next($event);
                $this->postEventLog($event);
            }
        } catch (Exception $e) {
            $this->logException($e);
        }
    }

    /**
     * @param Event $event
     */
    protected function preEventLog(Event $event)
    {
        $this->logger->info(sprintf('Starting %s handling.', get_class($event)));
    }

    /**
     * @param Event $event
     */
    protected function postEventLog(Event $event)
    {
        $this->logger->info(sprintf('%s was handled successfully.', get_class($event)));
    }

    /**
     * @param Exception $e
     */
    protected function logException(Exception $e)
    {
        $this->logger->alert(sprintf('[%s:%s] %s', $e->getFile(), $e->getLine(), $e->getMessage()));
    }
}
