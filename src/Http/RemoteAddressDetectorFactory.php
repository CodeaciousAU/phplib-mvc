<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Http;

use Codeacious\Stdlib\ArrayTool;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class RemoteAddressDetectorFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $config = ArrayTool::getArrayAtPath($config, 'Codeacious\Mvc:remote_address');

        $useProxy = !!ArrayTool::getValueAtPath($config, 'use_proxy');
        $trustedProxies = ArrayTool::getArrayAtPath($config, 'trusted_proxies');
        $proxyHeader = ArrayTool::getValueAtPath($config, 'proxy_header');

        $service = new RemoteAddressDetector();
        $service->setUseProxy($useProxy)
            ->setTrustedProxies($trustedProxies);
        if (!empty($proxyHeader))
            $service->setProxyHeader($proxyHeader);

        return $service;
    }
}