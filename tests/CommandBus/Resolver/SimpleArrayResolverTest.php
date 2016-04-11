<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 23/03/16
 * Time: 22:28.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\MessageBus\CommandBus\Resolver;

use InvalidArgumentException;
use NilPortugues\Tests\MessageBus\CommandBus\DummyCommandHandler;

class SimpleArrayResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testItCanResolve()
    {
        $handlers = [
            'NilPortugues\Tests\MessageBus\CommandBus\DummyCommandHandler' => function () {
                return new DummyCommandHandler();
            },
        ];

        $resolver = new SimpleArrayResolver($handlers);
        $instance = $resolver->instantiate(
            'NilPortugues\Tests\MessageBus\CommandBus\DummyCommandHandler'
        );

        $this->assertInstanceOf(DummyCommandHandler::class, $instance);
    }

    public function testItThrowsExceptionIfCannotResolve()
    {
        $this->expectException(InvalidArgumentException::class);
        $resolver = new SimpleArrayResolver([]);
        $resolver->instantiate('Hello\World');
    }
}
