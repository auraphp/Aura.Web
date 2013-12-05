<?php
namespace Aura\Web\Request\Accept\Value;

class ValueFactory
{
    protected $map = array(
        'charset' => 'Aura\Web\Request\Accept\Value\Charset',
        'encoding' => 'Aura\Web\Request\Accept\Value\Encoding',
        'language' => 'Aura\Web\Request\Accept\Value\Language',
        'media' => 'Aura\Web\Request\Accept\Value\Media',
    );
    
    public function newInstance($type, $value, $quality, $params)
    {
        $class = $this->map[$type];
        return new $class($value, $quality, $params);
    }
}
