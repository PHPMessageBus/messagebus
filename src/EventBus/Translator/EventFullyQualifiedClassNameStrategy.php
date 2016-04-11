<?php

namespace NilPortugues\MessageBus\EventBus\Translator;

use NilPortugues\MessageBus\EventBus\Contracts\Event;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandler;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandlerPriority;
use NilPortugues\MessageBus\EventBus\Contracts\EventTranslator;
use RuntimeException;

class EventFullyQualifiedClassNameStrategy implements EventTranslator
{
    /**
     * @var array
     */
    protected $listeners = [];

    /**
     * An array of strings representing the FQN of the Event Handler.
     *
     * @param string[] $handlers
     */
    public function __construct(array $handlers)
    {
        foreach ($handlers as $handler) {
            if (false === class_exists($handler, true)) {
                throw new RuntimeException(
                    sprintf('Class %s does not exist.', $handler)
                );
            }

            if (false === in_array(EventHandler::class, class_implements($handler, true))) {
                throw new RuntimeException(
                    sprintf('Class %s does not implement the %s interface.', $handler, EventHandler::class)
                );
            }

            $priority = EventHandlerPriority::LOW_PRIORITY;
            if (false !== in_array(EventHandlerPriority::class, class_implements($handler, true))) {
                /* @var EventHandlerPriority $handler */
                $priority = $handler::priority();
            }

            /* @var EventHandler $handler */
            $this->listeners[$handler::subscribedTo()][$priority][] = $handler;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handlerName(Event $event) : array
    {
        $eventClass = get_class($event);

        if (empty($this->listeners[$eventClass])) {
            throw new RuntimeException(sprintf('Event %s has no EventHandler defined.', $eventClass));
        }

        ksort($this->listeners[$eventClass], SORT_NUMERIC);

        return $this->listeners[$eventClass];
    }
}
