<?php

namespace NilPortugues\MessageBus\CommandBus;

use NilPortugues\MessageBus\CommandBus\Contracts\Command;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandBusMiddleware as CommandBusMiddlewareInterface;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandHandler;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandHandlerResolver;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandTranslator;

class CommandBusMiddleware implements CommandBusMiddlewareInterface
{
    /** @var CommandTranslator */
    protected $commandTranslator;

    /** @var CommandHandlerResolver */
    protected $handlerResolver;

    /**
     * @param CommandTranslator      $commandTranslator
     * @param CommandHandlerResolver $handlerResolver
     */
    public function __construct(CommandTranslator $commandTranslator, CommandHandlerResolver $handlerResolver)
    {
        $this->commandTranslator = $commandTranslator;
        $this->handlerResolver = $handlerResolver;
    }

    /**
     * @param Command       $command
     * @param callable|null $next
     */
    public function __invoke(Command $command, callable $next = null)
    {
        $handlerName = $this->commandTranslator->handlerName($command);

        /** @var $handlerInstance CommandHandler */
        $handlerInstance = $this->handlerResolver->instantiate($handlerName);
        $handlerInstance($command);

        if ($next) {
            $next($command);
        }
    }
}
