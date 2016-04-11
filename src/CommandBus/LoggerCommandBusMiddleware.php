<?php

namespace NilPortugues\MessageBus\CommandBus;

use Exception;
use NilPortugues\MessageBus\CommandBus\Contracts\Command;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandBusMiddleware as CommandBusMiddlewareInterface;
use Psr\Log\LoggerInterface;

class LoggerCommandBusMiddleware implements CommandBusMiddlewareInterface
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * CachingCommandBus constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Command       $command
     * @param callable|null $next
     */
    public function __invoke(Command $command, callable $next = null)
    {
        try {
            if ($next) {
                $this->preCommandLog($command);
                $next($command);
                $this->postCommandLog($command);
            }
        } catch (Exception $e) {
            $this->logException($e);
        }
    }

    /**
     * @param Command $command
     */
    protected function preCommandLog(Command $command)
    {
        $this->logger->info(sprintf('Starting %s handling.', get_class($command)));
    }

    /**
     * @param Command $command
     */
    protected function postCommandLog(Command $command)
    {
        $this->logger->info(sprintf('%s was handled successfully.', get_class($command)));
    }

    /**
     * @param Exception $e
     */
    protected function logException(Exception $e)
    {
        $this->logger->alert(sprintf('[%s:%s] %s', $e->getFile(), $e->getLine(), $e->getMessage()));
    }
}
