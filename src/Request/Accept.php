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

use Aura\Web\Request\Accept\Set as AcceptSet;

/**
 * Trying real hard to adhere to <http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html>.
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
     * @var \Aura\Web\Request\Accept\Set
     * 
     */
    protected $media;
    
    /**
     * 
     * The `Accept-Charset` header values as an array sorted by quality level.
     *
     * @var \Aura\Web\Request\Accept\Set
     * 
     */
    protected $charset;
    
    /**
     * 
     * The `Accept-Encoding` header values as an array sorted by quality level.
     *
     * @var \Aura\Web\Request\Accept\Set
     * 
     */
    protected $encoding;
    
    /**
     * 
     * The `Accept-Language` header values as an array sorted by quality level.
     *
     * @var \Aura\Web\Request\Accept\Set
     * 
     */
    protected $language;
    
    /**
     * 
     * A map of file .extensions to media types.
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
    public function __construct(
        AcceptSet $charset,
        AcceptSet $encoding,
        AcceptSet $language,
        AcceptSet $media,
        array $server,
        array $types = array()
    ) {
        // merge the media type maps
        $this->types = array_merge($this->types, $types);
        
        // set the properties
        $this->media    = $media;
        $this->charset  = $charset;
        $this->encoding = $encoding;
        $this->language = $language;
        
        // fix the properties
        $this->fixMedia($server);
        $this->fixCharset();
    }
    
    /**
     * 
     * Modify the $media property based on a URI file extension.
     * 
     * @param array $server An array of $_SERVER values.
     * 
     * @return null
     * 
     */
    protected function fixMedia($server)
    {
        // override the media if a file extension exists in the path
        $request_uri = isset($server['REQUEST_URI'])
                     ? $server['REQUEST_URI']
                     : null;
        $path   = parse_url('http://example.com/' . $request_uri, PHP_URL_PATH);
        $name   = basename($path);
        $ext    = strrchr($name, '.');
        if ($ext && isset($this->types[$ext])) {
            $this->media->setValues($this->types[$ext], 'HTTP_ACCEPT');
        }
    }
    
    /**
     * 
     * Modify the $charset property for ISO-8859-1 acceptability.
     * 
     * @return null
     * 
     */
    protected function fixCharset()
    {
        // no charset values were specified
        if (count($this->charset) == 0) {
            return;
        }
        
        // look for ISO-8859-1, case insensitive
        foreach ($this->charset as $charset) {
            if (strtolower($charset->getValue()) == 'iso-8859-1') {
                return;
            }
        }
        
        // charset iso-8859-1 is acceptable if not explictly mentioned
        $this->charset->addValues('ISO-8859-1', 'HTTP_ACCEPT_CHARSET');
    }
    
    /**
     * 
     * Returns the `Accept-Charset` value as an array; or, if available values
     * are passed, returns a negotiated value.
     * 
     * @param array $available Available values in preference order, if any.
     * 
     * @return mixed The header values as an array, or the negotiated value
     * (false indicates negotiation failed).
     * 
     */
    public function getCharset(array $available = null)
    {
        if ($available === null) {
            return $this->charset;
        }
        
        if (! $available) {
            return false;
        }

        $set = clone $this->charset;
        $set->setValues(array(), 'HTTP_ACCEPT_CHARSET');
        foreach ($available as $charset) {
            $set->addValues($charset, 'HTTP_ACCEPT_CHARSET');
        }
        $available = $set;
        
        // get acceptable charsets
        $acceptable = $this->charset;
        
        // if no acceptable charset specified, use first available
        if (count($acceptable) == 0) {
            return $available[0];
        }
        
        // loop through acceptable charsets
        foreach ($acceptable as $charset) {
            $value = strtolower($charset->getValue());
            
            // if the acceptable quality is zero, skip it
            if ($charset->getPriority() == 0) {
                continue;
            }
            
            // if acceptable charset is *, return the first available
            if ($value == '*') {
                return $available[0];
            }
            
            // if acceptable charset is available, use it
            foreach ($available as $avail) {
                if ($value == strtolower($avail->getValue())) {
                    return $avail;
                }
            }
        }
        
        return false;
    }
    
    /**
     * 
     * Returns an encoding negotiated between acceptable and available values.
     * 
     * @param array $available Available values in preference order, if any.
     * 
     * @return mixed The header values as an array, or the negotiated value
     * (false indicates negotiation failed).
     * 
     */
    public function getEncoding(array $available = null)
    {
        if ($available === null) {
            return $this->encoding;
        }
        
        if (! $available) {
            return false;
        }

        $set = clone $this->encoding;
        $set->setValues(array(), 'HTTP_ACCEPT_ENCODING');
        foreach ($available as $encoding) {
            $set->addValues($encoding, 'HTTP_ACCEPT_ENCODING');
        }
        $available = $set;

        // get acceptable encodings
        $acceptable = $this->encoding;
        
        // if no acceptable encoding specified, use first available
        if (count($acceptable) == 0) {
            return $available[0];
        }
        
        // loop through acceptable encodings
        foreach ($acceptable as $encoding) {
            $value = strtolower($encoding->getValue());
            
            // if the acceptable quality is zero, skip it
            if ($encoding->getPriority() == 0) {
                continue;
            }
            
            // if acceptable encoding is *, return the first available
            if ($value == '*') {
                return $available[0];
            }
            
            // if acceptable encoding is available, use it
            foreach ($available as $avail) {
                if ($value == strtolower($avail->getValue())) {
                    return $avail;
                }
            }
        }
        
        return false;
    }
    
    /**
     * 
     * Returns a language negotiated between acceptable and available values.
     * 
     * @param array $available Available values in preference order, if any.
     * 
     * @return mixed The header values as an array, or the negotiated value
     * (false indicates negotiation failed).
     * 
     */
    public function getLanguage(array $available = null)
    {
        if ($available === null) {
            return $this->language;
        }
        
        if (! $available) {
            return false;
        }

        $set = clone $this->language;
        $set->setValues(array(), 'HTTP_ACCEPT_LANGUAGE');
        foreach ($available as $language) {
            $set->addValues($language, 'HTTP_ACCEPT_LANGUAGE');
        }
        $available = $set;
        
        // get acceptable language
        $acceptable = $this->language;
        
        // if no acceptable language specified, use first available
        if (count($acceptable) == 0) {
            return $available[0];
        }
        
        // loop through acceptable languages
        foreach ($acceptable as $language) {
            
            // if the acceptable quality is zero, skip it
            if ($language->getPriority() == 0) {
                continue;
            }
            
            // if acceptable language is *, return the first available
            if ($language->getValue() == '*') {
                return $available[0];
            }
            
            // go through the available values and find what's acceptable.
            // force an ending dash on the language; ignored if subtype is
            // already present, avoids "undefined offset" error when not.
            foreach ($available as $avail) {
                if (! $language->getSubtype()) {
                    // accept any subtype of a language
                    if (strtolower($language->getType()) == strtolower($avail->getType())) {
                        // type match (subtype ignored)
                        return $avail;
                    }
                } elseif ($language->getValue() == $avail->getValue()) {
                    // type and subtype match
                    return $avail;
                }
            }
        }
        
        return false;
    }
    
    /**
     * 
     * Returns a media type negotiated between acceptable and available values.
     * 
     * @param array $available Available values in preference order, if any.
     * 
     * @return mixed The header values as an array, or the negotiated value
     * (false indicates negotiation failed).
     * 
     */
    public function getMedia(array $available = null)
    {
        if ($available === null) {
            return $this->media;
        }
        
        if (! $available) {
            return false;
        }

        $set = clone $this->media;
        $set->setValues(array(), 'HTTP_ACCEPT');
        foreach ($available as $media_type) {
            $set->addValues($media_type, 'HTTP_ACCEPT');
        }
        $available = $set;
        
        // get acceptable media
        $acceptable = $this->media;
        
        // if no acceptable media specified, use first available
        if (count($acceptable) == 0) {
            return $available[0];
        }

        // loop through acceptable media
        foreach ($acceptable as $media) {
            
            // if the acceptable quality is zero, skip it
            if ($media->getPriority() == 0) {
                continue;
            }
            
            // if acceptable media is */*, return the first available
            if ($media->getValue() == '*/*') {
                return $available[0];
            }
            
            // go through the available values and find what's acceptable.
            // force an ending dash on the language; ignored if subtype is
            // already present, avoids "undefined offset" error when not.
            $value = strtolower($media->getValue());
            foreach ($available as $avail) {
                if ($value == strtolower($avail->getValue())) {
                    return $avail;
                }
                if ($media->getSubtype() == '*' && $media->getType() == $avail->getType()) {
                    return $avail;
                }
            }
        }
        
        return false;
    }
}
