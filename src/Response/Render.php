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
     * Data to be rendered into the view and layout.
     * 
     * @var array
     * 
     */
    protected $data = array();
    
    /**
     * 
     * The name of the layout to wrap around the view.
     * 
     * @var string
     * 
     */
    protected $layout;
    
    /**
     * 
     * The stack of locations for layouts.
     * 
     * @var array
     * 
     */
    protected $layout_stack = array();
    
    /**
     * 
     * The view to render data into.
     * 
     * @var string
     * 
     */
    protected $view;
    
    /**
     * 
     * The stack of locations for views.
     * 
     * @var array
     * 
     */
    protected $view_stack = array();
    
    /**
     * 
     * Returns object properties by name.
     * 
     * @param string $key The property to return.
     * 
     * @return mixed The property.
     * 
     */
    public function __get($key)
    {
        return $this->$key;
    }
    
    /**
     * 
     * Sets object properties by name.
     * 
     * @param string $key The object property to set.
     * 
     * @param string $val The value to set it to.
     * 
     * @return null
     * 
     */
    public function __set($key, $val)
    {
        $this->$key = $val;
    }
}
