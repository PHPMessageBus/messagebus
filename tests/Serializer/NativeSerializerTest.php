<?php

namespace NilPortugues\Tests\MessageBus\Serializer;

use NilPortugues\MessageBus\Serializer\NativeSerializer;

class NativeSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanSerialize()
    {
        $stdClass = new \stdClass();
        $serializer = new NativeSerializer();

        $this->assertEquals(
            $serializer->unserialize($serializer->serialize($stdClass)),
            $stdClass
        );
    }
}
