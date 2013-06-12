<?php
namespace Aura\Web\Request;

class Headers
{
    protected $data = [];
    
    public function __construct(array $server)
    {
        foreach ($server as $label => $value) {
            
            // keep only HTTP_* values
            if (substr($key, 0, 5) !== 'HTTP_') {
                continue;
            }
            
            // remove the HTTP_* prefix and normalize to lowercase
            $label = strtolower(substr($label, 5));
            
            // convert underscores to dashes
            $label = str_replace('_', '-', strtolower($label));
            
            // strip the label of control chars
            $label = preg_replace('/[\x00-\x1F]/', '', $label);

            // skip X-JSON headers
            if ($label == 'x-json') {
                continue;
            }
            
            // strip the value of control chars
            $value = preg_replace('/[\x00-\x1F]/', '', $value);
            
            // retain the header label and value
            $this->data[$label] = $value;
        }
    }
    
    public function get($key, $alt = null)
    {
        $key = strtolower($key);
        if (array_key_exists($this->data[$key])) {
            return $this->data[$key];
        }
        return $alt;
    }
}
