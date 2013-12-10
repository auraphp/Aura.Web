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
namespace Aura\Web\Request\Accept\Value;

/**
 * 
 * A factory to create value objects.
 * 
 * @package Aura.Web
 * 
 */
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
