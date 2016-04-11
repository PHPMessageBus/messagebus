<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 23/03/16
 * Time: 22:45.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\MessageBus;

use Interop\Container\ContainerInterface;

/**
 * Class InMemoryInteropContainer.
 */
class InMemoryInteropContainer implements ContainerInterface
{
    /** @var array */
    protected $container = [];

    /**
     * InMemoryInteropContainer constructor.
     *
     * @param array $container
     *
     * @throws ContainerException
     */
    public function __construct(array $container)
    {
        foreach ($container as $item) {
            if (false === ($item instanceof \Closure)) {
                throw new ContainerException('Container values must be instance of \Closure.');
            }
        }

        $this->container = $container;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundException  No entry was found for this identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (false === $this->has($id)) {
            throw new NotFoundException('Identifier not found in container');
        }

        return $this->container[$id]();
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this->container[$id]);
    }
}
