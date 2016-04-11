<?php

namespace NilPortugues\MessageBus\QueryBus;

use Exception;
use NilPortugues\MessageBus\QueryBus\Contracts\Query;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryBusMiddleware as QueryBusMiddlewareInterface;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryResponse;
use Psr\Log\LoggerInterface;

class LoggerQueryBusMiddleware implements QueryBusMiddlewareInterface
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * LoggerQueryBus constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Query         $query
     * @param callable|null $next
     *
     * @return QueryResponse
     *
     * @throws Exception
     */
    public function __invoke(Query $query, callable $next = null) : QueryResponse
    {
        $response = EmptyResponse::create();

        try {
            if (null !== $next) {
                $this->preQueryLog($query);
                $response = $next($query);
                $this->postQueryLog($query);

                return $response;
            }
        } catch (Exception $e) {
            $this->logException($e);
        }

        return $response;
    }

    /**
     * @param Query $query
     */
    protected function preQueryLog(Query $query)
    {
        $this->logger->info(sprintf('Starting %s handling.', get_class($query)));
    }

    /**
     * @param Query $query
     */
    protected function postQueryLog(Query $query)
    {
        $this->logger->info(sprintf('%s was handled successfully.', get_class($query)));
    }

    /**
     * @param Exception $e
     */
    protected function logException(Exception $e)
    {
        $this->logger->alert(sprintf('[%s:%s] %s', $e->getFile(), $e->getLine(), $e->getMessage()));
    }
}
