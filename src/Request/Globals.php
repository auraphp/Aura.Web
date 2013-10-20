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
namespace Aura\Web\Request;

class Globals
{
    /**
     * 
     * Cookies object
     * 
     * @var Values
     * 
     */
    protected $cookies;
    
    /**
     * 
     * @var Values
     * 
     */
    protected $env;
    
    /**
     * 
     * @var Files
     * 
     */
    protected $files;
    
    /**
     * 
     * @var Values
     * 
     */
    protected $post;
    
    /**
     * 
     * @var Values
     * 
     */
    protected $query;
    
    /**
     * 
     * @var Values
     * 
     */
    protected $server;

    /**
     * 
     * Constructor
     * 
     * @param Values $cookies
     * 
     * @param Values $env
     * 
     * @param Files $files
     * 
     * @param Values $post
     * 
     * @param Values $query
     * 
     * @param Values $server
     * 
     */
    public function __construct(
        Values $cookies,
        Values $env,
        Files  $files,
        Values $post,
        Values $query,
        Values $server
    ) {
        $this->cookies = $cookies;
        $this->env = $env;
        $this->files = $files;
        $this->post = $post;
        $this->query = $query;
        $this->server = $server;
    }

    /**
     * 
     * Magic method get
     * 
     * @return mixed
     * 
     */
    public function __get($key)
    {
        return $this->$key;
    }
}
