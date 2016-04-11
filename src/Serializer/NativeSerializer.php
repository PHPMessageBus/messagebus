<?php

namespace NilPortugues\MessageBus\Serializer;

use NilPortugues\MessageBus\Serializer\Contracts\Serializer;

class NativeSerializer implements Serializer
{
    /**
     * Turns a data structure into a string that can be recovered.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function serialize($data) : string
    {
        return serialize($data);
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
        return unserialize($data);
    }
}
