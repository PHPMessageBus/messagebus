<?php

namespace NilPortugues\Tests\MessageBus\CommandBus\Translator;

use InvalidArgumentException;
use NilPortugues\MessageBus\CommandBus\Translator\AppendStrategy;
use NilPortugues\Tests\MessageBus\CommandBus\DummyCommand;

class AppendStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanAppendToCommand()
    {
        $strategy = new AppendStrategy('Handler');
        $command = new DummyCommand();

        $this->assertEquals(
            DummyCommand::class.'Handler',
            $strategy->handlerName($command)
        );
    }

    public function testItWillThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        new AppendStrategy('');
    }
}
