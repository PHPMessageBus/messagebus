<?php

namespace NilPortugues\Tests\MessageBus\QueryBus\Translator;

use InvalidArgumentException;
use NilPortugues\MessageBus\QueryBus\Translator\AppendStrategy;
use NilPortugues\Tests\MessageBus\QueryBus\DummyQuery;

class AppendStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanAppendToQuery()
    {
        $strategy = new AppendStrategy('Handler');
        $query = new DummyQuery();

        $this->assertEquals(
            DummyQuery::class.'Handler',
            $strategy->handlerName($query)
        );
    }

    public function testItWillThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        new AppendStrategy('');
    }
}
