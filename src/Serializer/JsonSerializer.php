<?php

namespace NilPortugues\MessageBus\Serializer;

use NilPortugues\MessageBus\Serializer\Contracts\Serializer;

class JsonSerializer implements Serializer
{
    /** @var \NilPortugues\Serializer\JsonSerializer */
    protected $serializer;

    /**
     * JsonSerializer constructor.
     */
    public function __construct()
    {
        $this->serializer = new \NilPortugues\Serializer\JsonSerializer();
    }

    /**
     * Turns a data structure into a string that can be recovered.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function serialize($data) : string
    {
        return $this->serializer->serialize($data);
    }

    /**
     * Turns string data structure into an usable $data structure.
     *
     * @param string $data
     *
     * @return mixed
     */
    public function unserialize(string $data)
    {
        return $this->serializer->unserialize($data);
    }
}
