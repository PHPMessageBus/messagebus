<?php

namespace NilPortugues\MessageBus\Serializer;

use NilPortugues\MessageBus\Serializer\Contracts\Serializer;
use NilPortugues\Serializer\DeepCopySerializer;
use NilPortugues\Serializer\Transformer\JsonTransformer;

class JsonObjectSerializer implements Serializer
{
    /** @var JsonTransformer */
    protected $transformer;

    /**
     * JsonObjectSerializer constructor.
     */
    public function __construct()
    {
        $this->transformer = new DeepCopySerializer(new JsonTransformer());
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
        return $this->transformer->serialize($data);
    }

    /**
     * Turns string data structure into an usable $data structure.
     *
     * @param string $data
     *
     * @return \stdClass
     */
    public function unserialize(string $data)
    {
        return (object) json_decode($data);
    }
}
