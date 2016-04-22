<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 23/03/16
 * Time: 23:24.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Tests\MessageBus;

use InvalidArgumentException;

/**
 * Class ContainerException.
 */
class ContainerException extends InvalidArgumentException implements \Interop\Container\Exception\ContainerException
{
}
