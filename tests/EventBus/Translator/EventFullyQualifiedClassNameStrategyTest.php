<?php

namespace NilPortugues\Tests\MessageBus\EventBus\Translator;

use NilPortugues\MessageBus\EventBus\Translator\EventFullyQualifiedClassNameStrategy;
use NilPortugues\Tests\MessageBus\EventBus\DummyEvent;
use NilPortugues\Tests\MessageBus\EventBus\DummyEventHandler;
use RuntimeException;

class EventFullyQualifiedClassNameStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanResolveHandler()
    {
        $strategy = new EventFullyQualifiedClassNameStrategy([
            DummyEventHandler::class,
        ]);

        $event = new DummyEvent();
        $handlers = $strategy->handlerName($event);

        $this->assertContains(DummyEvent::class.'Handler', reset($handlers));
    }

    public function testItWillThrowExceptionIfClassDoesNotExist()
    {
        $this->expectException(RuntimeException::class);
        new EventFullyQualifiedClassNameStrategy(['RandomHandler']);
    }

    public function testItWillThrowExceptionIfClassDoesNotImplementEventHandler()
    {
        $this->expectException(RuntimeException::class);
        new EventFullyQualifiedClassNameStrategy(['DateTime']);
    }

    public function testItWillThrowExceptionIfEventNotSupported()
    {
        $strategy = new EventFullyQualifiedClassNameStrategy([]);
        $event = new DummyEvent();

        $this->expectException(RuntimeException::class);
        $strategy->handlerName($event);
    }
}
