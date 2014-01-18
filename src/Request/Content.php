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

/**
 * 
 * Representation of the request content.
 * 
 * @package Aura.Web
 * 
 */
class Content
{
    /**
     * 
     * Content decoder callables.
     * 
     * @var array
     * 
     */
    protected $decoders = array(
        'application/json' => 'json_decode',
        'application/x-www-form-urlencoded' => 'parse_str',
    );
    
    /**
     * 
     * The value of the Content-Type header.
     * 
     * @var string
     * 
     */
    protected $type;
    
    /**
     * 
     * The value of the Content-Length header.
     * 
     * @var int
     * 
     */
    protected $length;
    
    /**
     * 
     * The value of the Content-MD5 header.
     * 
     * @var int
     * 
     */
    protected $md5;
    
    /**
     * 
     * The decoded content.
     * 
     * @var mixed
     * 
     */
    protected $value;
    
    /**
     * 
     * The raw content.
     * 
     * @var mixed
     * 
     */
    protected $raw;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $server An array of $_SERVER values.
     * 
     * @param array $decoders Additional content decoder callables.
     * 
     */
    public function __construct(
        array $server,
        array $decoders = array()
    ) {
        $this->type = $this->getHeaderValue($server, 'CONTENT_TYPE');
        
        $this->length = $this->getHeaderValue($server, 'CONTENT_LENGTH');
        
        $this->md5 = $this->getHeaderValue($server, 'CONTENT_MD5');
        
        $this->decoders = array_merge($this->decoders, $decoders);
    }
    
    /**
     * 
     * @param array $server An array of $_SERVER values.
     * 
     * @param string $key CONTENT_TYPE, CONTENT_LENGTH, CONTENT_MD5
     * 
     * @return string null / value if any
     * 
     */
    protected function getHeaderValue(array $server, $key)
    {
        if (isset($server[$key])) {
            $value = strtolower($server[$key]);
        } elseif (isset($server['HTTP_' . $key])) {
            $value = strtolower($server['HTTP_' . $key]);
        } else {
            $value = null;
        }
        return $value;
    }

    /**
     * 
     * Request body after decoding it based on the content type.
     * 
     * @return string The decoded request body.
     * 
     */
    public function get()
    {
        if ($this->value === null) {
            $this->value = $this->getRaw();
            if (isset($this->decoders[$this->type])) {
                $decode = $this->decoders[$this->type];
                $this->value = $decode($this->value);
            }
        }
        
        return $this->value;
    }
    
    /**
     * 
     * The raw request body.
     * 
     * @return string Raw request body.
     * 
     */
    public function getRaw()
    {
        if ($this->raw === null) {
            $this->raw = file_get_contents('php://input');
        }
        return $this->raw;
    }
    
    /**
     * 
     * The content-type of the request body.
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
     * The content-length of the request body.
     * 
     * @return string
     * 
     */
    public function getLength()
    {
        return $this->length;
    }
    
    /**
     * 
     * The MD5 of the request body.
     * 
     * @return string
     * 
     */
    public function getMd5()
    {
        return $this->md5;
    }
}
