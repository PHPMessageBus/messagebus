<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 24/03/16
 * Time: 0:07.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\MessageBus\CommandBus;

use NilPortugues\MessageBus\CommandBus\CommandBus;
use NilPortugues\MessageBus\CommandBus\CommandBusMiddleware;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandHandlerResolver;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandTranslator;
use NilPortugues\MessageBus\CommandBus\LoggerCommandBusMiddleware;
use NilPortugues\MessageBus\CommandBus\Resolver\SimpleArrayResolver;
use NilPortugues\MessageBus\CommandBus\Translator\AppendStrategy;
use NilPortugues\Tests\MessageBus\InMemoryLogger;

class CommandBusTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    protected $handlers;

    /** @var CommandTranslator */
    protected $translator;

    /** @var CommandHandlerResolver */
    protected $resolver;

    public function setUp()
    {
        $this->handlers = $handlers = [
            DummyCommandHandler::class => function () {
                return new DummyCommandHandler();
            },
        ];

        $this->translator = new AppendStrategy('Handler');
        $this->resolver = new SimpleArrayResolver($handlers);
    }

    public function testItCanStackMiddleware()
    {
        $logger = new InMemoryLogger();

        $middleware = [
            new LoggerCommandBusMiddleware($logger),
            new CommandBusMiddleware($this->translator, $this->resolver),
        ];

        $commandBus = new CommandBus($middleware);
        $commandBus->__invoke(new DummyCommand());
        $this->assertNotEmpty($logger->logs());
    }
}
