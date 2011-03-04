<?php

namespace aura\web;

/**
 * 
 * Class for gathering details about the request environment.
 * 
 * To be safe, treat everything in the superglobals as tainted.
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @author Clay Loveless <clay@killersoft.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
class Context
{
    /** @var array */
    protected $get;
    
    /** @var array */
    protected $post;
    
    /** @var array */
    protected $server;
    
    /** @var array */
    protected $cookie;
    
    /** @var array */
    protected $env;
    
    /** @var array */
    protected $files;
    
    /** @var array */
    protected $http;
    
    /** @var array */
    protected $raw;
    
    /** @var aura\http\Csrf */
    protected $csrf;


    public function __construct(array $get,    array $post,
				                array $server, array $cookie,
				                array $env,    array $files,
				                Csrf  $csrf)
    {
        $this->get    = $get;
        $this->post   = $post;
        $this->server = $server;
        $this->cookie = $cookie;
        $this->env    = $env;
        $this->csrf   = $csrf;
        
        $this->setupHttp();
        $this->rebuildFiles($files, $this->files);
    }
    
    public function __get($key)
    {
        $valid = array('get',   'post', 'server', 'cookie', 'env', 
                       'files', 'http', 'argv',   'raw');
        
        if (in_array($key, $valid)) {
            return ('raw' == $key) ? $this->raw() : $this->{$key};
        }
        
        throw new \UnexpectedValueException($key);
    }

    /** 
     * 
     * Is this a GET request.
     * 
     * @return boolean
     * 
     */
    public function isGet()
    {
        return 'GET' == $this->server('REQUEST_METHOD');
    }
    
    /**
     *  
     * Is this a POST request.
     * 
     * @return boolean
     * 
     */
    public function isPost()
    {
        return 'POST' == $this->server('REQUEST_METHOD');
    }
    
    /**
     *  
     * Is this a PUT request.
     * 
     * @return boolean
     * 
     */
    public function isPut()
    {
        $is_put      = $this->server('REQUEST_METHOD') == 'PUT';
        
        $is_override = $this->server('REQUEST_METHOD') == 'POST' &&
                       $this->http('X-HTTP-Method-Override') == 'PUT';
        
        return ($is_put || $is_override);
    }
    
    /**
     *  
     * Is this a DELETE request.
     * 
     * @return boolean
     * 
     */
    public function isDelete()
    {
        $is_delete   = $this->server('REQUEST_METHOD') == 'DELETE';
        
        $is_override = $this->server('REQUEST_METHOD') == 'POST' &&
                       $this->http('X-HTTP-Method-Override') == 'DELETE';
        
        return ($is_delete || $is_override);
    }
    
    /**
     *  
     * Is this a HEAD request.
     * 
     * @return boolean
     * 
     */
    public function isHead()
    {
        return 'HEAD' == $this->server('REQUEST_METHOD');
    }
    
    /**
     *  
     * Is this an OPTIONS request.
     * 
     * @return boolean
     * 
     */
    public function isOptions()
    {
        return 'OPTIONS' == $this->server('REQUEST_METHOD');
    }
    
    /**
     *  
     * Is this an XmlHttpRequest?
     * 
     * @return boolean
     * 
     */
    public function isXhr()
    {
        return 'xmlhttprequest' == strtolower($this->server('HTTP_X_REQUESTED_WITH'));
    }
    
    /**
     * 
     * Is the current request a cross-site forgery?
     * Note: if the key does not exist this method will return true.
     * 
     * @param string $key The name of the $_POST key containing the CSRF token.
     * 
     * @return bool
     * 
     */
    public function isCsrf($key = '__csrf_token')
    {
        $token = $this->post($key, 'invalid-token');
        
        try {
            // if the token is valid return false. This is not a csrf attack.
            return !$this->csrf->isValidToken($token);
        } catch (Exception_InvalidTokenFormat $e) {
            return true;
        }
    }
    
    /**
     *  
     * Is this a ssl request.
     * 
     * @return boolean
     * 
     */
    public function isSsl()
    {
        return $this->server('HTTPS') == 'on'
            || $this->server('SERVER_PORT') == 443;
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the [[Request::$get | ]] property,
     * or an alternate default value if that key does not exist.
     * 
     * @param string $key The $get key to retrieve the value of.
     * 
     * @param string $alt The value to return if the key does not exist.
     * 
     * @return mixed The value of $get[$key], or the alternate default
     * value.
     * 
     */
    public function get($key = null, $alt = null)
    {
        return $this->getValue('get', $key, $alt);
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the [[Request::$post | ]] property,
     * or an alternate default value if that key does not exist.
     * 
     * @param string $key The $post key to retrieve the value of.
     * 
     * @param string $alt The value to return if the key does not exist.
     * 
     * @return mixed The value of $post[$key], or the alternate default
     * value.
     * 
     */
    public function post($key = null, $alt = null)
    {
        return $this->getValue('post', $key, $alt);
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the [[Request::$cookie | ]] property,
     * or an alternate default value if that key does not exist.
     * 
     * @param string $key The $cookie key to retrieve the value of.
     * 
     * @param string $alt The value to return if the key does not exist.
     * 
     * @param bool   $signed Is the cookie value signed.
     * 
     */
    public function cookie($key = null, $alt = null)
    {
        return $this->getValue('cookie', $key, $alt);
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the [[Request::$env | ]] property,
     * or an alternate default value if that key does not exist.
     * 
     * @param string $key The $env key to retrieve the value of.
     * 
     * @param string $alt The value to return if the key does not exist.
     * 
     * @return mixed The value of $env[$key], or the alternate default
     * value.
     * 
     */
    public function env($key = null, $alt = null)
    {
        return $this->getValue('env', $key, $alt);
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the [[Request::$server | ]] property,
     * or an alternate default value if that key does not exist.
     * 
     * @param string $key The $server key to retrieve the value of.
     * 
     * @param string $alt The value to return if the key does not exist.
     * 
     * @return mixed The value of $server[$key], or the alternate default
     * value.
     * 
     */
    public function server($key = null, $alt = null)
    {
        return $this->getValue('server', $key, $alt);
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the [[Request::$files | ]] property,
     * or an alternate default value if that key does not exist.
     * 
     * @param string $key The $files key to retrieve the value of.
     * 
     * @param string $alt The value to return if the key does not exist.
     * 
     * @return mixed The value of $files[$key], or the alternate default
     * value.
     * 
     */
    public function files($key = null, $alt = null)
    {
        return $this->getValue('files', $key, $alt);
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the [[Request::$post | ]] *and* 
     * [[Request::$files | ]] properties, or an alternate default value if that key does 
     * not exist in either location.  Files takes precedence over post.
     * 
     * @param string $key The $post and $files key to retrieve the value of.
     * 
     * @param string $alt The value to return if the key does not exist in
     * either $post or $files.
     * 
     * @return mixed The value of $post[$key] combined with $files[$key], or 
     * the alternate default value.
     * 
     */
    public function postAndFiles($key = null, $alt = null)
    {
        $post  = $this->getValue('post',  $key, false);
        $files = $this->getValue('files', $key, false);
        
        // no matches in post or files
        if (! $post && ! $files) {
            return $alt;
        }
        
        // match in post, not in files
        if ($post && ! $files) {
            return $post;
        }
        
        // match in files, not in post
        if (! $post && $files) {
            return $files;
        }
        
        // are either or both arrays?
        $post_array  = is_array($post);
        // files is allways an array so we test for a multidimensional array
        $files_array = is_array($files[key($files)]);
        
        // neither are arrays, append to files
        if (!$post_array && !$files_array) {
            array_push($files, $post);
            return $files;
        }
        
        // files array single/array post, append to files
        if ($files_array) {
            foreach ($files as $key => $file) {
                if ($post_array) {
                    if (isset($post[$key])) {
                        $files[$key] = array_merge((array) $post[$key], $files[$key]);
                        unset($post[$key]);
                    }
                } else {
                    $files[$key][] = $post;
                }
            }
            // merge the remaining post values
            return ($post_array && !empty($post)) ?
                        array_merge((array) $post, $files) : $files;
        }
        
        // post array but single files, append to post
        if ($post_array && ! $files_array) {
            return array_merge($post, $files);
        }
        
        // now what?
        return $alt;
    }

    /**
     * Get the raw POST data. Useful for accessing PUT data.
     *
     * @return string 
     * 
     */
    public function raw()
    {
        if (!$this->raw) {
            // fetch and parse the raw input
            $this->raw = file_get_contents('php://input');
        }
        
        return $this->raw;
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the [[Request::$http | ]] property,
     * or an alternate default value if that key does not exist.
     * 
     * @param string $key The $http key to retrieve the value of.
     * 
     * @param string $alt The value to return if the key does not exist.
     * 
     * @return mixed The value of $http[$key], or the alternate default
     * value.
     * 
     */
    public function http($key = null, $alt = null)
    {
        if ($key !== null) {
            $key = strtoupper($key);
        }
        return $this->getValue('http', $key, $alt);
    }
    
    /**
     * 
     * Parse a http[accept*] header and sort by the quality factor. The highest
     * being first in the returned array. The returned data in unfiltered.
     * 
     * @throws aura\web\Exception_Request
     * 
     * @param string $header The name of the accept header to parse.
     * 
     * @param mixed $alt The value to return if the key does not exist.
     * 
     * @return array
     * 
     */
    public function parseAccept($header, $alt = null)
    {
        $accept = $this->http($header);
        
        if ('accept' != substr(\strtolower($header), 0, 6)) {
            throw new Exception_Context('Not a HTTP accept key.');
        }
        
        if (!$accept) {
            return $alt;
        }
        
        $accept = explode(',', $accept);
        $pref   = array(array());
        
        foreach ((array) $accept as $key => $value) {
            $value = trim($value);
            
            if (false === strpos($value, ';q=')) {
                $accept[$key] = array($value, 1.0);
            } else {
                $accept[$key] = explode(';q=', $value);
            }
            
            $pref[$key] = $accept[$key][1];
        }
        
        // sort by quality factor, highest first.
        array_multisort($pref, SORT_DESC, $accept);
        return $accept;
    }
    
    /**
     * 
     * Setup the "fake" http variables.
     * 
     * @return void
     * 
     */
    protected function setupHttp()
    {
        // load the "fake" http request var
        $this->http = array();
        
        foreach ($this->server as $key => $val) {
            
            // only retain HTTP headers
            if (substr($key, 0, 5) == 'HTTP_') {
                
                // normalize the header key
                $nicekey = str_replace('_', '-', substr($key, 5));
                
                // strip control characters from keys and values
                $nicekey = preg_replace('/[\x00-\x1F]/', '', $nicekey);
                $this->http[$nicekey] = preg_replace('/[\x00-\x1F]/', '', $val);
                
                // no control characters wanted in $this->server for these
                $this->server[$key] = preg_replace('/[\x00-\x1F]/', '', $val);
                
                // disallow external setting of X-JSON headers.
                if ($nicekey == 'X-JSON') {
                    unset($this->http[$nicekey]);
                    unset($this->server[$key]);
                }
            }
        }
    }
    
    /**
     * 
     * Recursive method to rebuild $_FILES structure to be more like $_POST.
     * 
     * @param array $src The source $_FILES array, perhaps from a sub-
     * element of that array/
     * 
     * @param array &$tgt Where we will store the restructured data when we
     * find it.
     * 
     * @return void
     * 
     */
    protected function rebuildFiles($src, &$tgt)
    {
        // an array with these keys is a "target" for us (pre-sorted)
        $tgtkeys = array('error', 'name', 'size', 'tmp_name', 'type');
        
        // the keys of the source array (sorted so that comparisons work
        // regardless of original order)
        $srckeys = array_keys((array) $src);
        sort($srckeys);
        
        // is the source array a target?
        if ($srckeys == $tgtkeys) {
            // get error, name, size, etc
            foreach ($srckeys as $key) {
                if (is_array($src[$key])) {
                    // multiple file field names for each error, name, size, etc.
                    foreach ((array) $src[$key] as $field => $value) {
                        $tgt[$field][$key] = $value;
                    }
                } else {
                    // the key itself is error, name, size, etc., and the
                    // target is already the file field name
                    $tgt[$key] = $src[$key];
                }
            }
        } else {
            // not a target, create sub-elements and rebuild them too
            foreach ($src as $key => $val) {
                $tgt[$key] = array();
                $this->rebuildFiles($val, $tgt[$key]);
            }
        }
    }
    
    /**
     * 
     * Common method to get a request value and return it.
     * 
     * @param string $var The request variable to fetch from: get, post,
     * etc.
     * 
     * @param string $key The array key, if any, to get the value of.
     * 
     * @param string $alt The alternative default value to return if the
     * requested key does not exist.
     * 
     * @return mixed The requested value, or the alternative default
     * value.
     * 
     */
    protected function getValue($var, $key, $alt)
    {
        // get the whole property, or just one key?
        if ($key === null) {
            // no key selected, return the whole array
            return $this->$var;
        } elseif (array_key_exists($key, $this->$var)) {
            // found the requested key.
            // need the funny {} becuase $var[$key] will try to find a
            // property named for that element value, not for $var.
            return $this->{$var}[$key];
        } else {
            // requested key does not exist
            return $alt;
        }
    }
}
