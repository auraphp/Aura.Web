<?php
namespace Aura\Web\Response;

class Render
{
    protected $data;
    
    protected $layout;
    
    protected $layout_stack = array();
    
    protected $view;
    
    protected $view_stack = array();
    
    public function __construct()
    {
        $this->data = (object) [];
    }
    
    public function __get($key)
    {
        return $this->$key;
    }
    
    public function __set($key, $val)
    {
        $this->$key = $val;
    }
}
