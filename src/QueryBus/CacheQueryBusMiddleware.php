<?php

namespace NilPortugues\MessageBus\QueryBus;

use InvalidArgumentException;
use NilPortugues\MessageBus\QueryBus\Contracts\Query;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryBusMiddleware as QueryBusMiddlewareInterface;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryResponse;
use NilPortugues\MessageBus\Serializer\Contracts\Serializer;
use Psr\Cache\CacheItemPoolInterface;

class CacheQueryBusMiddleware implements QueryBusMiddlewareInterface
{
    /** @var CacheItemPoolInterface */
    protected $cache;

    /** @var Serializer */
    protected $serializer;

    /** @var int */
    protected $ttl;

    /**
     * CachingQueryBus constructor.
     *
     * @param Serializer             $serializer
     * @param CacheItemPoolInterface $cacheItemPool
     * @param int                    $expirationInSeconds
     */
    public function __construct(
        Serializer $serializer,
        CacheItemPoolInterface $cacheItemPool,
        int $expirationInSeconds
    ) {
        $this->cache = $cacheItemPool;
        $this->serializer = $serializer;
        $this->ttl = $expirationInSeconds;
    }

    /**
     * @param Query         $query
     * @param callable|null $next
     *
     * @return QueryResponse
     */
    public function __invoke(Query $query, callable $next = null) : QueryResponse
    {
        if (null === $next) {
            throw new InvalidArgumentException('callable $next must not be null.');
        }

        $cache = $this->cache->getItem($this->queryHashing($query));

        if ($cache->isHit()) {
            return $this->serializer->unserialize($cache->get());
        }

        $response = $next($query);

        $cache->set($this->serializer->serialize($response));
        $cache->expiresAfter($this->ttl);
        $this->cache->save($cache);

        return $response;
    }

    /**
     * @param Query $query
     *
     * @return string
     */
    protected function queryHashing(Query $query) : string
    {
        return md5(serialize($query));
    }
}
