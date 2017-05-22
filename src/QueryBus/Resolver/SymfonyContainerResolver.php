<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 23/03/16
 * Time: 22:11.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\MessageBus\QueryBus\Resolver;

use Symfony\Component\DependencyInjection\ContainerInterface;
use InvalidArgumentException;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryHandler;
use NilPortugues\MessageBus\QueryBus\Contracts\QueryHandlerResolver;

/**
 * Class SymfonyContainerResolver.
 */
class SymfonyContainerResolver implements QueryHandlerResolver
{
    /** @var ContainerInterface */
    protected $container;

    /**
     * SymfonyContainerResolver constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Given a string to identify the Query Handler, return the instance.
     *
     * @param string $handler
     *
     * @return QueryHandler
     */
    public function instantiate(string $handler) : QueryHandler
    {
        $handler = ltrim($handler, '\\');
        if (false === $this->container->has($handler)) {
            throw new InvalidArgumentException(
                sprintf('Handler %s could not be found. Did you register it?', $handler)
            );
        }

        return $this->container->get($handler);
    }
}
