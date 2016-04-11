<?php

namespace NilPortugues\Tests\MessageBus;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class InMemoryCache implements CacheItemPoolInterface
{
    /**
     * The stored data in this cache pool.
     *
     * @var array
     */
    protected $data = [];
    /**
     * Deferred cache items to be saved later.
     *
     * @var CacheItemInterface[]
     */
    protected $deferred = [];

    /**
     * {@inheritdoc}
     */
    public function deleteItem($key)
    {
        return $this->deleteItems([$key]);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            unset($this->data[$key]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(CacheItemInterface $item)
    {
        return $this->write([$item]);
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $items)
    {
        /** @var \Psr\Cache\CacheItemInterface $item */
        foreach ($items as $item) {
            $this->data[$item->getKey()] = [
                // Assumes use of the BasicCacheItemAccessorsTrait.
                'value' => $item->getRawValue(),
                'ttd' => $item->getExpiration(),
                'hit' => true,
            ];
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferred[] = $item;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        $success = $this->write($this->deferred);
        if ($success) {
            $this->deferred = [];
        }

        return $success;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(array $keys = [])
    {
        // This method will throw an appropriate exception if any key is not valid.
        array_map([$this, 'validateKey'], $keys);
        $collection = [];
        foreach ($keys as $key) {
            $collection[$key] = $this->getItem($key);
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        // This method will either return True or throw an appropriate exception.
        $this->validateKey($key);
        if (!$this->hasItem($key)) {
            $this->data[$key] = $this->emptyItem();
        }

        return new InMemoryCacheItem($key, $this->data[$key]);
    }

    /**
     * {@inheritdoc}
     */
    protected function validateKey($key)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem($key)
    {
        return array_key_exists($key, $this->data) && $this->data[$key]['ttd'] > new \DateTime();
    }

    /**
     * Returns an empty item definition.
     *
     * @return array
     */
    protected function emptyItem()
    {
        return [
            'value' => null,
            'hit' => false,
            'ttd' => null,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->data = [];

        return true;
    }
}
