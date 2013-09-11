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
 * Collection point for information about the web environment.
 * 
 * @package Aura.Web
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
     * The value of `php://input`.
     * 
     * @var string
     * 
     */
    protected $input = false;

    /**
     * 
     * An array of http user-agents used in matching 
     * mobile browsers and crawlers
     *
     * @see isMobile()
     * 
     * @see isCrawler()
     * 
     * @var array
     * 
     */
    protected $agents = [
        'mobile' => [
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
        ],
        'crawler' => [
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
        ],
    ];

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
     * @param array $globals An array of global variables, typically $GLOBALS.
     * 
     * @param array $agents An array of override agent values.
     * 
     */
    public function __construct(array $globals, array $agents = [])
    {
        $this->input  = file_get_contents('php://input');
        $this->get    = ! isset($globals['_GET'])    ? [] : $globals['_GET'];
        $this->post   = ! isset($globals['_POST'])   ? [] : $globals['_POST'];
        $this->server = ! isset($globals['_SERVER']) ? [] : $globals['_SERVER'];
        $this->cookie = ! isset($globals['_COOKIE']) ? [] : $globals['_COOKIE'];
        $this->env    = ! isset($globals['_ENV'])    ? [] : $globals['_ENV'];
        $files        = ! isset($globals['_FILES'])  ? [] : $globals['_FILES'];

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
        $valid = ['get', 'post', 'server', 'cookie', 'env', 'files', 'header'];

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
        return 'xmlhttprequest' == strtolower($this->getHeader('X-Requested-With'));
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
     * Retrieves an **unfiltered** value by key from the `$post` property,
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
    public function getPost($key = null, $alt = null)
    {
        return $this->getValue('post', $key, $alt);
    }

    /**
     * 
     * Retrieves an **unfiltered** value by key from the `$files` property,
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
    public function getFiles($key = null, $alt = null)
    {
        return $this->getValue('files', $key, $alt);
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
     * @return mixed The value of $cookie[$key], or the alternate default
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
     * Retrieves the unfiltered `$input` property.
     * 
     * @return string The value of $input.
     * 
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * 
     * Retrieves the `$input` property after applying `json_decode()`.
     * 
     * @param bool $assoc When true, returned objects will be converted into 
     * associative arrays.
     * 
     * @param int $depth Recursion depth.
     * 
     * @return object The `json_decode()` results.
     * 
     */
    public function getJsonInput($assoc = false, $depth = 512)
    {
        return json_decode($this->input, $assoc, $depth);
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
     * Set the "fake" `$header` property.
     * 
     * @return void
     * 
     */
    protected function setHeader()
    {
        // load the "fake" header var
        $this->header = [];

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
     * @param array $tgt Where we will store the restructured data when we
     * find it.
     * 
     * @return void
     * 
     */
    protected function rebuildFiles($src, &$tgt)
    {
        if (! $src) {
            $tgt = [];
            return;
        }

        // an array with these keys is a "target" for us (pre-sorted)
        $tgtkeys = ['error', 'name', 'size', 'tmp_name', 'type'];

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
                $tgt[$key] = [];
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
            // need the funny {} because $var[$key] will try to find a
            // property named for that element value, not for $var.
            return $this->{$var}[$key];
        } else {
            // requested key does not exist
            return $alt;
        }
    }

    /**
     *
     * Get the context URL.
     *
     * @return string
     *
     */
    public function getUrl()
    {
        // build a default scheme (with '://' in it)
        $url = $this->isSsl() ? 'https://' : 'http://';

        // add the current host
        $url .= $this->getServer('HTTP_HOST');

        // add the URI
        $url .= $this->getServer('REQUEST_URI');

        return $url;
    }
}
