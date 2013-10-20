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
 * The raw body of the request
 * 
 * @package Aura.Web
 * 
 */
class Content
{
    /**
     * 
     * @var array built in decoders
     * 
     */
    protected $decoders = array(
        'application/json' => 'json_decode',
        'application/x-www-form-urlencoded' => 'parse_str',
    );
    
    /**
     * 
     * @var string
     * 
     */
    protected $type;
    
    /**
     * 
     * @var mixed
     * 
     */
    protected $value;
    
    /**
     * 
     * @var mixed
     * 
     */
    protected $raw;
    
    /**
     * 
     * Constructor
     * 
     * @param array $server 
     * 
     * @param array $decoders
     * 
     */
    public function __construct(
        array $server,
        array $decoders = array()
    ) {
        $this->type = isset($server['HTTP_CONTENT_TYPE'])
                    ? strtolower($server['HTTP_CONTENT_TYPE'])
                    : null;
        
        $this->decoders = array_merge($this->decoders, $decoders);
    }
    
    /**
     * 
     * Request body after decoding it based on the content type
     * 
     * @return string The request body after decoding it based on the content type
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
     * The raw request body
     * 
     * @return string Raw request body
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
     * the content-type of the request body
     * 
     * @return string
     * 
     */
    public function getType()
    {
        return $this->type;
    }
}
