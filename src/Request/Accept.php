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

use Aura\Web\Request\Accept\Charset;
use Aura\Web\Request\Accept\Encoding;
use Aura\Web\Request\Accept\Language;
use Aura\Web\Request\Accept\Media;

/**
 * Trying real hard to adhere to <http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html>.
 */
class Accept
{
    /**
     * 
     * The `Accept` header values as an array sorted by quality level.
     * 
     * @var Media
     * 
     */
    protected $media;
    
    /**
     * 
     * The `Accept-Charset` header values as an array sorted by quality level.
     *
     * @var Charset
     * 
     */
    protected $charset;
    
    /**
     * 
     * The `Accept-Encoding` header values as an array sorted by quality level.
     *
     * @var Encoding
     * 
     */
    protected $encoding;
    
    /**
     * 
     * The `Accept-Language` header values as an array sorted by quality level.
     *
     * @var Language
     * 
     */
    protected $language;
    
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
        Charset $charset,
        Encoding $encoding,
        Language $language,
        Media $media
    ) {
        $this->media    = $media;
        $this->charset  = $charset;
        $this->encoding = $encoding;
        $this->language = $language;
    }
    
    /**
     * 
     * Returns a property object by name.
     * 
     * @param string $key The property name.
     * 
     * @return object The property object.
     * 
     */
    public function __get($key)
    {
        return $this->$key;
    }
}
