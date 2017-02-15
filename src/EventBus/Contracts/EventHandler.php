<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 17/03/16
 * Time: 22:01.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\MessageBus\EventBus\Contracts;

/**
 * Interface EventHandler.
 */
interface EventHandler
{
    /**
     * Returns the name of the event subscribed to.
     *
     * @return string
     */
    public static function subscribedTo() : string;
}
