<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 23/03/16
 * Time: 23:23.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Tests\MessageBus;

use InvalidArgumentException;

/**
 * Class NotFoundException.
 */
class NotFoundException extends InvalidArgumentException implements \Interop\Container\Exception\NotFoundException
{
}
