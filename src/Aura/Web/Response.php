<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Web
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web;

/**
 * 
 * Retains information about the controller results for transfer to an actual
 * HTTP response object.
 * 
 * @package Aura.Web
 * 
 */
class Response
{
    /**
     * 
     * Should the response disable browser caching?
     * 
     * When `false`, the `getHeaders()` method will set these headers in its
     * return value (note that they are not added to `$headers` directly):
     * 
     * {{code:
     *     Pragma: no-cache
     *     Cache-Control: no-store, no-cache, must-revalidate
     *     Cache-Control: post-check=0, pre-check=0
     *     Expires: 1
     * }}
     * 
     * When true or null, the `getHeaders()` method will make no changes to
     * the existing headers.
     * 
     * @var bool
     * 
     * @see setCache()
     * 
     * @see setRedirectAfterPost()
     * 
     */
    protected $cache = null;

    /**
     * 
     * The response body content.
     * 
     * @var string
     * 
     */
    protected $content = null;

    /**
     * 
     * The Content-Type of the response.
     * 
     * @var string
     * 
     */
    protected $content_type = null;

    /**
     * 
     * The response cookies.
     * 
     * @var array
     * 
     */
    protected $cookies = [];

    /**
     * 
     * Whether or not cookies should default to being sent by HTTP only.
     * 
     * @var bool
     * 
     */
    protected $cookies_httponly = true;

    /**
     * 
     * The response headers (less the cookies).
     * 
     * @var array
     * 
     */
    protected $headers = [];

    /**
     * 
     * Redirect to this location.
     * 
     * @var string
     * 
     */
    protected $redirect = '';

    /**
     * 
     * The response status code.
     * 
     * @var int
     * 
     */
    protected $status_code = 200;

    /**
     * 
     * The response status text.
     * 
     * @var int
     * 
     */
    protected $status_text = null;

    /**
     * 
     * The HTTP version to send as.
     * 
     * @var string
     * 
     */
    protected $version = '1.1';

    /**
     * 
     * Should the response disable HTTP caching?
     * 
     * When `false`, the `getHeaders()` method will add these headers:
     * 
     *     Pragma: no-cache
     *     Cache-Control: no-store, no-cache, must-revalidate
     *     Cache-Control: post-check=0, pre-check=0
     *     Expires: 1
     * 
     * @param bool $flag When true, disable browser caching.
     * 
     * @see redirectPost()
     * 
     * @return void
     * 
     */
    public function setCache($flag)
    {
        if ($flag === null) {
            $this->cache = null;
        } else {
            $this->cache = (bool) $flag;
        }
    }

    /**
     * 
     * Is caching turned off?
     * 
     * @return mixed Note that this is a ternary: true, false, or null.
     * 
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * 
     * Sets the content of the response.
     * 
     * @param string $content The body content of the response.
     * 
     * @return void
     * 
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * 
     * Gets the content of the response.
     * 
     * @return string The body content of the response.
     * 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * 
     * Sets the Content-Type of the response.
     * 
     * @param string $type The Content-Type of the response.
     * 
     * @return void
     * 
     * @see negotiateContentType()
     * 
     */
    public function setContentType($type)
    {
        $this->content_type = $type;
    }

    /**
     * 
     * Gets the Content-Type of the response.
     * 
     * @return string The Content-Type of the response.
     * 
     */
    public function getContentType()
    {
        return $this->content_type;
    }

    /**
     * 
     * Sets a cookie value in `$cookies`.
     * 
     * @param string $name The name of the cookie.
     * 
     * @param string $value The value of the cookie.
     * 
     * @param int|string $expire The Unix timestamp after which the cookie
     * expires.  If non-numeric, the method uses strtotime() on the value.
     * 
     * @param string $path The path on the server in which the cookie will be
     * available on.
     * 
     * @param string $domain The domain that the cookie is available on.
     * 
     * @param bool $secure Indicates that the cookie should only be
     * transmitted over a secure HTTPS connection.
     * 
     * @param bool $httponly When true, the cookie will be made accessible
     * only through the HTTP protocol. This means that the cookie won't be
     * accessible by scripting languages, such as JavaScript.
     * 
     * @return void
     * 
     */
    public function setCookie(
        $name,
        $value = '',
        $expire = 0,
        $path = '',
        $domain = '',
        $secure = false,
        $httponly = null
    ) {
        $this->cookies[$name] = [
            'value'    => $value,
            'expire'   => $expire,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $httponly,
        ];
    }

    /**
     * 
     * Gets one cookie for the response.
     * 
     * @param string $name The cookie name.
     * 
     * @return array A cookie descriptor.
     * 
     */
    public function getCookie($name)
    {
        $cookie = $this->cookies[$name];

        // was httponly set for this cookie?  if not,
        // use the default.
        $cookie['httponly'] = ($cookie['httponly'] === null)
                            ? $this->cookies_httponly
                            : (bool) $cookie['httponly'];

        // try to allow for times not in unix-timestamp format
        if (! is_numeric($cookie['expire'])) {
            $cookie['expire'] = strtotime($cookie['expire']);
        }

        $cookie['expire'] = (int) $cookie['expire'];
        $cookie['secure']  = (bool) $cookie['secure'];
        return $cookie;
    }

    /**
     * 
     * Gets all cookies for the response.
     * 
     * @return array A sequential array of cookie descriptors.
     * 
     */
    public function getCookies()
    {
        $cookies = [];
        foreach ($this->cookies as $name => $cookie) {
            $cookies[$name] = $this->getCookie($name);
        }
        return $cookies;
    }

    /**
     * 
     * By default, should cookies be sent by HTTP only?
     * 
     * @param bool $flag True to send by HTTP only, false to send by any
     * method.
     * 
     * @return void
     * 
     */
    public function setCookiesHttponly($flag)
    {
        $this->cookies_httponly = (bool) $flag;
    }

    /**
     * 
     * Sets a header value in `$headers`.
     * 
     * @param string $key The header label.
     * 
     * @param string $val The value for the header.
     * 
     * @return void
     * 
     */
    public function setHeader($key, $val)
    {
        $key = $this->headerLabel($key);
        $val = $this->headerValue($val);
        $this->headers[$key] = $val;
    }

    /**
     * 
     * Adds to a header value in $this->headers.
     * 
     * @param string $key The header label, such as "Content-Type".
     * 
     * @param string $val The value for the header.
     * 
     * @return void
     * 
     */
    public function addHeader($key, $val)
    {
        $key = $this->headerLabel($key);
        $val = $this->headerValue($val);
        settype($this->headers[$key], 'array');
        $this->headers[$key][] = $val;
    }

    /**
     * 
     * Returns the value of a single header.
     * 
     * @param string $key The header name.
     * 
     * @return string|array A string if the header has only one value, or an
     * array if the header has multiple values, or null if the header does not
     * exist.
     * 
     */
    public function getHeader($key)
    {
        $headers = $this->getHeaders();
        $key     = $this->headerLabel($key);
        if (isset($headers[$key])) {
            return $headers[$key];
        }
    }

    /**
     * 
     * Returns the array of all headers to be sent with the response.
     * 
     * @return array
     * 
     * @todo Add case for cache === true (hm, or cache === array).
     * 
     */
    public function getHeaders()
    {
        $headers = $this->headers;

        if ($this->content_type) {
            $headers['Content-Type'] = $this->headerValue($this->content_type);
        }

        if ($this->cache === false) {
            $headers['Pragma'] = 'no-cache';
            $headers['Cache-Control'] = [
                'no-store, no-cache, must-revalidate',
                'post-check=0, pre-check=0',
            ];
            $headers['Expires'] = '1';
        }

        if ($this->redirect) {
            $headers['Location'] = $this->headerValue($this->redirect);
        }

        return $headers;
    }

    /**
     * 
     * Set a location that the response should redirect to, along with a
     * a status code and status text.
     * 
     * @param string $uri The URI to redirect to.
     * 
     * @param int $code The HTTP status code to redirect with; default
     * is `302`.
     * 
     * @param string $text The HTTP status text; default is `'Found'`.
     * 
     * @return void
     * 
     */
    public function setRedirect($uri, $code = 302, $text = 'Found')
    {
        $this->redirect = $uri;
        $this->setStatusCode($code);
        $this->setStatusText($text);
    }

    /**
     * 
     * Set a location that the response should redirect to, along with a
     * a status code and status text, *and* sets caching to false.
     * 
     * @param string $uri The URI to redirect to.
     * 
     * @param int|string $code The HTTP status code to redirect with; default
     * is `303`.
     * 
     * @param string $text The HTTP status text; default is `'See Other'`.
     * 
     * @return void
     * 
     */
    public function setRedirectAfterPost($uri, $code = '303', $text = 'See Other')
    {
        $this->setRedirect($uri, $code, $text);
        $this->setCache(false);
    }

    /**
     * 
     * Is the response set to issue a redirect?
     * 
     * @return bool
     * 
     */
    public function isRedirect()
    {
        return (bool) $this->redirect;
    }

    /**
     * 
     * Returns the redirect location, if any.
     * 
     * @return string
     * 
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * 
     * Sets the HTTP status code to for the response.
     * 
     * Automatically resets the status text to null.
     * 
     * @param int $code An HTTP status code, such as 200, 302, 404, etc.
     * 
     */
    public function setStatusCode($code)
    {
        $code = (int) $code;
        if ($code < 100 || $code > 599) {
            throw new Exception("Status code $code not recognized.");
        }

        $this->status_code = $code;
        $this->setStatusText(null);
    }

    /**
     * 
     * Returns the HTTP status code for the response.
     * 
     * @return int
     * 
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * 
     * Sets the HTTP status text for the response.
     * 
     * @param string $text The status text.
     * 
     * @return void
     * 
     */
    public function setStatusText($text)
    {
        // trim and remove newlines
        $text = trim(str_replace(["\r", "\n"], '', $text));
        $this->status_text = $text;
    }

    /**
     * 
     * Returns the HTTP status text for the response.
     * 
     * @return string
     * 
     */
    public function getStatusText()
    {
        return $this->status_text;
    }

    /**
     * 
     * Sets the HTTP version for the response to '1.0' or '1.1'.
     * 
     * @param string $version The HTTP version to use for this response.
     * 
     * @return void
     * 
     */
    public function setVersion($version)
    {
        $version = trim($version);
        if ($version != '1.0' && $version != '1.1') {
            throw new Exception("HTTP version '$version' not recognized.");
        } else {
            $this->version = $version;
        }
    }

    /**
     * 
     * Returns the HTTP version for the response.
     * 
     * @return string
     * 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * 
     * Normalizes and sanitizes a header label.
     * 
     * @param string $label The header label to be sanitized.
     * 
     * @return string The sanitized header label.
     * 
     */
    protected function headerLabel($label)
    {
        $label = preg_replace('/[^a-zA-Z0-9-]/', '', $label);
        $label = ucwords(strtolower(str_replace('-', ' ', $label)));
        $label = str_replace(' ', '-', $label);
        return $label;
    }

    /**
     * 
     * Sanitizes a header value.
     * 
     * @param string $value The header value to be sanitized.
     * 
     * @return string The sanitized header value.
     * 
     */
    protected function headerValue($value)
    {
        return str_replace(["\r", "\n"], '', $value);
    }
}
