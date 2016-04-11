<?php

namespace NilPortugues\MessageBus\CommandBus\Translator;

use InvalidArgumentException;
use NilPortugues\MessageBus\CommandBus\Contracts\Command;
use NilPortugues\MessageBus\CommandBus\Contracts\CommandTranslator;

class AppendStrategy implements CommandTranslator
{
    /** @var string */
    protected $append = '';

    /**
     * CommandTranslatorAppendStrategy constructor.
     *
     * @param string $append
     */
    public function __construct(string $append)
    {
        $append = trim($append);

        if (0 === strlen($append)) {
            throw new InvalidArgumentException('Append string cannot be empty or a white-space character');
        }

        $this->append = $append;
    }

    /**
     * Given a command, find the Command Handler's name.
     *
     * @param Command $command
     *
     * @return string
     */
    public function handlerName(Command $command) : string
    {
        return sprintf('%s%s', get_class($command), $this->append);
    }
}
