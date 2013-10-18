<?php
namespace Aura\Web\Request;

class Client
{
    protected $forwarded_for = array();
    protected $mobile;
    protected $crawler;
    protected $ip;
    protected $referer;
    protected $user_agent;
    
    /**
     * 
     * User-Agent strings used in matching mobile clients.
     *
     * @see isMobile()
     * 
     * @var array
     * 
     */
    protected $agents = array(
        'mobile' => array(
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
        ),
        'crawler' => array(
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
        ),
    );

    public function __construct(
        array $server,
        array $agents = array()
    ) {
        $this->agents = array_merge_recursive($this->agents, $agents);

        if (isset($server['REMOTE_ADDR'])) {
            $this->ip = $server['REMOTE_ADDR'];
        }
        
        if (isset($server['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $server['HTTP_X_FORWARDED_FOR']);
            foreach ($ips as $ip) {
                $this->forwarded_for[] = trim($ip);
            }
            $this->ip = $this->forwarded_for[0];
        }
        
        $this->user_agent = isset($server['HTTP_USER_AGENT'])
                          ? $server['HTTP_USER_AGENT']
                          : null;
        
        $this->referer = isset($server['HTTP_REFERER'])
                       ? $server['HTTP_REFERER']
                       : null;
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
    
    public function getReferer()
    {
        return $this->referer;
    }
    
    public function getUserAgent()
    {
        return $this->user_agent;
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
        if ($this->crawler === null) {
            $this->crawler = $this->matchAgent('crawler');
        }
        return $this->crawler;
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
        if ($this->mobile === null) {
            $this->mobile = $this->matchAgent('mobile');
        }
        return $this->mobile;
    }

    protected function matchAgent($key)
    {
        foreach ($this->agents[$key] as $regex) {
            $regex = preg_quote($regex);
            $match = preg_match("/$regex/i", $this->user_agent);
            if ($match) {
                return true;
            }
        }
        return false;
    }
}
