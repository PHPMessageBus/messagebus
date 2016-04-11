<?php

namespace NilPortugues\MessageBus\CommandBus\Contracts;

/**
 * Interface CommandHandlerResolver.
 */
interface CommandHandlerResolver
{
    /**
     * Given a string to identify the Command Handler, return the instance.
     *
     * @param string $handler
     *
     * @return CommandHandler
     */
    public function instantiate(string $handler) : CommandHandler;
}
