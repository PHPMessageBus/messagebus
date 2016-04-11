<?php

namespace NilPortugues\Tests\MessageBus\EventBus;

use NilPortugues\MessageBus\EventBus\Contracts\EventHandlerResolver;
use NilPortugues\MessageBus\EventBus\Contracts\EventTranslator;
use NilPortugues\MessageBus\EventBus\EventBus;
use NilPortugues\MessageBus\EventBus\EventBusMiddleware;
use NilPortugues\MessageBus\EventBus\LoggerEventBusMiddleware;
use NilPortugues\MessageBus\EventBus\Resolver\SimpleArrayResolver;
use NilPortugues\MessageBus\EventBus\TransactionalEventBusMiddleware;
use NilPortugues\MessageBus\EventBus\Translator\EventFullyQualifiedClassNameStrategy;
use NilPortugues\Tests\MessageBus\InMemoryLogger;
use PDO;

class EventBusTest extends \PHPUnit_Framework_TestCase
{
    /** @var array */
    protected $handlers;

    /** @var EventTranslator */
    protected $translator;

    /** @var EventHandlerResolver */
    protected $resolver;

    /** @var PDO */
    protected $pdo;

    public function setUp()
    {
        $this->handlers = $handlers = [
            DummyEventHandler::class => function () {
                return new DummyEventHandler();
            },
        ];

        $this->translator = new EventFullyQualifiedClassNameStrategy([
            DummyEventHandler::class,
        ]);

        $this->resolver = new SimpleArrayResolver($handlers);

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS users (id INTEGER, name TEXT);');
    }

    public function testItCanStackMiddleware()
    {
        $logger = new InMemoryLogger();

        $middleware = [
            new TransactionalEventBusMiddleware($this->pdo),
            new LoggerEventBusMiddleware($logger),
            new EventBusMiddleware($this->translator, $this->resolver),
        ];

        $eventBus = new EventBus($middleware);
        $eventBus->__invoke(new SqliteEvent($this->pdo));
        $this->assertNotEmpty($logger->logs());
    }
}
