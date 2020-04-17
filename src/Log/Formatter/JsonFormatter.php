<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Log\Formatter;

use Laminas\Log\Formatter\Base;

class JsonFormatter extends Base
{
    /**
     * Formats data to be written by the writer.
     *
     * @param array $event event data
     * @return string|array
     */
    public function format($event)
    {
        $event = parent::format($event);
        return @json_encode($event);
    }
}