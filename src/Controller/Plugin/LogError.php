<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Controller\Plugin;

use Laminas\Log\Logger;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

class LogError extends AbstractPlugin
{
    /**
     * @param string $message
     * @param int $priority One of the \Laminas\Log\Logger constants
     * @param \Throwable|null $exception
     * @return void
     */
    public function __invoke(string $message, int $priority = Logger::ERR, \Throwable $exception=null)
    {
        $controller = $this->getController();
        if ($controller instanceof AbstractController)
        {
            $controller->getEventManager()->trigger('error', $controller, [
                'priority' => $priority,
                'message' => $message,
                'exception' => $exception,
            ]);
        }
    }
}