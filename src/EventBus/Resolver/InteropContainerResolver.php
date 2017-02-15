<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 23/03/16
 * Time: 22:11.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\MessageBus\EventBus\Resolver;

use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandler;
use NilPortugues\MessageBus\EventBus\Contracts\EventHandlerResolver;

/**
 * Class InteropContainerResolver.
 */
class InteropContainerResolver implements EventHandlerResolver
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * InteropContainerResolver constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Given a string to identify the Event Handler, return the instance.
     *
     * @param string $handler
     *
     * @return EventHandler
     */
    public function instantiate(string $handler) : EventHandler
    {
        if (false === $this->container->has($handler)) {
            throw new InvalidArgumentException(
                sprintf('Handler %s could not be found. Did you register it?', $handler)
            );
        }

        return $this->container->get($handler);
    }
}
