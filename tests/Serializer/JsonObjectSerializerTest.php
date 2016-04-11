<?php

namespace NilPortugues\Tests\MessageBus\Serializer;

use NilPortugues\MessageBus\Serializer\JsonObjectSerializer;

class JsonObjectSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanSerialize()
    {
        $stdClass = new \stdClass();
        $stdClass->id = 1;
        $stdClass->name = ['name' => 'Nil', 'surname' => 'Portugués'];

        $serializer = new JsonObjectSerializer();

        $unserialized = $serializer->unserialize($serializer->serialize($stdClass));

        $this->assertEquals($stdClass->id, $unserialized->id);
        $this->assertEquals($stdClass->name['name'], $unserialized->name->name);
        $this->assertEquals($stdClass->name['surname'], $unserialized->name->surname);
    }
}
