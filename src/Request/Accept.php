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
            $this->media->setValues($this->types[$ext]);
        }
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
        $set->setValues(array());
        foreach ($available as $charset) {
            $set->addValues($charset);
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
        $set->setValues(array());
        foreach ($available as $encoding) {
            $set->addValues($encoding);
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
        $set->setValues(array());
        foreach ($available as $language) {
            $set->addValues($language);
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
        $set->setValues(array());
        foreach ($available as $media_type) {
            $set->addValues($media_type);
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
