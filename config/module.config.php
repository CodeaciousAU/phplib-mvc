<?php
/**
 * Configuration for the module.
 */

use Codeacious\Mvc\Controller\Plugin as ControllerPlugin;
use Codeacious\Mvc\CsrfPreventionService;
use Codeacious\Mvc\CsrfPreventionServiceFactory;
use Codeacious\Mvc\Http\RemoteAddressDetector;
use Codeacious\Mvc\Http\RemoteAddressDetectorFactory;
use Codeacious\Mvc\Listener\ErrorListener;
use Codeacious\Mvc\Listener\ErrorListenerFactory;
use Codeacious\Mvc\Listener\ProfilingListener;
use Codeacious\Mvc\Listener\ProfilingListenerFactory;
use Codeacious\Mvc\Log\Formatter\JsonFormatter;
use Codeacious\Mvc\Log\Writer\DebugWriter;
use Codeacious\Mvc\Log\Writer\DebugWriterFactory;
use Codeacious\Mvc\Log\Writer\StreamWriterFactory;
use Codeacious\Mvc\View\Helper as ViewHelper;
use Interop\Container\ContainerInterface;
use Laminas\Log\LoggerAbstractServiceFactory;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\Uri\Uri;

return [
    'Codeacious\Mvc' => [
        'asset_url_prefix' => '',
        'csrf_cookie' => [
            'name' => 'FormToken',
            'is_secure' => false,
            'http_only' => false,
            'validity_period' => 0, //zero means the duration of the browser session
        ],
        'remote_address' => [
            //Whether to trust proxy headers (X-Forwarded-For) when determining the remote user's IP
            'use_proxy' => false,
            //List of IPs from which to trust proxy headers. If empty, no restriction is applied.
            'trusted_proxies' => [],
        ],
    ],

    'Codeacious\Mvc\Log' => [
        'debug_writer' => [
            'formatter' => [
                'name' => 'simple',
                'options' => [
                    'dateTimeFormat' => 'Y-m-d H:i:s T',
                    'format' => '[%timestamp%] %priorityName% %message% %extra%',
                ],
            ],
        ],
        'stream_writer' => [
            'formatter' => [
                'name' => JsonFormatter::class,
            ],
            'stream' => 'php://stderr',
        ],
    ],

    'service_manager' => [
        'abstract_factories' => [
            LoggerAbstractServiceFactory::class,
        ],
        'factories' => [
            CsrfPreventionService::class => CsrfPreventionServiceFactory::class,
            ErrorListener::class => ErrorListenerFactory::class,
            ProfilingListener::class => ProfilingListenerFactory::class,
            RemoteAddressDetector::class => RemoteAddressDetectorFactory::class,
        ],
    ],

    'controller_plugins' => [
        'aliases' => [
            'canonicalUrl' => ControllerPlugin\CanonicalUrl::class,
            'csrfToken' => ControllerPlugin\CsrfToken::class,
            'log' => ControllerPlugin\Log::class,
            'logError' => ControllerPlugin\LogError::class,
            'remoteAddress' => ControllerPlugin\RemoteAddress::class,
        ],
        'factories' => [
            ControllerPlugin\CanonicalUrl::class => function(ContainerInterface $container) {
                /* @var $router \Laminas\Router\RouteStackInterface */
                $router = $container->get('HttpRouter');
                return new ControllerPlugin\CanonicalUrl($router);
            },
            ControllerPlugin\CsrfToken::class => function(ContainerInterface $container) {
                /* @var $csrfService CsrfPreventionService */
                $csrfService = $container->get(CsrfPreventionService::class);
                return new ControllerPlugin\CsrfToken($csrfService);
            },
            ControllerPlugin\Log::class => function(ContainerInterface $container) {
                /* @var $logger \Laminas\Log\Logger */
                $logger = $container->get('Codeacious\Mvc\DefaultLogger');
                return new ControllerPlugin\Log($logger);
            },
            ControllerPlugin\LogError::class => InvokableFactory::class,
            ControllerPlugin\RemoteAddress::class => function(ContainerInterface $container) {
                /* @var $remoteAddr RemoteAddressDetector */
                $remoteAddr = $container->get(RemoteAddressDetector::class);
                return new ControllerPlugin\RemoteAddress($remoteAddr);
            },
        ],
    ],

    'view_helpers' => [
        'aliases' => [
            'assetUrl' => ViewHelper\AssetUrl::class,
            'cacheControlAsset' => ViewHelper\CacheControlAsset::class,
            'csrfToken' => ViewHelper\CsrfToken::class,
        ],
        'factories' => [
            ViewHelper\AssetUrl::class => function(ContainerInterface $container) {
                $config = $container->get('config');
                $baseUrl = !empty($config['Codeacious\Mvc']['asset_url_prefix'])
                    ? new Uri($config['Codeacious\Mvc']['asset_url_prefix']) : null;
                $helper = new ViewHelper\AssetUrl();
                $helper->setBaseUrl($baseUrl);
                return $helper;
            },
            ViewHelper\CacheControlAsset::class => InvokableFactory::class,
            ViewHelper\CsrfToken::class => function(ContainerInterface $container) {
                /* @var $csrfService CsrfPreventionService */
                $csrfService = $container->get(CsrfPreventionService::class);
                return new ViewHelper\CsrfToken($csrfService);
            },
        ],
    ],

    'listeners' => [
        ErrorListener::class,
        ProfilingListener::class,
    ],

    'log' => [
        'Codeacious\Mvc\DefaultLogger' => [
            'errorhandler' => true,
            'fatal_error_shutdownfunction' => true,
            'writers' => [
                [
                    'name' => 'Codeacious\Mvc\DefaultLogWriter',
                ],
            ],
        ],
    ],

    'log_writers' => [
        'aliases' => [
            'Codeacious\Mvc\DefaultLogWriter' => DebugWriter::class,
        ],
        'factories' => [
            DebugWriter::class => DebugWriterFactory::class,
            'Codeacious\Mvc\Log\Writer\StreamWriter' => StreamWriterFactory::class,
        ],
    ],
];