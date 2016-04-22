<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 29/03/16
 * Time: 23:03.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Tests\MessageBus\EventBus;

use NilPortugues\MessageBus\EventBus\EventBusMiddleware;
use NilPortugues\MessageBus\EventBus\Resolver\SimpleArrayResolver;
use NilPortugues\MessageBus\EventBus\Translator\EventFullyQualifiedClassNameStrategy;

class EventBusMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @var EventBusMiddleware */
    protected $eventBus;

    public function setUp()
    {
        $handlers = [
            DummyEventHandler::class => function () {
                return new DummyEventHandler();
            },
        ];

        $this->eventBus = new EventBusMiddleware(
            new EventFullyQualifiedClassNameStrategy([DummyEventHandler::class]),
            new SimpleArrayResolver($handlers)
        );
    }

    public function testItCanHandle()
    {
        $this->eventBus->__invoke(new DummyEvent(), function () {
            return;
        });
        $this->assertTrue(true);
    }
}
