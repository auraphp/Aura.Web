<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\web;

/**
 * 
 * Retains information about the controller results for transfer to an actual
 * HTTP response object.
 * 
 * @package aura.web
 * 
 */
class Transfer
{
    /**
     * 
     * The body content of the HTTP response.
     * 
     * @var string
     * 
     */
    protected $content;
    
    /**
     * 
     * Cookies to be sent with the HTTP response.
     * 
     * @var array
     * 
     */
    protected $cookies;
    
    /**
     * 
     * Data for the view.
     * 
     * @var \ArrayObject
     * 
     */
    protected $data;
    
    /**
     * 
     * The view format to use.
     * 
     * @var string
     * 
     */
    protected $format;
    
    /**
     * 
     * Non-cookie headers to be sent with the HTTP response.
     * 
     * @var \ArrayObject
     * 
     */
    protected $headers;
    
    /**
     * 
     * The name of the layout template to use in a 2-step view.
     * 
     * @var string
     * 
     */
    protected $layout;
    
    /**
     * 
     * The name of the content variable in the layout template.
     * 
     * @var string
     * 
     */
    protected $layout_content_var = 'layout_content';
    
    /**
     * 
     * The status code for the HTTP response.
     * 
     * @var int
     * 
     */
    protected $status_code = 200;
    
    /**
     * 
     * The status text for the HTTP response.
     * 
     * @var string
     * 
     */
    protected $status_text = 'OK';
    
    /**
     * 
     * The name of the core template to use in a 2-step view.
     * 
     * @var string
     * 
     */
    protected $view;
    
    /**
     * 
     * Constructor.
     * 
     */
    public function __construct()
    {
        $this->cookies     = new \ArrayObject;
        $this->data        = new \ArrayObject;
        $this->headers     = new \ArrayObject;
    }
    
    /**
     * 
     * Magic read for protected properties.
     * 
     * @param string $key The property to get.
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
     * Magic write for protected properties.
     * 
     * @param string $key The property to set.
     * 
     * @param mixed $val Set to this value.
     * 
     * @return void
     * 
     */
    public function __set($key, $val)
    {
        $this->$key = $val;
    }
}
