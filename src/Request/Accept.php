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
 * Trying real hard to adhere to <http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html>.
 * 
 * Rename this to $accept and change getMedia() etc to negotiateMedia() ? Or
 * make getMedia() return the array, and getMedia($available) return the
 * negotiated value?
 * 
 * @todo figure out what to do when matching to * when the result has an explicit q=0 value.
 * @todo identity encoding is always acceptable unless set explictly to q=0
 */
class Accept
{
    /**
     * 
     * The `Accept` header values as an array sorted by quality level.
     * 
     * @var array
     * 
     */
    protected $accept = array();
    
    /**
     * 
     * The `Accept-Charset` header values as an array sorted by quality level.
     * 
     * @var array
     * 
     */
    protected $accept_charset = array();
    
    /**
     * 
     * The `Accept-Encoding` header values as an array sorted by quality level.
     * 
     * @var array
     * 
     */
    protected $accept_encoding = array();
    
    /**
     * 
     * The `Accept-Language` header values as an array sorted by quality level.
     * 
     * @var array
     * 
     */
    protected $accept_language = array();
    
    /**
     * 
     * A map of file .extensions to Content-Type values.
     * 
     * @var array
     * 
     */
    protected $types = array(
        '.aif'      => 'audio/x-aiff',
        '.aifc'     => 'audio/x-aiff',
        '.aiff'     => 'audio/x-aiff',
        '.asf'      => 'video/x-ms-asf',
        '.asr'      => 'video/x-ms-asf',
        '.asx'      => 'video/x-ms-asf',
        '.atom'     => 'application/atom+xml',
        '.au'       => 'audio/basic',
        '.avi'      => 'video/x-msvideo',
        '.bas'      => 'text/plain',
        '.bmp'      => 'image/bmp',
        '.c'        => 'text/plain',
        '.css'      => 'text/css',
        '.csv'      => 'text/plain',
        '.dtd'      => 'application/xml-dtd',
        '.etx'      => 'text/x-setext',
        '.flr'      => 'x-world/x-vrml',
        '.gif'      => 'image/gif',
        '.gz'       => 'application/x-gzip',
        '.h'        => 'text/plain',
        '.htm'      => 'text/html',
        '.html'     => 'text/html',
        '.ico'      => 'image/x-icon',
        '.jfif'     => 'image/pipeg',
        '.jpe'      => 'image/jpeg',
        '.jpeg'     => 'image/jpeg',
        '.jpg'      => 'image/jpeg',
        '.js'       => 'text/javascript',
        '.json'     => 'application/json',
        '.lsf'      => 'video/x-la-asf',
        '.lsx'      => 'video/x-la-asf',
        '.m3u'      => 'audio/x-mpegurl',
        '.mid'      => 'audio/mid',
        '.mov'      => 'video/quicktime',
        '.movie'    => 'video/x-sgi-movie',
        '.mp2'      => 'video/mpeg',
        '.mp3'      => 'audio/mpeg',
        '.mpa'      => 'video/mpeg',
        '.mpe'      => 'video/mpeg',
        '.mpeg'     => 'video/mpeg',
        '.mpg'      => 'video/mpeg',
        '.mpv2'     => 'video/mpeg',
        '.ogg'      => 'application/ogg',
        '.pbm'      => 'image/x-portable-bitmap',
        '.pdf'      => 'application/pdf',
        '.pgm'      => 'image/x-portable-graymap',
        '.png'      => 'image/png',
        '.pnm'      => 'image/x-portable-anymap',
        '.ppm'      => 'image/x-portable-pixmap',
        '.ps'       => 'application/postscript',
        '.qt'       => 'video/quicktime',
        '.ra'       => 'audio/x-pn-realaudio',
        '.ram'      => 'audio/x-pn-realaudio',
        '.ras'      => 'image/x-cmu-raster',
        '.rdf'      => 'application/rdf+xml',
        '.rgb'      => 'image/x-rgb',
        '.rmi'      => 'audio/mid',
        '.rss'      => 'application/rss+xml',
        '.rss2'     => 'application/rss+xml',
        '.rtf'      => 'application/rtf',
        '.snd'      => 'audio/basic',
        '.svg'      => 'image/svg+xml',
        '.text'     => 'text/plain',
        '.tif'      => 'image/tiff',
        '.tiff'     => 'image/tiff',
        '.tsv'      => 'text/plain',
        '.txt'      => 'text/plain',
        '.vcf'      => 'text/x-vcard',
        '.vrml'     => 'x-world/x-vrml',
        '.wav'      => 'audio/x-wav',
        '.wrl'      => 'x-world/x-vrml',
        '.wrz'      => 'x-world/x-vrml',
        '.xaf'      => 'x-world/x-vrml',
        '.xbm'      => 'image/x-xbitmap',
        '.xht'      => 'application/xhtml+xml',
        '.xhtml'    => 'application/xhtml+xml',
        '.xml'      => 'application/xml',
        '.xof'      => 'x-world/x-vrml',
        '.xpm'      => 'image/x-xpixmap',
        '.xwd'      => 'image/x-xwindowdump',
        '.zip'      => 'application/zip',
    );
    
    /**
     * 
     * Constructor.
     * 
     * @param array $server An array of $_SERVER values.
     * 
     * @param array $types Additional extension to Content-Type mappings.
     * 
     */
    public function __construct(array $server, array $types = array())
    {
        // merge the media type maps
        $this->types = array_merge($this->types, $types);
        
        // set the "accept" properties
        $this->accept          = $this->qualitySort($server, 'HTTP_ACCEPT');
        $this->accept_charset  = $this->qualitySort($server, 'HTTP_ACCEPT_CHARSET');
        $this->accept_encoding = $this->qualitySort($server, 'HTTP_ACCEPT_ENCODING');
        $this->accept_language = $this->qualitySort($server, 'HTTP_ACCEPT_LANGUAGE');
        
        // fix the "accept" properties
        $this->fixAccept($server);
        $this->fixAcceptCharset();
    }
    
    /**
     * 
     * Sorts an Accept header value set according to quality levels.
     * 
     * This is an unusual sort. Normally we'd think a reverse-sort would
     * order the array by q values from 1 to 0, but the problem is that
     * an implicit 1.0 on more than one value means that those values will
     * be reverse from what the header specifies, which seems unexpected
     * when negotiating later.
     * 
     * @param array $server An array of $_SERVER values.
     * 
     * @param string $key The key to look up in $_SERVER.
     * 
     * @return array An array of values sorted by quality level.
     * 
     */
    protected function qualitySort($server, $key)
    {
        if (! isset($server[$key])) {
            return array();
        }
        
        $raw    = $server[$key];
        $var    = array();
        $bucket = array();
        $values = explode(',', $raw);
        
        // sort into q-value buckets
        foreach ($values as $value) {
            $value = trim($value);
            if (strpos($value, ';q=') === false) {
                $bucket['1.0'][] = $value;
            } else {
                list($value, $q) = explode(';q=', $value);
                $bucket[$q][] = $value;
            }
        }
        
        // reverse-sort the buckets so that q=1 is first and q=0 is last,
        // but the values in the buckets stay in the original order.
        krsort($bucket);
        
        // flatten the buckets into the var
        foreach ($bucket as $q => $values) {
            foreach ($values as $value) {
                $var[$value] = (float) $q;
            }
        }
        
        return $var;
    }
    
    /**
     * 
     * Modify the $accept property based on a URI file extension.
     * 
     * @param array $server An array of $_SERVER values.
     * 
     * @return null
     * 
     */
    protected function fixAccept($server)
    {
        // override the accept media if a file extension exists in the path
        $request_uri = isset($server['REQUEST_URI'])
                     ? $server['REQUEST_URI']
                     : null;
        $path   = parse_url('http://example.com/' . $request_uri, PHP_URL_PATH);
        $name   = basename($path);
        $ext    = strrchr($name, '.');
        if ($ext && isset($this->types[$ext])) {
            $this->accept = array($this->types[$ext] => 1.0);
        }
    }
    
    /**
     * 
     * Modify the $accept_charset property for ISO-8859-1 acceptability.
     * 
     * @return null
     * 
     */
    protected function fixAcceptCharset()
    {
        // no charset values were specified
        if (! $this->accept_charset) {
            return;
        }
        
        // look for ISO-8859-1, case insensitive
        foreach ($this->accept_charset as $charset => $q) {
            if (strtolower($charset) == 'iso-8859-1') {
                return;
            }
        }
        
        // charset iso-8859-1 is acceptable if not explictly mentioned
        $this->accept_charset = array_merge(
            array('ISO-8859-1' => 1.0),
            $this->accept_charset
        );
    }
    
    /**
     * 
     * Returns the value of the `Accept` header as an array sorted by quality
     * level.
     * 
     * @return array
     * 
     */
    public function getAccept()
    {
        return $this->accept;
    }
    
    /**
     * 
     * Returns the value of the `Accept-Charset` header as an array sorted by quality
     * level.
     * 
     * @return array
     * 
     */
    public function getAcceptCharset()
    {
        return $this->accept_charset;
    }
    
    /**
     * 
     * Returns the value of the `Accept-Encoding` header as an array sorted by quality
     * level.
     * 
     * @return array
     * 
     */
    public function getAcceptEncoding()
    {
        return $this->accept_encoding;
    }
    
    /**
     * 
     * Returns the value of the `Accept-Language` header as an array sorted by quality
     * level.
     * 
     * @return array
     * 
     */
    public function getAcceptLanguage()
    {
        return $this->accept_language;
    }
    
    /**
     * 
     * Returns a charset negotiated between acceptable and available values.
     * 
     * @param array $available Available values in preference order.
     * 
     * @return string|bool The negotiated value, or false if negotiation
     * failed.
     * 
     */
    public function getCharset(array $available = array())
    {
        if (! $available) {
            return false;
        }
        
        // get acceptable charsets
        $acceptable = $this->accept_charset;
        
        // if no acceptable charset specified, use first available
        if (! $acceptable) {
            return $available[0];
        }
        
        // normalize for comparisons
        list($norm_accept, $norm_avail) = $this->normalize(
            $acceptable,
            $available
        );
        
        // loop through acceptable charsets
        foreach ($norm_accept as $charset => $q) {
            
            // if the acceptable quality is zero, skip it
            if (! $q) {
                continue;
            }
            
            // if acceptable charset is *, return the first available
            if ($charset == '*') {
                return $available[0];
            }
            
            // if acceptable charset is available, use it
            foreach ($norm_avail as $key => $avail) {
                if ($charset == $avail) {
                    return $available[$key];
                }
            }
        }
        
        return false;
    }
    
    /**
     * 
     * Returns an encoding negotiated between acceptable and available values.
     * 
     * @param array $available Available values in preference order.
     * 
     * @return string|bool The negotiated value, or false if negotiation
     * failed.
     * 
     */
    public function getEncoding(array $available = array())
    {
        if (! $available) {
            return false;
        }
        
        // get acceptable encodings
        $acceptable = $this->accept_encoding;
        
        // if no acceptable encoding specified, use first available
        if (! $acceptable) {
            return $available[0];
        }
        
        // normalize for comparisons
        list($norm_accept, $norm_avail) = $this->normalize(
            $acceptable,
            $available
        );
        
        // loop through acceptable encodings
        foreach ($norm_accept as $encoding => $q) {
            
            // if the acceptable quality is zero, skip it
            if (! $q) {
                continue;
            }
            
            // if acceptable encoding is *, return the first available
            if ($encoding == '*') {
                return $available[0];
            }
            
            // if acceptable encoding is available, use it
            foreach ($norm_avail as $key => $avail) {
                if ($encoding == $avail) {
                    return $available[$key];
                }
            }
        }
        
        return false;
    }
    
    /**
     * 
     * Returns a language negotiated between acceptable and available values.
     * 
     * @param array $available Available values in preference order.
     * 
     * @return string|bool The negotiated value, or false if negotiation
     * failed.
     * 
     */
    public function getLanguage(array $available = array())
    {
        if (! $available) {
            return false;
        }
        
        // get acceptable language
        $acceptable = $this->accept_language;
        
        // if no acceptable language specified, use first available
        if (! $acceptable) {
            return $available[0];
        }
        
        // normalize for comparisons
        list($norm_accept, $norm_avail) = $this->normalize(
            $acceptable,
            $available
        );
        
        // loop through acceptable languages
        foreach ($norm_accept as $language => $q) {
            
            // if the acceptable quality is zero, skip it
            if (! $q) {
                continue;
            }
            
            // if acceptable language is *, return the first available
            if ($language == '*') {
                return $available[0];
            }
            
            // go through the available values and find what's acceptable.
            // force an ending dash on the language; ignored if subtype is
            // already present, avoids "undefined offset" error when not.
            list($language_type, $language_subtype) = explode('-', $language . '-');
            foreach ($norm_avail as $key => $avail) {
                if (! $language_subtype) {
                    // accept any subtype of a language
                    list($avail_type, $avail_subtype) = explode('-', $avail);
                    if ($language_type == $avail_type) {
                        // type match (subtype ignored)
                        return $available[$key];
                    }
                } elseif ($language == $avail) {
                    // type and subtype match
                    return $available[$key];
                }
            }
        }
        
        return false;
    }
    
    /**
     * 
     * Returns a media type negotiated between acceptable and available values.
     * 
     * @param array $available Available values in preference order.
     * 
     * @return string|bool The negotiated value, or false if negotiation
     * failed.
     * 
     */
    public function getMedia(array $available = array())
    {
        if (! $available) {
            return false;
        }
        
        // get acceptable media
        $acceptable = $this->accept;
        
        // if no acceptable media specified, use first available
        if (! $acceptable) {
            return $available[0];
        }
        
        // normalize for comparisons
        list($norm_accept, $norm_avail) = $this->normalize(
            $acceptable,
            $available
        );
        
        // loop through acceptable media
        foreach ($norm_accept as $media => $q) {
            
            // if the acceptable quality is zero, skip it
            if (! $q) {
                continue;
            }
            
            // if acceptable media is */*, return the first available
            if ($media == '*/*') {
                return $available[0];
            }
            
            // go through the available values and find what's acceptable.
            // force an ending dash on the language; ignored if subtype is
            // already present, avoids "undefined offset" error when not.
            list($media_type, $media_subtype) = explode('/', $media . '/*');
            foreach ($norm_avail as $key => $avail) {
                if ($media == $avail) {
                    return $available[$key];
                }
                list($avail_type, $avail_subtype) = explode('/', $avail);
                if ($media_subtype == '*' && $media_type == $avail_type) {
                    return $available[$key];
                }
            }
        }
        
        return false;
    }
    
    /**
     * 
     * Normalized available and acceptable value arrays.
     * 
     * @param array $acceptable Acceptable values in preference order.
     * 
     * @param array $available Available values in preference order.
     * 
     * @return array An array where element 0 is the normalized acceptable
     * values and element 1 is the normalized available values.
     * 
     */
    public function normalize($acceptable, $available)
    {
        $normalized = array();
        foreach ($acceptable as $value => $q) {
            $value = strtolower($value);
            $normalized[$value] = $q;
        }
        $acceptable = $normalized;
        foreach ($available as $key => $val) {
            $available[$key] = strtolower($val);
        }
        return array($acceptable, $available);
    }
}
