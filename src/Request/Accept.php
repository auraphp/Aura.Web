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
        
        return $this->charset->negotiate($available);
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
        
        return $this->encoding->negotiate($available);
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
        
        return $this->language->negotiate($available);
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
        
        return $this->media->negotiate($available);
    }
}
