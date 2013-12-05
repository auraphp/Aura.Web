<?php
namespace Aura\Web\Request\Accept;

class Media extends AbstractValues
{
    protected $server_key = 'HTTP_ACCEPT';
    
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
     * @param array $server A copy of $_SERVER.
     */
    public function __construct(array $server = array(), array $types = array())
    {
        parent::__construct($server);
        
        // merge the media type maps
        $this->types = array_merge($this->types, $types);
        
        // override the media if a file extension exists in the path
        $request_uri = isset($server['REQUEST_URI'])
                     ? $server['REQUEST_URI']
                     : null;
        $path   = parse_url('http://example.com/' . $request_uri, PHP_URL_PATH);
        $name   = basename($path);
        $ext    = strrchr($name, '.');
        if ($ext && isset($this->types[$ext])) {
            $this->setValues($this->types[$ext]);
        }
    }
}
