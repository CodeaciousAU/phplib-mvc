<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Controller\Plugin;

use Laminas\Log\Logger;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class Log extends AbstractPlugin
{
    /**
     * @var Logger
     */
    private $logger;


    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $message
     * @param int $priority One of the \Laminas\Log\Logger constants
     * @param array $extra
     * @return void
     */
    public function __invoke(string $message, int $priority, array $extra=[])
    {
        $this->logger->log($priority, $message, $extra);
    }
}