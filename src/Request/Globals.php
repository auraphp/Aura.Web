<?php
namespace Aura\Web\Request;

class Globals
{
    protected $cookies;
    protected $env;
    protected $files;
    protected $post;
    protected $query;
    protected $server;

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

    public function __get($key)
    {
        return $this->$key;
    }
}
