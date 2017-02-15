<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 23/03/16
 * Time: 22:43.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\MessageBus\CommandBus\Resolver;

use InvalidArgumentException;
use NilPortugues\MessageBus\CommandBus\Resolver\InteropContainerResolver;
use NilPortugues\Tests\MessageBus\CommandBus\DummyCommandHandler;
use NilPortugues\Tests\MessageBus\InMemoryInteropContainer;

class InteropContainerResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanResolve()
    {
        $handlers = [
            'NilPortugues\Tests\MessageBus\CommandBus\DummyCommandHandler' => function () {
                return new DummyCommandHandler();
            },
        ];

        $resolver = new InteropContainerResolver(new InMemoryInteropContainer($handlers));
        $instance = $resolver->instantiate(
            'NilPortugues\Tests\MessageBus\CommandBus\DummyCommandHandler'
        );

        $this->assertInstanceOf(DummyCommandHandler::class, $instance);
    }

    public function testItThrowsExceptionIfCannotResolve()
    {
        $this->expectException(InvalidArgumentException::class);
        $resolver = new InteropContainerResolver(new InMemoryInteropContainer([]));
        $resolver->instantiate('Hello\World');
    }
}
