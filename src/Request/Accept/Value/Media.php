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
namespace Aura\Web\Request\Accept\Value;

/**
 * 
 * Represents an acceptable media type value.
 * 
 * @package Aura.Web
 * 
 */
class Media extends AbstractValue
{
    /**
     * 
     * The media type.
     * 
     * @var string
     * 
     */
    protected $type = '*';
    
    /**
     * 
     * The media subtype.
     * 
     * @var string
     * 
     */
    protected $subtype = '*';

    /**
     * 
     * Finishes construction of the value object.
     * 
     * @return null
     * 
     */
    protected function init()
    {
        list($this->type, $this->subtype) = explode('/', $this->value);
    }
    
    /**
     * 
     * Returns the media type.
     * 
     * @return string
     * 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 
     * Returns the media subtype.
     * 
     * @return string
     * 
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    public function isWildcard()
    {
        return $this->value == '*/*';
    }
    
    /**
     * 
     * Checks if an available media type value matches this acceptable value.
     * 
     * @param Media $avail An available media type value.
     * 
     * @return True on a match, false if not.
     * 
     */
    public function match(Media $avail)
    {
        // is it a full match?
        if (strtolower($this->value) == strtolower($avail->getValue())) {
            return $this->matchParameters($avail);
        }
        
        // is it a type match?
        return $this->subtype == '*'
            && strtolower($this->type) == strtolower($avail->getType())
            && $this->matchParameters($avail);
    }
}
