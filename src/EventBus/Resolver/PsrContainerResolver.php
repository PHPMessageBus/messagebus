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

use Psr\Container\ContainerInterface;

/**
 * Class PsrContainerResolver.
 */
class PsrContainerResolver extends InteropContainerResolver
{
    /**
     * PsrContainerResolver constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
