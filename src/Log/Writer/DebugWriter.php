<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Log\Writer;

use Laminas\Log\Writer\AbstractWriter;

/**
 * Writer which sends log messages to PHP's configured error_log destination.
 */
class DebugWriter extends AbstractWriter
{
    /**
     * Write a message to the log
     *
     * @param array $event log data event
     * @return void
     */
    protected function doWrite(array $event)
    {
        error_log($this->formatter->format($event));
    }
}