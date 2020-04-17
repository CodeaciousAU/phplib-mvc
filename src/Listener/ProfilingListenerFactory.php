<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Listener;

use Codeacious\ProfileCollector\Collector;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ProfilingListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('ApplicationConfig');

        $profiler = null;
        if (isset($config['profiler']) && $config['profiler'] instanceof Collector)
            $profiler = $config['profiler'];

        return new ProfilingListener($profiler);
    }
}