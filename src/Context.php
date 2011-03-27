<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\web;

/**
 * 
 * Collection point for information about the web environment.
 * 
 * @package aura.web
 * 
 */
class Context
{
    /**
     * 
     * Imported $_GET values.
     * 
     * @var array
     * 
     */
    protected $get;
    
    /**
     * 
     * Imported $_POST values.
     * 
     * @var array
     * 
     */
    protected $post;
    
    /**
     * 
     * Imported $_SERVER values.
     * 
     * @var array
     * 
     */
    protected $server;
    
    /**
     * 
     * Imported $_COOKIE values.
     * 
     * @var array
     * 
     */
    protected $cookie;
    
    /**
     * 
     * Imported $_ENV values.
     * 
     * @var array
     * 
     */
    protected $env;
    
    /**
     * 
     * Imported $_FILES values.
     * 
     * @var array
     * 
     */
    protected $files;
    
    /**
     * 
     * Imported $_SERVER['HTTP_*'] values.
     * 
     * Header keys are normalized and lower-cased; keys and values are
     * filtered for control characters.
     * 
     * @var array
     * 
     */
    protected $header;
    
    /**
     * 
     * The parsed http[accept*] headers with each header sorted
     * by the quality factor
     * 
     * @var array
     * 
     */
    protected $accept;
    
    /**
     * 
     * The value of `php://input`.
     * 
     * @var string
     * 
     */
    protected $raw_input = false;
    
    /**
     * 
     * A cross-site request forgery object.
     * 
     * @var Csrf
     * 
     */
    protected $csrf;
    
    /**
     * 
     * An array of http user-agents used in matching 
     * mobile browsers and crawlers
     *
     * @see isMobile()
     * @see isCrawler()
     * 
     * @var array
     * 
     */
    protected $agents = array(
        'mobile'=>array(
            'Android',
            'BlackBerry',
            'Blazer',
            'Brew',
            'IEMobile',
            'iPad',
            'iPhone',
            'iPod',
            'KDDI',
            'Kindle',
            'Maemo',
            'MOT-', // Motorola Internet Browser
            'Nokia',
            'SymbianOS',
            'UP.Browser', // Openwave Mobile Browser
            'UP.Link', 
            'Opera Mobi',
            'Opera Mini',        
            'webOS', // Palm devices
            'Playstation',
            'PS2',
            'Windows CE',
            'Polaris',
            'SEMC',
            'NetFront',
            'Fennec'
        ),
        'crawler'=>array(
            'Ask',
            'Baidu',
            'Google',        
            'AdsBot',
            'gsa-crawler',
            'adidxbot', 
            'librabot',
            'llssbot',
            'bingbot',
            'Danger hiptop',
            'MSMOBOT',
            'MSNBot',
            'MSR-ISRCCrawler',
            'MSRBOT',
            'Vancouver',
            'Y!J',
            'Yahoo',       
            'mp3Spider',
            'Mp3Bot',
            'Scooter',
            'slurp',
            'Y!OASIS',
            'YRL_ODP_CRAWLER',
            'Yandex',
            'Fast',
            'Lycos',
            'heritrix',
            'ia_archiver',
            'InternetArchive',
            'archive.org_bot',
            'Nutch',
            'WordPress',
            'Wget'
        )
    );
    
    /**
     * 
     * A property to hold previous calls to isMobile() 
     * so you don't have to loop through $this->agents['mobile'] again.
     * 
     * @var mixed
     * 
     */
    protected $is_mobile;
    
    /**
     * 
     * A property to hold previous calls to isCrawler() 
     * so you don't have to loop through $this->agents['crawler'] again.
     * 
     * @var mixed 
     * 
     */
    protected $is_crawler;
    
    /**
     * 
     * Constructor.
     * 
     */
    public function __construct(array $globals, Csrf $csrf = null, array $agents = null)
    {
        $this->get    = empty($globals['_GET'])    ? array() : $globals['_GET'];
        $this->post   = empty($globals['_POST'])   ? array() : $globals['_POST'];
        $this->server = empty($globals['_SERVER']) ? array() : $globals['_SERVER'];
        $this->cookie = empty($globals['_COOKIE']) ? array() : $globals['_COOKIE'];
        $this->env    = empty($globals['_ENV'])    ? array() : $globals['_ENV'];
        $files        = empty($globals['_FILES'])  ? array() : $globals['_FILES'];
        $this->csrf   = $csrf;
        
        if ($agents) {
            $this->agents = array_merge_recursive($this->agents, $agents);
        }
        
        $this->setHeader();
        $this->httpMethodOverride();
        $this->rebuildFiles($files, $this->files);
    }
    
    /**
     * 
     * Magic get to make properties read-only.
     * 
     * @param string $key The property to read.
     * 
     * @return mixed The property value.
     * 
     */
    public function __get($key)
    {
        $valid = array(
            'get', 'post', 'server', 'cookie', 'env', 'files', 'header',
        );
        
        if (in_array($key, $valid)) {
            return $this->{$key};
        }
        
        throw new \UnexpectedValueException($key);
    }

    /** 
     * 
     * Is this a GET request?
     * 
     * @return boolean
     * 
     */
    public function isGet()
    {
        return 'GET' == $this->getServer('REQUEST_METHOD');
    }
    
    /**
     *  
     * Is this a POST request?
     * 
     * @return boolean
     * 
     */
    public function isPost()
    {
        return 'POST' == $this->getServer('REQUEST_METHOD');
    }
    
    /**
     *  
     * Is this a PUT request?
     * 
     * @return boolean
     * 
     */
    public function isPut()
    {
        return 'PUT' == $this->getServer('REQUEST_METHOD');
    }
    
    /**
     *  
     * Is this a DELETE request?
     * 
     * @return boolean
     * 
     */
    public function isDelete()
    {
        return 'DELETE' == $this->getServer('REQUEST_METHOD');
    }
    
    /**
     *  
     * Is this a HEAD request?
     * 
     * @return boolean
     * 
     */
    public function isHead()
    {
        return 'HEAD' == $this->getServer('REQUEST_METHOD');
    }
    
    /**
     *  
     * Is this an OPTIONS request?
     * 
     * @return boolean
     * 
     */
    public function isOptions()
    {
        return 'OPTIONS' == $this->getServer('REQUEST_METHOD');
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
        return 'xmlhttprequest' == strtolower($this->getServer('HTTP_X_REQUESTED_WITH'));
    }
    
    /**
     *  
     * Is this a mobile device? 
     * 
     * @return mixed False if not mobile, or the matched pattern if it is.
     * 
     */
    public function isMobile()
    {
        // have we found a mobile agent previously?
        if ($this->is_mobile !== null) {
            // yes, return it
            return $this->is_mobile;
        }
        
        // by default, not mobile
        $this->is_mobile = false;
        
        // what is the actual user-agent string?
        $user_agent = $this->getServer('HTTP_USER_AGENT');
        
        // look for mobile agents
        foreach ($this->agents['mobile'] as $agent) {
            $find = preg_quote($agent);
            $match = preg_match("/$find/i", $user_agent); // case-insensitive
            if ($match) {
                $this->is_mobile = $agent;
                break;
            }
        }
        
        // done!
        return $this->is_mobile;
    }
    
    /**
     *  
     * Is this a crawler/bot device? 
     * 
     * @return mixed False if not a crawler, or the matched pattern if it is.
     * 
     */
    public function isCrawler()
    {
        // have we found a crawler agent previously?
        if ($this->is_crawler !== null) {
            // yes, return it
            return $this->is_crawler;
        }
        
        // by default, not crawler
        $this->is_crawler = false;
        
        // what is the actual user-agent string?
        $user_agent = $this->getServer('HTTP_USER_AGENT');
        
        // look for crawler agents
        foreach ($this->agents['crawler'] as $agent) {
            $find = preg_quote($agent);
            $match = preg_match("/$find/i", $user_agent); // case-insensitive
            if ($match) {
                $this->is_crawler = $agent;
                break;
            }
        }
        
        // done!
        return $this->is_crawler;
    }
    
    /**
     * 
     * Is the current request a cross-site forgery?
     * 
     * Note: if the key does not exist this method will return true.
     * 
     * @throws aura\web\Exception_Context If a CSRF library has not been provided.
     * 
     * @param string $key The name of the $_POST key containing the CSRF token.
     * 
     * @return bool
     * 
     */
    public function isCsrf($key = '__csrf_token')
    {
        if (! $this->csrf) {
            throw new Exception_Context('A CSRF library has not been provided');
        }
        
        $token = $this->getValue('post', $key, 'invalid-token');
        
        try {
            // if the token is valid return false. This is not a csrf attack.
            return ! $this->csrf->isValidToken($token);
        } catch (Exception_MalformedToken $e) {
            return true;
        }
    }
    
    /**
     *  
     * Is this an SSL request?
     * 
     * @return boolean
     * 
     */
    public function isSsl()
    {
        return $this->getServer('HTTPS') == 'on'
            || $this->getServer('SERVER_PORT') == 443;
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the `$get` property,
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
    public function getQuery($key = null, $alt = null)
    {
        return $this->getValue('get', $key, $alt);
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the `$cookie` property,
     * or an alternate default value if that key does not exist.
     * 
     * @param string $key The $cookie key to retrieve the value of.
     * 
     * @param string $alt The value to return if the key does not exist.
     * 
     * @param bool The value of $cookie[$key], or the alternate default
     * value.
     * 
     */
    public function getCookie($key = null, $alt = null)
    {
        return $this->getValue('cookie', $key, $alt);
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the `$env` property,
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
    public function getEnv($key = null, $alt = null)
    {
        return $this->getValue('env', $key, $alt);
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the `$server` property,
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
    public function getServer($key = null, $alt = null)
    {
        return $this->getValue('server', $key, $alt);
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value from a user input.
     * 
     * A value by key from the `$post` *and* `$files` properties, or an 
     * alternate default value if that key does not exist in either location.
     * Files takes precedence over post.
     * 
     * If the key is null and the content type isn't `multipart/form-data` and 
     * `$post` and `$files` are empty, the raw data from the request body 
     * is returned. 
     * 
     * @param string $key The $post and $files key to retrieve the value of.
     * 
     * @param string $alt The value to return if the key does not exist in
     * either $post or $files.
     * 
     * @return mixed The value of $post[$key] combined with $files[$key], or the
     * raw request body, or the alternate default value.
     * 
     */
    public function getInput($key = null, $alt = null)
    {
        $post  = $this->getValue('post',  $key, false);
        $files = $this->getValue('files', $key, false);
        
        $parts = explode(';', $this->getServer('CONTENT_TYPE'), 2);
        $ctype = trim(array_shift($parts));
        
        // POST or PUT data. It could be anything, a urlencoded string, xml, json, etc
        // So it is returned the way PHP received it.
        $use_raw = null === $key
                && 'multipart/form-data' != $ctype
                && empty($post)
                && empty($files);
                
        if ($use_raw) {
            // in some cases php://input can only be read once
            if (false === $this->raw_input) {
                $this->raw_input = file_get_contents('php://input');
            }
            
            return $this->raw_input;
        }

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
        
        // files is always an array so we test for a multidimensional array
        $files_array = is_array($files[key($files)]);
        
        // neither are arrays, append to files
        if (! $post_array && ! $files_array) {
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
            return ($post_array && ! empty($post)) ?
                        array_merge((array) $post, $files) : $files;
        }
        
        // post array but single files, append to post
        return array_merge($post, $files);
    }
    
    /**
     * 
     * Retrieves an **unfiltered** value by key from the `$header` property,
     * or an alternate default value if that key does not exist.
     * 
     * @param string $key The $http key to retrieve the value of.
     * 
     * @param string $alt The value to return if the key does not exist.
     * 
     * @return mixed The value of $header[$key], or the alternate default
     * value.
     * 
     */
    public function getHeader($key = null, $alt = null)
    {
        $key = strtolower($key);
        return $this->getValue('header', $key, $alt);
    }
    
    /**
     * 
     * Parse a http[accept*] header and sort by the quality factor, the 
     * highest being first in the returned array. The returned data is 
     * unfiltered.
     * 
     * @param string $header The name of the accept header to parse.
     * 
     * @param mixed $alt The value to return if the key does not exist.
     * 
     * @return array
     * 
     */
    protected function parseAccept($accept, $alt = null)
    {
        $accept = explode(',', $accept);
        $sorted = array();
        
        foreach ((array) $accept as $key => $value) {
            $value = trim($value);
            
            if (false === strpos($value, ';q=')) {
                $sorted[$value]  = 1.0;
            } else {
                list($value, $q) = explode(';q=', $value);
                $sorted[$value]  = $q;
            }
        }
        
        // sort by quality factor, highest first.
        asort($sorted);
        return $sorted;
    }
    
    /**
     * 
     * Gets an `Accept` header.  If you want the content-type, ask for 
     * `'type'`; otherwise, if you want (e.g.) `'Accept-Language'`, ask for 
     * `'language'`.
     * 
     * @param string $key The `$accept` key to return; if null, returns the
     * entire `$accept` property.
     * 
     * @param mixed $alt The value to return if the key does not exist.
     * 
     * @return array
     * 
     */
    public function getAccept($key = null, $alt = null)
    {
        // do we have an $accept property yet?
        if (null === $this->accept) {
            // create the $accept property
            $this->accept = array();
            // go through each header ...
            foreach ($this->header as $label => $value) {
                
                // then extract and parse only accept* headers
                $label = strtolower($label);
                if ('accept' == substr($label, 0, 6)) {
                    if ('accept' == $label) {
                        // content type
                        $label = 'type';
                    } else {
                        // accept-(charset|language|encoding)
                        $label = substr($label, 7);
                    }
                    $this->accept[$label] = $this->parseAccept($value);
                }
            }
        }
        
        if (null == $key) {
            return $this->accept;
        }
        
        $key = strtolower($key);
        if (isset($this->accept[$key])) {
            return $this->accept[$key];
        } else {
            return $alt;
        }
    }
    
    /**
     * 
     * Set the "fake" `$header` property.
     * 
     * @return void
     * 
     */
    protected function setHeader()
    {
        // load the "fake" header var
        $this->header = array();
        
        foreach ($this->server as $key => $val) {
            
            // only retain HTTP headers
            if ('HTTP_' == substr($key, 0, 5)) {
                
                // normalize the header key
                $nicekey = str_replace('_', '-', strtolower(substr($key, 5)));
                
                // strip control characters from keys and values
                $nicekey = preg_replace('/[\x00-\x1F]/', '', $nicekey);
                $val     = preg_replace('/[\x00-\x1F]/', '', $val);
                
                $this->header[$nicekey] = $val;
                // no control characters wanted in $this->server for these
                $this->server[$key]     = $val;
                
                // disallow external setting of X-JSON headers.
                if ('x-json' == $nicekey) {
                    unset($this->header[$nicekey]);
                    unset($this->server[$key]);
                }
            }
        }
    }
    
    /**
     * 
     * Overrides the REQUEST_METHOD with X-HTTP-Method-Override header or 
     * $_POST value.
     * 
     * @return void
     * 
     */
    protected function httpMethodOverride()
    {
        // must be a POST to do an override
        if ('POST' != $this->getServer('REQUEST_METHOD')) {
            return;
        }
        
        // look for override in header
        $override = $this->getHeader('x-http-method-override');
        if ($override) {
            $this->server['REQUEST_METHOD'] = strtoupper($override);
            return;
        }
        
        // look for override in $_POST
        $override = isset($this->post['X-HTTP-Method-Override'])
                  ? $this->post['X-HTTP-Method-Override']
                  : null;
        if ($override) {
            $this->server['REQUEST_METHOD'] = strtoupper($override);
            return;
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
        if (! $src) {
            $tgt = array();
            return;
        }
        
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
     * Common method to get a property value and return it.
     * 
     * @param string $var The property variable to fetch from: get, post,
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
        if (null === $key) {
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
