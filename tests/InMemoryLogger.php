<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 24/03/16
 * Time: 0:14.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace NilPortugues\Tests\MessageBus;

use Psr\Log\AbstractLogger;

/**
 * Class InMemoryLogger.
 */
class InMemoryLogger extends AbstractLogger
{
    /** @var array */
    protected $logs = [];

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = array())
    {
        $this->logs[$level][] = $message;
    }

    /**
     * @return array
     */
    public function logs()
    {
        return $this->logs;
    }
}
