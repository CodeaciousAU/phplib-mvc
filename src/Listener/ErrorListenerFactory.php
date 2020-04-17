<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Listener;

use Interop\Container\ContainerInterface;
use Laminas\Log\Logger;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ErrorListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $logger Logger */
        $logger = $container->get('Codeacious\Mvc\DefaultLogger');

        return new ErrorListener($logger);
    }
}