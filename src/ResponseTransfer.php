<?php
/**
 * 
 * This file is part of the Aura project for PHP.
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
class ResponseTransfer
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
    protected $content_type;
    
    /**
     * 
     * The response cookies.
     * 
     * @var array
     * 
     */
    protected $cookies = array();
    
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
     * The filename .format extension used to determine the content-type.
     * 
     * @var bool
     * 
     */
    protected $format;
    
    /**
     * 
     * The response headers (less the cookies).
     * 
     * @var array
     * 
     */
    protected $headers = array();
    
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
     * The outer layout template for use in a 2-step view.
     * 
     * @var mixed
     * 
     */
    protected $layout;
    
    /**
     * 
     * The name of the content variable in the layout template.
     * 
     * @var string
     * 
     */
    protected $layout_content_var = 'layout_content';
    
    /**
     * 
     * Data for the layout.
     * 
     * @var \ArrayObject
     * 
     */
    protected $layout_data;
    
    /**
     * 
     * Stack to search for layout templates.
     * 
     * @var array
     * 
     */
    protected $layout_stack = array();
    
    /**
     * 
     * The inner view template options for use in a 2-step view.
     * 
     * @var string|array
     * 
     */
    protected $view;
    
    /**
     * 
     * Data for the view.
     * 
     * @var \ArrayObject
     * 
     */
    protected $view_data;
    
    /**
     * 
     * Stack to search for view templates.
     * 
     * @var array
     * 
     */
    protected $view_stack = array();
    
    /**
     * 
     * An array of `.format` extensions to Content-Type mappings.
     * 
     * @var array
     * 
     */
    protected $format_type = array(
        '.atom'     => 'application/atom+xml',
        '.css'      => 'text/css',
        '.htm'      => 'text/html',
        '.html'     => 'text/html',
        '.js'       => 'text/javascript',
        '.json'     => 'application/json',
        '.pdf'      => 'application/pdf',
        '.ps'       => 'application/postscript',
        '.rdf'      => 'application/rdf+xml',
        '.rss'      => 'application/rss+xml',
        '.rss2'     => 'application/rss+xml',
        '.rtf'      => 'application/rtf',
        '.text'     => 'text/plain',
        '.txt'      => 'text/plain',
        '.xhtml'    => 'application/xhtml+xml',
        '.xml'      => 'application/xml',
    );
    
    /**
     * 
     * Constructor.
     * 
     * @param array $format_type Additional or override `.format` to 
     * Content-Type mappings.
     * 
     */
    public function __construct(array $format_type = array())
    {
        $this->view_data   = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
        $this->layout_data = new \ArrayObject(array(), \ArrayObject::ARRAY_AS_PROPS);
        $this->format_type = array_merge($this->format_type, $format_type);
    }
    
    /**
     * 
     * Magic read to provide access to $view_data and $layout_data properties.
     * 
     * @param string $key The property name.
     * 
     * @return mixed The property value.
     * 
     */
    public function __get($key)
    {
        if ($key == 'view_data') {
            return $this->view_data;
        }
        
        if ($key == 'layout_data') {
            return $this->layout_data;
        }
        
        throw new Exception("Property '\$$key' is not accessible or does not exist.");
    }
    
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
     * @param string The Content-Type of the response.
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
    public function setCookie($name, $value = '', $expire = 0,
        $path = '', $domain = '', $secure = false, $httponly = null)
    {
        $this->cookies[$name] = array(
            'value'    => $value,
            'expire'   => $expire,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $httponly,
        );
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
        $cookies = array();
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
    public function setHeader($key, $val, $replace = true)
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
            $headers['Cache-Control'] = array(
                'no-store, no-cache, must-revalidate',
                'post-check=0, pre-check=0',
            );
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
    public function setRedirect($uri, $code = '302', $text = 'Found')
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
        $text = trim(str_replace(array("\r", "\n"), '', $text));
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
     * Sets the inner view template for a two-step view.
     * 
     * @param mixed $view The inner view template.
     * 
     * @return void
     * 
     */
    public function setView($view)
    {
        $this->view = $view;
    }
    
    /**
     * 
     * Returns the inner view template for a two-step view.
     * 
     * @return mixed The inner view template.
     * 
     */
    public function getView()
    {
        return $this->view;
    }
    
    /**
     * 
     * Sets the data for the inner view template.
     * 
     * @param array $view_data The inner view template data.
     * 
     * @return void
     * 
     */
    public function setViewData(array $view_data)
    {
        $this->view_data->exchangeArray($view_data);
    }
    
    /**
     * 
     * Returns the data for the inner view template.
     * 
     * @return array The inner view template data.
     * 
     */
    public function getViewData()
    {
        return $this->view_data->getArrayCopy();
    }
    
    /**
     * 
     * Adds a class name and subdirectory for the inner view template finder.
     * 
     * @param array $view_stack The inner view template finder stack.
     * 
     * @return void
     * 
     */
    public function addViewStack($spec, $subdir = 'view')
    {
        $subdir = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $subdir);
        $this->view_stack[] = array($spec, $subdir);
    }
    
    /**
     * 
     * Returns the stack for the inner view template finder.
     * 
     * @return array The inner view template finder stack.
     * 
     */
    public function getViewStack()
    {
        return $this->view_stack;
    }
    
    /**
     * 
     * Sets the outer layout template for a two-step layout.
     * 
     * @param mixed $layout The outer layout template.
     * 
     * @return void
     * 
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
    
    /**
     * 
     * Returns the outer layout template for a two-step layout.
     * 
     * @return mixed The outer layout template.
     * 
     */
    public function getLayout()
    {
        return $this->layout;
    }
    
    /**
     * 
     * Sets the data for the outer layout template.
     * 
     * @param array $layout_data The outer layout template data.
     * 
     * @return void
     * 
     */
    public function setLayoutData(array $layout_data)
    {
        $this->layout_data->exchangeArray($layout_data);
    }
    
    /**
     * 
     * Returns the data for the outer layout template.
     * 
     * @return array The outer layout template data.
     * 
     */
    public function getLayoutData()
    {
        return $this->layout_data->getArrayCopy();
    }
    
    /**
     * 
     * Adds a class name and subdirectory for the outer layout template finder.
     * 
     * @param array $layout_stack The outer layout template finder stack.
     * 
     * @return void
     * 
     */
    public function addLayoutStack($spec, $subdir = 'layout')
    {
        $subdir = str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $subdir);
        $this->layout_stack[] = array($spec, $subdir);
    }
    
    /**
     * 
     * Returns the stack for the outer layout template finder.
     * 
     * @return array The outer layout template finder stack.
     * 
     */
    public function getLayoutStack()
    {
        return $this->layout_stack;
    }
    
    public function setLayoutContentVar($layout_content_var)
    {
        $this->layout_content_var = $layout_content_var;
    }
    
    public function getLayoutContentVar()
    {
        return $this->layout_content_var;
    }
    
    /**
     * 
     * Sets the output format extension.
     * 
     * @param string $format
     * 
     * @return void
     * 
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }
    
    /**
     * 
     * Returns the output format extension.
     * 
     * @return string
     * 
     */
    public function getFormat()
    {
        return $this->format;
    }
    
    /**
     * 
     * Negotiates, and then sets, the response Content-Type by checking
     * an array of acceptable types against the format extension, then 
     * against the view type map, and finally against the layout type map.
     * 
     * @param array $accept An array of acceptable types.
     * 
     * @return void
     * 
     */
    public function negotiateContentType(array $accept = array())
    {
        // only negoatiate if not already set
        if ($this->content_type) {
            return null;
        }
        
        // if an explicit format is set, use that
        if ($this->format && isset($this->format_type[$this->format])) {
            $this->content_type = $this->format_type[$this->format];
            return true;
        }
        
        // negotiate the Accept headers against the "view" portion.
        if (is_array($this->view)) {
            foreach ($accept as $type) {
                if (isset($this->view[$type])) {
                    // found an acceptable content type
                    $this->content_type = $type;
                    return true;
                }
            }
        }
        
        // negotiate the Accept headers against the "layout" portion.
        if (is_array($this->layout)) {
            foreach ($accept as $type) {
                if (isset($this->layout[$type])) {
                    // found an acceptable content type
                    $this->content_type = $type;
                    return true;
                }
            }
        }
        
        // could not negotiate a content type
        return false;
    }
    
    /**
     * 
     * Compares the view map against the content-type and returns the 
     * matching view specification.
     * 
     * @return string
     * 
     */
    public function matchView()
    {
        $view = $this->match($this->getView());
        if ($view !== false) {
            return $view;
        } else {
            throw new Exception\NoAcceptableView($this->content_type);
        }
    }
    
    /**
     * 
     * Compares the layout map against the content-type and returns the 
     * matching layout specification.
     * 
     * @return string
     * 
     */
    public function matchLayout()
    {
        $layout = $this->match($this->getLayout());
        if ($layout !== false) {
            return $layout;
        } else {
            throw new Exception\NoAcceptableLayout($this->content_type);
        }
    }
    
    /**
     * 
     * Support method for matching views and layouts.
     * 
     * @param mixed $spec The $view or $layout specification.
     * 
     * @return mixed The matching view or layout.
     * 
     */
    protected function match($spec)
    {
        // is the spec empty?
        if (! $spec) {
            return null;
        }
        
        // is the spec a string?
        if (is_string($spec)) {
            return $spec;
        }
        
        // is the spec a closure?
        if ($spec instanceof \Closure) {
            return $spec($this);
        }
        
        // is the spec an array, with a matching content-type key?
        $has_match = is_array($spec)
                  && isset($spec[$this->content_type]);
        if ($has_match) {
            $match = $spec[$this->content_type];
            // allow for closure or string
            if ($match instanceof \Closure) {
                return $match($this);
            } else {
                return $match;
            }
        }
        
        // no match
        return false;
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
     * @param string $label The header value to be sanitized.
     * 
     * @return string The sanitized header value.
     * 
     */
    protected function headerValue($value)
    {
        return str_replace(array("\r", "\n"), '', $value);
    }
}
