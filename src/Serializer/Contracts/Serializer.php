<?php

namespace NilPortugues\MessageBus\Serializer\Contracts;

interface Serializer
{
    /**
     * Turns a data structure into a string that can be recovered.
     *
     * @param mixed $data
     *
     * @return string
     */
    public function serialize($data) : string;

    /**
     * Turns string data structure into an usable $data structure.
     *
     * @param string $data
     *
     * @return mixed
     */
    public function unserialize(string $data);
}
