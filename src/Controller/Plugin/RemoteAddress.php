<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Controller\Plugin;

use Laminas\Http\PhpEnvironment;

/**
 * Controller plugin for retrieving the IP address of the user who made the current HTTP request.
 */
class RemoteAddress
{
    /**
     * @var PhpEnvironment\RemoteAddress
     */
    private $remoteAddressDetector;


    /**
     * @param PhpEnvironment\RemoteAddress $remoteAddressDetector
     */
    public function __construct(PhpEnvironment\RemoteAddress $remoteAddressDetector)
    {
        $this->remoteAddressDetector = $remoteAddressDetector;
    }

    /**
     * @return string
     */
    public function __invoke()
    {
        return $this->remoteAddressDetector->getIpAddress();
    }
}