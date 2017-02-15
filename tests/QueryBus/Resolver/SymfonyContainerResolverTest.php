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
use NilPortugues\MessageBus\QueryBus\Resolver\SymfonyContainerResolver;
use NilPortugues\Tests\MessageBus\InMemorySymfonyContainer;
use NilPortugues\Tests\MessageBus\QueryBus\DummyQueryHandler;

class SymfonyContainerResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanResolve()
    {
        $handlers = [
            'NilPortugues\Tests\MessageBus\QueryBus\DummyQueryHandler' => function () {
                return new DummyQueryHandler();
            },
        ];

        $resolver = new SymfonyContainerResolver(new InMemorySymfonyContainer($handlers));
        $instance = $resolver->instantiate(
            'NilPortugues\Tests\MessageBus\QueryBus\DummyQueryHandler'
        );

        $this->assertInstanceOf(DummyQueryHandler::class, $instance);
    }

    public function testItThrowsExceptionIfCannotResolve()
    {
        $this->expectException(InvalidArgumentException::class);
        $resolver = new SymfonyContainerResolver(new InMemorySymfonyContainer([]));
        $resolver->instantiate('Hello\World');
    }
}
