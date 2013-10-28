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

/**
 * 
 * Information about the client.
 * 
 * @package Aura.Web
 * 
 */
class Client
{
    /**
     * 
     * The list of 'X-Forwarded-For' values.
     * 
     * @var array
     * 
     */
    protected $forwarded_for = array();
    
    /**
     * 
     * Is the 'User-Agent' recognized as a mobile agent?
     * 
     * @var bool
     * 
     */
    protected $mobile = null;
    
    /**
     * 
     * Is the 'User-Agent' recognizes as a crawler robot?
     * 
     * @var bool
     * 
     */
    protected $crawler;
    
    /**
     * 
     * The client IP address.
     * 
     * @var string
     * 
     */
    protected $ip;
    
    /**
     * 
     * The 'Referer' value.
     * 
     * @var string
     * 
     */
    protected $referer;
    
    /**
     * 
     * The 'User-Agent' string.
     * 
     * @var string
     * 
     */
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
    protected $mobile_agents = array(
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
    );
    
    /**
     * 
     * User-Agent strings used in matching crawler robot clients.
     *
     * @see isCrawler()
     * 
     * @var array
     * 
     */
    protected $crawler_agents = array(
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
    );

    /**
     * 
     * Constructor.
     * 
     * @param array $server An array of $_SERVER values.
     * 
     * @param array $mobile_agents Additional mobile agent strings.
     * 
     * @param array $crawler_agents Additiona crawler agent strings.
     * 
     */
    public function __construct(
        array $server,
        array $mobile_agents = array(),
        array $crawler_agents = array()
    ) {
        $this->mobile_agents = array_merge(
            $this->mobile_agents,
            $mobile_agents
        );

        $this->crawler_agents = array_merge(
            $this->crawler_agents,
            $crawler_agents
        );

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

    /**
     * 
     * Returns the values of the `X-Forwarded-For` headers as an array.
     * 
     * @return array
     * 
     */
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
    
    /**
     * 
     * Returns the value of the 'Referer' header.
     * 
     * @return string
     * 
     */
    public function getReferer()
    {
        return $this->referer;
    }
    
    /**
     * 
     * Returns the value of the 'User-Agent' header.
     * 
     * @return string
     * 
     */
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
            $this->crawler = false;
            foreach ($this->crawler_agents as $regex) {
                $regex = preg_quote($regex);
                if (preg_match("/$regex/i", $this->user_agent)) {
                    $this->crawler = true;
                    break;
                }
            }
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
            $this->mobile = false;
            foreach ($this->mobile_agents as $regex) {
                $regex = preg_quote($regex);
                if (preg_match("/$regex/i", $this->user_agent)) {
                    $this->mobile = true;
                    break;
                }
            }
        }
        return $this->mobile;
    }
}
