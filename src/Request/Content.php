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
     * The content-type.
     * 
     * @var string
     * 
     */
    protected $type;
    
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
        $this->type = isset($server['HTTP_CONTENT_TYPE'])
                    ? strtolower($server['HTTP_CONTENT_TYPE'])
                    : null;
        
        $this->decoders = array_merge($this->decoders, $decoders);
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
}
