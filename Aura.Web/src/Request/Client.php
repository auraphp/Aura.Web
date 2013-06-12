<?php
namespace Aura\Web;

class Client
{
    protected $accept = [];
    protected $accept_charset = [];
    protected $accept_encoding = [];
    protected $accept_language = [];
    protected $forwarded_for = [];
    protected $device = [
        'mobile' => null,
        'crawler' => null,
    ];
    protected $ip;
    protected $referer;
    protected $user_agent;
    protected $xhr = false;
    
    /**
     * 
     * User-Agent strings used in matching mobile clients.
     *
     * @see isMobile()
     * 
     * @var array
     * 
     */
    protected $mobile = [
        'Android',
        'BlackBerry',
        'Blazer',
        'Brew',
        'Fennec',
        'IEMobile',
        'iPad',
        'iPhone',
        'iPod',
        'KDDI',
        'Kindle',
        'Maemo',
        'MOT-', // Motorola Internet Browser
        'NetFront',
        'Nokia',
        'Playstation',
        'Polaris',
        'PS2',
        'SEMC',
        'SymbianOS',
        'UP.Browser', // Openwave Mobile Browser
        'UP.Link',
        'Opera Mobi',
        'Opera Mini',
        'webOS', // Palm devices
        'Windows CE',
    ];
    
    protected $crawler = [
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
    ];

    public function __construct(
        $server,
        array $mobile = [],
        array $crawler = []
    ) {
        $this->mobile  = array_merge($this->mobile,  $mobile);
        $this->crawler = array_merge($this->crawler, $crawler);
        
        $this->initAccept($server);
        $this->initForwardedFor($server);
        $this->initIp($server);
        $this->initReferer($server);
        $this->initUserAgent($server);
        $this->initXhr($server);
    }
    
    protected function initAccept($server)
    {
        $keys_vars = [
            'HTTP_ACCEPT'          => 'accept',
            'HTTP_ACCEPT_CHARSET'  => 'accept_charset',
            'HTTP_ACCEPT_ENCODING' => 'accept_encoding',
            'HTTP_ACCEPT_LANGUAGE' => 'accept_language',
        ];
        
        foreach ($keys_vars as $key => $var) {
            
            $raw = isset($server[$key])
                 ? $server[$key]
                 : null;
            
            if (! $raw) {
                continue;
            }
            
            $values = explode(',', $raw);
            
            foreach ($values as $value) {
                $value = trim($value);
                if (false === strpos($value, ';q=')) {
                    $this->{$var}[$value] = 1.0;
                } else {
                    list($value, $q) = explode(';q=', $value);
                    $this->{$var}[$value] = (float) $q;
                }
            }
            
            // sort by quality factor, highest first
            $this->{$var} = arsort($this->{$var});
        }
    }
    
    protected function initDevice($type)
    {
        $this->device[$type] = false;
        foreach ($this->$type as $regex) {
            $regex = preg_quote($regex);
            $match = preg_match("/$regex/i", $this->user_agent); // case-insensitive
            if ($match) {
                $this->device[$type] = true;
                return;
            }
        }
    }
    
    protected function initForwardedFor($server)
    {
        if (isset($server['HTTP_X_FORWARDED_FOR'])) {
            $value = $server['HTTP_X_FORWARDED_FOR'];
            $this->forwarded_for = explode(',', $value);
        }
    }
    
    protected function initIp($server)
    {
        // default value
        if (isset($server['REMOTE_ADDR'])) {
            $this->ip = $server['REMOTE_ADDR'];
        }
        
        // proxy value
        if ($this->forwarded_for) {
            $this->ip = $this->forwarded_for[0];
        }
    }
    
    protected function initUserAgent($server)
    {
        $this->user_agent = isset($server['HTTP_USER_AGENT'])
                          ? $server['HTTP_USER_AGENT']
                          : null;
    }
    
    protected function initReferer($server)
    {
        $this->referer = isset($server['HTTP_REFERER'])
                       ? $server['HTTP_REFERER']
                       : null;
    }
    
    public function initXhr($server)
    {
        if (isset($server['HTTP_X_REQUESTED_WITH'])) {
            $value = $server['HTTP_X_REQUESTED_WITH'];
            $this->xhr = ($value == 'xmlhttprequest');
        }
    }
    
    public function getAccept()
    {
        return $this->accept;
    }
    
    public function getAcceptCharset()
    {
        return $this->accept_charset;
    }
    
    public function getAcceptEncoding()
    {
        return $this->accept_encoding;
    }
    
    public function getAcceptLanguage()
    {
        return $this->accept_language;
    }
    
    public function getForwardedFor()
    {
        return $this->forwarded_for;
    }
    
    /**
     * 
     * Returns the client IP address.
     * 
     * @return string
     * 
     */
    public function getIp()
    {
        return $this->ip;
    }
    
    public function getUserAgent()
    {
        return $this->user_agent;
    }
    
    public function getReferer()
    {
        return $this->referer;
    }
    
    /**
     *  
     * Is the client a mobile device?
     * 
     * @return bool
     * 
     */
    public function isMobile()
    {
        if ($this->device['mobile'] === null) {
            $this->initDevice('mobile');
        }
        return $this->device['mobile'];
    }

    /**
     *  
     * Is the client a crawler?
     * 
     * @return bool
     * 
     */
    public function isCrawler()
    {
        if ($this->device['crawler'] === null) {
            $this->initDevice('crawler');
        }
        return $this->device['crawler'];
    }
    
    /**
     * 
     * Is the client making an XmlHttpRequest?
     * 
     * @return bool
     * 
     */
    public function isXhr()
    {
        return $this->xhr;
    }
}
