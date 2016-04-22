<?php

namespace NilPortugues\MessageBus\CommandBus;

use NilPortugues\Assert\Assert;
use NilPortugues\MessageBus\CommandBus\Contracts\Command;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandBusMiddleware as CommandBusMiddlewareInterface;

class CommandBus implements CommandBusMiddlewareInterface
{
    /** @var callable[] */
    protected $middleware = [];

    /**
     * StackedCommandBus constructor.
     *
     * @param callable[] $middleware
     */
    public function __construct(array $middleware = [])
    {
        foreach ($middleware as $commandBusMiddleware) {
            Assert::isInstanceOf($commandBusMiddleware, CommandBusMiddlewareInterface::class);
        }

        $this->middleware = $middleware;
    }

    /**
     * @param Command       $command
     * @param callable|null $next
     */
    public function __invoke(Command $command, callable $next = null)
    {
        $middleware = $this->middleware;
        $current = array_shift($middleware);

        if (empty($middleware) && !empty($current)) {
            $current->__invoke($command);
            return;
        }

        foreach ($middleware as $commandBusMiddleware) {
            $callable = function ($command) use ($commandBusMiddleware) {
                return $commandBusMiddleware($command);
            };

            $current->__invoke($command, $callable);
            $current = $commandBusMiddleware;
        }
    }
}
