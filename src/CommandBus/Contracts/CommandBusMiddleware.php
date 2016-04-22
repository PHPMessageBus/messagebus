<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 29/02/16
 * Time: 23:55.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\MessageBus\CommandBus\Contracts;

/**
 * Interface CommandBusMiddleware.
 */
interface CommandBusMiddleware
{
    /**
     * @param Command  $command
     * @param callable $next
     */
    public function __invoke(Command $command, callable $next = null);
}
