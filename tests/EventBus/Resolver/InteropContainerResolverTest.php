<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 23/03/16
 * Time: 22:43.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\MessageBus\EventBus\Resolver;

use InvalidArgumentException;
use NilPortugues\MessageBus\EventBus\Resolver\InteropContainerResolver;
use NilPortugues\Tests\MessageBus\EventBus\DummyEventHandler;
use NilPortugues\Tests\MessageBus\InMemoryInteropContainer;

class InteropContainerResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanResolve()
    {
        $handlers = [
            'NilPortugues\Tests\MessageBus\EventBus\DummyEventHandler' => function () {
                return new DummyEventHandler();
            },
        ];

        $resolver = new InteropContainerResolver(new InMemoryInteropContainer($handlers));
        $instance = $resolver->instantiate(
            'NilPortugues\Tests\MessageBus\EventBus\DummyEventHandler'
        );

        $this->assertInstanceOf(DummyEventHandler::class, $instance);
    }

    public function testItThrowsExceptionIfCannotResolve()
    {
        $this->expectException(InvalidArgumentException::class);
        $resolver = new InteropContainerResolver(new InMemoryInteropContainer([]));
        $resolver->instantiate('Hello\World');
    }
}
