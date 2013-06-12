<?php
namespace Aura\Web\Request;

class Content
{
    protected $decode = [
        'application/json' => 'json_decode',
        'application/x-www-form-urlencoded' => 'parse_str',
    ];
    
    protected $type;
    
    protected $value;
    
    protected $raw;
    
    public function __construct($server, $decode = [])
    {
        $this->type = isset($server['HTTP_CONTENT_TYPE'])
                    ? strtolower($server['HTTP_CONTENT_TYPE'])
                    : null;
        
        $this->decode = array_merge($this->decode, $decode);
    }
    
    public function get()
    {
        if ($this->value === null) {
            $this->value = $this->getRaw();
            if (isset($this->decode[$type])) {
                $decode = $this->decode[$type];
                $this->value = $decode($this->value);
            }
        }
        
        return $this->value;
    }
    
    public function getRaw()
    {
        if ($this->raw === null) {
            $raw = file_get_contents('php://input');
        }
        return $this->raw;
    }
    
    public function getType()
    {
        return $this->type;
    }
}
