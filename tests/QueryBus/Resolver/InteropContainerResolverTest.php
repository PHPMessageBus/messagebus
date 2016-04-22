<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 23/03/16
 * Time: 22:43.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Tests\MessageBus\QueryBus\Resolver;

use InvalidArgumentException;
use NilPortugues\MessageBus\QueryBus\Resolver\InteropContainerResolver;
use NilPortugues\Tests\MessageBus\InMemoryInteropContainer;
use NilPortugues\Tests\MessageBus\QueryBus\DummyQueryHandler;

class InteropContainerResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanResolve()
    {
        $handlers = [
            'NilPortugues\Tests\MessageBus\QueryBus\DummyQueryHandler' => function () {
                return new DummyQueryHandler();
            },
        ];

        $resolver = new InteropContainerResolver(new InMemoryInteropContainer($handlers));
        $instance = $resolver->instantiate(
            'NilPortugues\Tests\MessageBus\QueryBus\DummyQueryHandler'
        );

        $this->assertInstanceOf(DummyQueryHandler::class, $instance);
    }

    public function testItThrowsExceptionIfCannotResolve()
    {
        $this->expectException(InvalidArgumentException::class);
        $resolver = new InteropContainerResolver(new InMemoryInteropContainer([]));
        $resolver->instantiate('Hello\World');
    }
}
