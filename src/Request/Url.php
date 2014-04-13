<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @package Aura.Web
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Web\Request;

use Aura\Web\Exception;

/**
 *
 * A Url object from the server values passed
 *
 * @package Aura.Web
 *
 */
class Url
{
    /**
     *
     * @var string Url string
     *
     */
    protected $string;

    /**
     *
     * @var array parts of the URL
     *
     */
    protected $parts;

    /**
     *
     * @var bool Indicate whether the request is secure or not
     *
     */
    protected $secure;

    /**
     *
     * @var array component constants, see http://php.net/parse-url
     *
     */
    protected $keys = array(
        PHP_URL_SCHEME      => 'scheme',
        PHP_URL_HOST        => 'host',
        PHP_URL_PORT        => 'port',
        PHP_URL_USER        => 'user',
        PHP_URL_PASS        => 'pass',
        PHP_URL_PATH        => 'path',
        PHP_URL_QUERY       => 'query',
        PHP_URL_FRAGMENT    => 'fragment',
    );

    /**
     *
     * Constructor
     *
     * @param array $server An array of server values
     *
     */
    public function __construct(array $server)
    {
        $this->secure = $this->getSecure($server);
        $this->string = $this->getString($server);
        $this->parts  = $this->getParts($server);
    }

    protected function getSecure($server)
    {
        $secure = $this->getHttps($server)
               || $this->getSecurePort($server)
               || $this->getSecureForward($server);
        return (bool) $secure;
    }

    protected function getString($server)
    {
        $scheme = $this->getScheme();
        list($host, $port) = $this->getHostPort($server);
        $uri = $this->getRequestUri($server);
        return $scheme . $host . $port . $uri;
    }

    protected function getParts($server)
    {
        $parts = parse_url($this->string);
        if ($this->hostIsMissing($server)) {
            $parts[PHP_URL_HOST] = null;
        }
        return $parts;
    }

    protected function hostIsMissing($server)
    {
        return ! isset($server['HTTP_HOST'])
            && ! isset($server['SERVER_NAME']);
    }

    protected function getHttps($server)
    {
        return isset($server['HTTPS'])
             ? (strtolower($server['HTTPS']) == 'on')
             : false;
    }

    protected function getSecurePort($server)
    {
        return isset($server['SERVER_PORT'])
             ? ($server['SERVER_PORT'] == 443)
             : false;
    }

    protected function getSecureForward($server)
    {
        return isset($server['HTTP_X_FORWARDED_PROTO'])
             ? (strtolower($server['HTTP_X_FORWARDED_PROTO']) == 'https')
             : false;
    }

    protected function getScheme()
    {
        return $this->secure
             ? 'https://'
             : 'http://';
    }

    protected function getHostPort($server)
    {
        // pick the host; we need to fake it on missing
        // hosts for parse_url() to work properly
        $host = 'example.com';
        if (isset($server['HTTP_HOST'])) {
            $host = $server['HTTP_HOST'];
        } elseif(isset($server['SERVER_NAME'])) {
            $host = $server['SERVER_NAME'];
        }

        $port = $this->getPort($server, $host);
        return array($host, $port);
    }

    protected function getPort($server, &$host)
    {
        preg_match('#\:[0-9]+$#', $host, $matches);
        if ($matches) {
            $found_port = array_pop($matches);
            $host = substr($host, 0, -strlen($found_port));
        }

        // pick the port
        $port   = isset($server['SERVER_PORT'])
                ? ':' . $server['SERVER_PORT']
                : null;

        if (is_null($port) && !empty($found_port)) {
            $port = $found_port;
        }

        return $port;
    }

    protected function getRequestUri($server)
    {
        return isset($server['REQUEST_URI'])
             ? $server['REQUEST_URI']
             : null;
    }

    /**
     *
     * Returns the full URL string;
     * or, if a component constant is passed, returns only that part of the URL
     *
     * @param string $component
     *
     * @return string
     *
     */
    public function get($component = null)
    {
        if ($component === null) {
            return $this->string;
        }

        if (! isset($this->keys[$component])) {
            throw new Exception\InvalidComponent($component);
        }

        $key = $this->keys[$component];
        return isset($this->parts[$key])
             ? $this->parts[$key]
             : null;
    }

    /**
     *
     * Indicates if the request is secure, whether via SSL, TLS, or
     * forwarded from a secure protocol
     *
     * @return bool
     *
     */
    public function isSecure()
    {
        return $this->secure;
    }
}
