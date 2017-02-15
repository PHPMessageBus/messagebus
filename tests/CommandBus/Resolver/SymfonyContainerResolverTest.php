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
use NilPortugues\MessageBus\CommandBus\Resolver\SymfonyContainerResolver;
use NilPortugues\Tests\MessageBus\InMemorySymfonyContainer;
use NilPortugues\Tests\MessageBus\CommandBus\DummyCommandHandler;

class SymfonyContainerResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanResolve()
    {
        $handlers = [
            'NilPortugues\Tests\MessageBus\CommandBus\DummyQueryHandler' => function () {
                return new DummyCommandHandler();
            },
        ];

        $resolver = new SymfonyContainerResolver(new InMemorySymfonyContainer($handlers));
        $instance = $resolver->instantiate(
            'NilPortugues\Tests\MessageBus\CommandBus\DummyQueryHandler'
        );

        $this->assertInstanceOf(DummyCommandHandler::class, $instance);
    }

    public function testItThrowsExceptionIfCannotResolve()
    {
        $this->expectException(InvalidArgumentException::class);
        $resolver = new SymfonyContainerResolver(new InMemorySymfonyContainer([]));
        $resolver->instantiate('Hello\World');
    }
}
