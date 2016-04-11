<?php

namespace NilPortugues\MessageBus\QueryBus\Translator;

use InvalidArgumentException;
use NilPortugues\MessageBus\QueryBus\Contracts\Query;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryTranslator;

class AppendStrategy implements QueryTranslator
{
    /** @var string */
    protected $append = '';

    /**
     * QueryTranslatorAppendStrategy constructor.
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
     * Given a query, find the Query Handler's name.
     *
     * @param Query $query
     *
     * @return string
     */
    public function handlerName(Query $query) : string
    {
        return sprintf('%s%s', get_class($query), $this->append);
    }
}
