<?php

namespace NilPortugues\MessageBus\CommandBus\Contracts;

/**
 * Interface CommandTranslator.
 */
interface CommandTranslator
{
    /**
     * Given a command, find the Command Handler's name.
     *
     * @param Command $command
     *
     * @return string
     */
    public function handlerName(Command $command) : string;
}
