<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\Controller\Plugin;

use Codeacious\Mvc\CsrfPreventionService;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Controller plugin for obtaining and verifying an anti-CSRF token.
 */
class CsrfToken extends AbstractPlugin
{
    /**
     * @var CsrfPreventionService
     */
    protected $service;


    /**
     * @param CsrfPreventionService $service
     */
    public function __construct(CsrfPreventionService $service)
    {
        $this->service = $service;
    }

    /**
     * @return CsrfToken
     */
    public function __invoke()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getToken();
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->service->getToken();
    }

    /**
     * @param string $token
     * @return boolean
     */
    public function verifyToken($token)
    {
        return $this->service->verifyToken($token);
    }
}