<?php
namespace Aura\Web\Response;

class Render
{
    protected $data = [];
    
    protected $layout;
    
    protected $layout_stack = [];
    
    protected $view;
    
    protected $view_stack = [];
    
    public function __get($key)
    {
        return $this->$key;
    }
    
    public function __set($key, $val)
    {
        $this->$key = $val;
    }
}
