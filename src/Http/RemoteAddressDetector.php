<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Http;

use Laminas\Http\PhpEnvironment\RemoteAddress;

class RemoteAddressDetector extends RemoteAddress
{
    /**
     * @return string|false
     */
    protected function getIpAddressFromProxy()
    {
        if (!$this->useProxy)
            return false;

        if (empty($this->trustedProxies))
        {
            //No proxy whitelist in place, but useProxy is true, so trust from all
            $header = $this->proxyHeader;
            if (!isset($_SERVER[$header]) || empty($_SERVER[$header]))
                return false;

            $ips = explode(',', $_SERVER[$header]);
            $ips = array_map('trim', $ips);

            return array_pop($ips);
        }

        return parent::getIpAddressFromProxy();
    }

}