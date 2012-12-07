<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Web
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web;

/**
 * 
 * Collection point for information about the Accept headers.
 * 
 * @package Aura.Web
 * 
 */
class Accept
{
    /**
     * 
     * The various accept header values.
     * 
     * @var array
     * 
     */
    protected $values = [
        'HTTP_ACCEPT'          => [],
        'HTTP_ACCEPT_CHARSET'  => [],
        'HTTP_ACCEPT_ENCODING' => [],
        'HTTP_ACCEPT_LANGUAGE' => [],
    ];

    /**
     * 
     * Constructor.
     * 
     * @param array $server An array of $_SERVER information.
     * 
     */
    public function __construct(array $server)
    {
        $keys = array_keys($this->values);
        foreach ($server as $key => $val) {
            if (in_array($key, $keys)) {
                $this->values[$key] = $this->parse($val);
            }
        }
    }

    /**
     * 
     * Returns the acceptable content types as an array ordered by q-values.
     * 
     * @return array
     * 
     */
    public function getContentType()
    {
        return $this->values['HTTP_ACCEPT'];
    }

    /**
     * 
     * Returns the acceptable character sets as an array ordered by q-values.
     * 
     * @return array
     * 
     */
    public function getCharset()
    {
        return $this->values['HTTP_ACCEPT_CHARSET'];
    }

    /**
     * 
     * Returns the acceptable encodings as an array ordered by q-values.
     * 
     * @return array
     * 
     */
    public function getEncoding()
    {
        return $this->values['HTTP_ACCEPT_ENCODING'];
    }

    /**
     * 
     * Returns the acceptable languages as an array ordered by q-values.
     * 
     * @return array
     * 
     */
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
     * @param string $values The values of the accept header to parse.
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
