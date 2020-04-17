<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc\View\Helper;

use Codeacious\Mvc\CsrfPreventionService;
use Laminas\View\Helper\AbstractHelper;

/**
 * View helper to obtain an anti-CSRF token for inclusion in a form.
 *
 * When the form is submitted, you can validate the token using the CsrfToken controller plugin.
 */
class CsrfToken extends AbstractHelper
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
     * @return string
     */
    public function __invoke()
    {
        return $this->service->getToken();
    }
}