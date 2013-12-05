<?php
namespace Aura\Web\Request\Accept;

class Language extends AbstractValues
{
    protected $server_key = 'HTTP_ACCEPT_LANGUAGE';

    protected $value_type = 'language';

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
    public function negotiate(array $available)
    {
        if (! $available) {
            return false;
        }

        $set = clone $this;
        $set->setValues(array());
        foreach ($available as $language) {
            $set->addValues($language);
        }
        $available = $set;
        
        // get acceptable languages
        $acceptable = $this->values;
        
        // if no acceptable language specified, use first available
        if (count($acceptable) == 0) {
            return $available[0];
        }
        
        // loop through acceptable languages
        foreach ($acceptable as $language) {
            
            // if the acceptable quality is zero, skip it
            if ($language->getQuality() == 0) {
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
}
