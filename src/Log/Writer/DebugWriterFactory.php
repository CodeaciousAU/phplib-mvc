<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Log\Writer;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DebugWriterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $options = (isset($config['Codeacious\Mvc\Log']['debug_writer']))
            ? $config['Codeacious\Mvc\Log']['debug_writer'] : [];

        return new DebugWriter($options);
    }
}