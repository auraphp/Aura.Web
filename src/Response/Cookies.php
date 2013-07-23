<?php
namespace Aura\Web\Response;

class Cookies
{
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
    protected $httponly = true;

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
    public function set(
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
    public function get($name = null)
    {
        if (! $name) {
            $cookies = [];
            foreach ($this->cookies as $name => $cookie) {
                $cookies[$name] = $this->get($name);
            }
            return $cookies;
        }
        
        $cookie = $this->cookies[$name];

        // was httponly set for this cookie?  if not,
        // use the default.
        $cookie['httponly'] = ($cookie['httponly'] === null)
                            ? $this->httponly
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
     * By default, should cookies be sent by HTTP only?
     * 
     * @param bool $flag True to send by HTTP only, false to send by any
     * method.
     * 
     * @return void
     * 
     */
    public function setHttponly($flag)
    {
        $this->httponly = (bool) $flag;
    }

    public function getHttponly()
    {
        return $this->httponly;
    }
}
