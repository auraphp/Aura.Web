<?php
namespace Aura\Web\Request;

class Content
{
    protected $decoders = array(
        'application/json' => 'json_decode',
        'application/x-www-form-urlencoded' => 'parse_str',
    );
    
    protected $type;
    
    protected $value;
    
    protected $raw;
    
    public function __construct(
        array $server,
        array $decoders = array()
    ) {
        $this->type = isset($server['HTTP_CONTENT_TYPE'])
                    ? strtolower($server['HTTP_CONTENT_TYPE'])
                    : null;
        
        $this->decoders = array_merge($this->decoders, $decoders);
    }
    
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
    
    public function getRaw()
    {
        if ($this->raw === null) {
            $this->raw = file_get_contents('php://input');
        }
        return $this->raw;
    }
    
    public function getType()
    {
        return $this->type;
    }
}
