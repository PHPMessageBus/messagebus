<?php

namespace NilPortugues\Tests\MessageBus\Serializer;

use NilPortugues\MessageBus\Serializer\JsonSerializer;

class JsonSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanSerialize()
    {
        $stdClass = new \stdClass();
        $serializer = new JsonSerializer();

        $this->assertEquals(
            $serializer->unserialize($serializer->serialize($stdClass)),
            $stdClass
        );
    }
}
