<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Router\RouteStackInterface;
use Laminas\Uri\Uri;

/**
 * Controller plugin for generating absolute URLs from application routes, even when the application
 * is running from the CLI.
 */
class CanonicalUrl extends AbstractPlugin
{
    /**
     * @var Uri|null
     */
    private $baseUrl;

    /**
     * @var RouteStackInterface
     */
    private $router;


    /**
     * @param RouteStackInterface $router
     */
    public function __construct(RouteStackInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return Uri|null
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set the base application URL (for generating canonical URLs). Defaults to automatic, which
     * only works when the application is serving an HTTP request.
     *
     * @param Uri|null $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    /**
     * @return $this
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @param string $routeName
     * @param array $params
     * @return string
     */
    public function fromRoute($routeName, array $params = [])
    {
        $options = ['name' => $routeName, 'force_canonical' => true];
        if ($this->baseUrl)
            $options['uri'] = $this->baseUrl;

        return $this->router->assemble($params, $options);
    }
}