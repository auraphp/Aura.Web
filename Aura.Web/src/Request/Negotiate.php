<?php
namespace Aura\Web\Request;

/**
 * Trying real hard to adhere to <http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html>.
 * @todo figure out what to do when matching to * when the result has an explicit q=0 value.
 * @todo identity encoding is always acceptable unless set explictly to q=0
 */
class Negotiate
{
    protected $accept = [];
    protected $accept_charset = [];
    protected $accept_encoding = [];
    protected $accept_language = [];
    protected $types = [
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
    ];
    
    // this is messy and has a high CRAP score. refactor to methods.
    public function __construct(
        array $server,
        array $types = []
    ) {
        $this->types = array_merge($this->types, $types);
        
        $keys_vars = [
            'HTTP_ACCEPT'          => 'accept',
            'HTTP_ACCEPT_CHARSET'  => 'accept_charset',
            'HTTP_ACCEPT_ENCODING' => 'accept_encoding',
            'HTTP_ACCEPT_LANGUAGE' => 'accept_language',
        ];
        
        // this is an unusual sort. normally we'd think a reverse-sort would
        // order the arary by q values from 1 to 0, but the problem is that
        // an implicit 1.0 on more than one value means that those values will
        // be reverse from what the header specifies, which seems unexpected
        // when negotiating later.
        foreach ($keys_vars as $key => $var) {
            
            $raw = isset($server[$key])
                 ? $server[$key]
                 : null;
            
            if (! $raw) {
                continue;
            }
            
            $bucket = [];
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
            
            // flatten the buckets into the property
            foreach ($bucket as $q => $values) {
                foreach ($values as $value) {
                    $this->{$var}[$value] = (float) $q;
                }
            }
            
        }
        
        // override the accept media if a file extension exists in the path
        $request_uri = isset($server['REQUEST_URI'])
                     ? $server['REQUEST_URI']
                     : null;
        $path   = parse_url('http://example.com/' . $request_uri, PHP_URL_PATH);
        $name   = basename($path);
        $ext    = strrchr($name, '.');
        if ($ext && isset($this->types[$ext])) {
            $this->accept = [$this->types[$ext] => 1.0];
        }
        
        // fix charset
        if ($this->accept_charset) {
            // look for ISO-8859-1, case insensitive
            $found = false;
            foreach ($this->accept_charset as $charset => $q) {
                if (strtolower($charset) == 'iso-8859-1') {
                    $found = true;
                    break;
                }
            }
            // charset iso-8859-1 is acceptable if not explictly mentioned
            if (! $found) {
                $this->accept_charset = array_merge(
                    ['ISO-8859-1' => 1.0],
                    $this->accept_charset
                );
            }
        }
    }
    
    public function get(array $available)
    {
        $result = [];
        
        $base = [
            'charset'  => null,
            'encoding' => null,
            'language' => null,
            'media'    => null,
        ];
        
        $available = array_merge($base, $available);
        
        $list = [
            'charset'  => 'getCharset',
            'encoding' => 'getEncoding',
            'language' => 'getLanguage',
            'media'    => 'getMedia'
        ];
        
        foreach ($list as $key => $method) {
            $result[$key] = $this->$method($available[$key]);
        }
        
        return $result;
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
    
    public function getCharset(array $available = [])
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
    
    public function getEncoding(array $available = [])
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
    
    public function getLanguage(array $available = [])
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
    
    public function getMedia(array $available = [])
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
    
    public function normalize($acceptable, $available)
    {
        $normalized = [];
        foreach ($acceptable as $value => $q) {
            $value = strtolower($value);
            $normalized[$value] = $q;
        }
        $acceptable = $normalized;
        foreach ($available as $key => $val) {
            $available[$key] = strtolower($val);
        }
        return [$acceptable, $available];
    }
}
