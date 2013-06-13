<?php
namespace Aura\Web\Request;

class Content
{
    protected $decoders = [
        'application/json' => 'json_decode',
        'application/x-www-form-urlencoded' => 'parse_str',
    ];
    
    protected $type;
    
    protected $value;
    
    protected $raw;
    
    public function __construct(
        array $server,
        array $decoders = []
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
            if (isset($this->decoders[$type])) {
                $decode = $this->decoders[$type];
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
