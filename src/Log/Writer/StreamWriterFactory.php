<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Log\Writer;

use Interop\Container\ContainerInterface;
use Laminas\Log\Writer\Stream;
use Laminas\ServiceManager\Factory\FactoryInterface;

class StreamWriterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $options = (isset($config['Codeacious\Mvc\Log']['stream_writer']))
            ? $config['Codeacious\Mvc\Log']['stream_writer'] : [];

        return new Stream($options);
    }
}