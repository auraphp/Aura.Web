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
namespace Aura\Web\Response;

class Render
{
    /**
     * 
     * @var array
     * 
     */
    protected $data;
    
    /**
     * 
     * @var string
     * 
     */
    protected $layout;
    
    /**
     * 
     * @var array
     * 
     */
    protected $layout_stack = array();
    
    /**
     * 
     * @var string
     * 
     */
    protected $view;
    
    /**
     * 
     * @var array
     * 
     */
    protected $view_stack = array();
    
    /**
     * 
     * Constructor
     * 
     * 
     */
    public function __construct()
    {
        $this->data = (object) array();
    }
    
    /**
     * 
     * Magic getter
     * 
     * @return mixed
     * 
     */
    public function __get($key)
    {
        return $this->$key;
    }
    
    /**
     * 
     * Magic setter
     * 
     * @param string $key
     * 
     * @param string $val
     * 
     */
    public function __set($key, $val)
    {
        $this->$key = $val;
    }
}
