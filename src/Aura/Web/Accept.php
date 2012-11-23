<?php
namespace Aura\Web;

class Accept
{
    protected $values = [
        'HTTP_ACCEPT'          => [],
        'HTTP_ACCEPT_CHARSET'  => [],
        'HTTP_ACCEPT_ENCODING' => [],
        'HTTP_ACCEPT_LANGUAGE' => [],
    ];
    
    public function __construct(array $server)
    {
        $keys = array_keys($this->values);
        foreach ($server as $key => $val) {
            if (in_array($key, $keys)) {
                $this->values[$key] = $this->parse($val);
            }
        }
    }
    
    public function getContentType()
    {
        return $this->values['HTTP_ACCEPT'];
    }
    
    public function getCharset()
    {
        return $this->values['HTTP_ACCEPT_CHARSET'];
    }
    
    public function getEncoding()
    {
        return $this->values['HTTP_ACCEPT_ENCODING'];
    }
    
    public function getLanguage()
    {
        return $this->values['HTTP_ACCEPT_LANGUAGE'];
    }
    
    /**
     * 
     * Parse an HTTP `Accept*` header and sort by the quality factor, the 
     * highest being first in the returned array. The returned data is 
     * unfiltered.
     * 
     * @param string $value The value of the accept header to parse.
     * 
     * @return array
     * 
     */
    protected function parse($values)
    {
        $values = explode(',', $values);
        $sorted = [];

        foreach ((array) $values as $value) {
            $value = trim($value);
            if (false === strpos($value, ';q=')) {
                $sorted[$value]  = 1.0;
            } else {
                list($value, $q) = explode(';q=', $value);
                $sorted[$value]  = (float) $q;
            }
        }

        // sort by quality factor, highest first.
        arsort($sorted);
        return $sorted;
    }
}