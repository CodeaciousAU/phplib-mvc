<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc;

use Codeacious\Stdlib\ArrayTool;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CsrfPreventionServiceFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = ArrayTool::getArrayAtPath($container->get('config'),
            'Codeacious\Mvc:csrf_cookie');
        return new CsrfPreventionService($config);
    }
}