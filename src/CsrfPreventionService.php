<?php
/**
 * @author Glenn Schmidt <glenn@codeacious.com>
 */

namespace Codeacious\Mvc;

use \RuntimeException;

/**
 * Generates a random token and stores it in a cookie. By including this token as a hidden form
 * field, and comparing the form submission against the cookie value, you can protect against
 * CSRF attacks.
 *
 * For the principle behind this, see:
 * https://www.owasp.org/index.php/Cross-Site_Request_Forgery_(CSRF)_Prevention_Cheat_Sheet#Double_Submit_Cookies
 */
class CsrfPreventionService
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var string
     */
    protected $tokenValue;


    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $requiredKeys = array('name', 'validity_period', 'is_secure');
        foreach ($requiredKeys as $key)
        {
            if (!isset($this->config[$key]))
                throw new RuntimeException('Missing configuration key "'.$key.'"');
        }
    }

    /**
     * @return string
     */
    public function getToken()
    {
        if (!$this->tokenValue)
        {
            $cookieName = $this->getConfig('name');
            if (!empty($_COOKIE[$cookieName]))
                $this->tokenValue = $_COOKIE[$cookieName];
            else
            {
                $this->tokenValue = $this->_makeToken();
                $validityPeriod = $this->getConfig('validity_period');
                $expires = 0;
                if ($validityPeriod > 0)
                    $expires = time() + $validityPeriod;

                setcookie(
                    $this->getConfig('name'),
                    $this->tokenValue,
                    $expires,
                    '/',
                    $this->getConfig('domain'),
                    $this->getConfig('is_secure', false),
                    $this->getConfig('http_only', false)
                );
            }
        }
        return $this->tokenValue;
    }

    /**
     * @return void
     */
    public function resetToken()
    {
        $cookieName = $this->getConfig('name');
        if (isset($_COOKIE[$cookieName]))
            unset($_COOKIE[$cookieName]);

        setcookie(
            $cookieName,
            '',
            time() - 3600,
            '/',
            $this->getConfig('domain'),
            $this->getConfig('is_secure', false),
            $this->getConfig('http_only', false)
        );

        $this->tokenValue = null;
    }

    /**
     * @param string $token
     * @return boolean
     */
    public function verifyToken($token)
    {
        return (!empty($token) && ($token == $this->getToken()));
    }

    /**
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfig($key=null, $default=null)
    {
        if (!isset($this->config[$key]))
            return $default;

        return $this->config[$key];
    }

    /**
     * @return string
     */
    protected function _makeToken()
    {
        $data = random_bytes(20);
        if (!$data)
            throw new RuntimeException('OpenSSL failed to generate random bytes');
        return bin2hex($data);
    }
}