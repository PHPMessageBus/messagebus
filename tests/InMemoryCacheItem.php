<?php

namespace NilPortugues\Tests\MessageBus;

use Psr\Cache\CacheItemInterface;

class InMemoryCacheItem implements CacheItemInterface
{
    /**
     * @var string
     */
    protected $key;
    /**
     * @var mixed
     */
    protected $value;
    /**
     * @var bool
     */
    protected $hit;
    /**
     * @var \DateTime
     */
    protected $expiration;

    /**
     * Constructs a new MemoryCacheItem.
     *
     * @param string $key
     *                     The key of the cache item this object represents
     * @param array  $data
     *                     An associative array of data from the Memory Pool
     */
    public function __construct($key, array $data)
    {
        $this->key = $key;
        $this->value = $data['value'];
        $this->expiration = $data['ttd'];
        $this->hit = $data['hit'];
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->isHit() ? $this->value : null;
    }

    /**
     * {@inheritdoc}
     */
    public function isHit()
    {
        return $this->hit;
    }

    /**
     * {@inheritdoc}
     */
    public function set($value = null)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAt($expiration)
    {
        if (is_null($expiration)) {
            $this->expiration = new \DateTime('now +1 year');
        } else {
            assert('$expiration instanceof \DateTimeInterface');
            $this->expiration = $expiration;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function expiresAfter($time)
    {
        if (is_null($time)) {
            $this->expiration = new \DateTime('now +1 year');
        } elseif (is_numeric($time)) {
            $this->expiration = new \DateTime('now +'.$time.' seconds');
        } else {
            assert('$time instanceof DateInterval');
            $expiration = new \DateTime();
            $expiration->add($time);
            $this->expiration = $expiration;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiration()
    {
        return $this->expiration ?: new \DateTime('now +1 year');
    }

    /**
     * {@inheritdoc}
     */
    public function getRawValue()
    {
        return $this->value;
    }
}
