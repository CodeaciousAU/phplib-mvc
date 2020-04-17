<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\View\Helper;

use Laminas\Uri\Uri;
use Laminas\View\Helper\AbstractHelper;

/**
 * View helper to obtain an absolute URL for an asset file (eg. an image or stylesheet).
 */
class AssetUrl extends AbstractHelper
{
    /**
     * @var Uri|null
     */
    private $baseUrl;


    /**
     * @return Uri|null
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set the base URL for generating canonical asset URLs. Defaults to automatic, which
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
     * @param string $file A partial URL (relative to the application's root)
     * @param bool $cacheControlSuffix
     *
     * @return string
     */
    public function __invoke($file, $cacheControlSuffix=true)
    {
        if ($cacheControlSuffix)
            $path = $this->view->cacheControlAsset($file);
        else
            $path = $this->view->basePath($file);

        if (empty($this->baseUrl))
            return $this->view->serverUrl($path);

        return $this->baseUrl->getScheme().'://'.$this->baseUrl->getHost().$path;
    }
}