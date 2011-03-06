<?php
namespace aura\web;

class PhpStream
{
    protected $pos = 0;

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        return isset($GLOBALS['aura\web\PhpStream']);
    }

    public function stream_read($count)
    {
        $return = substr($GLOBALS['aura\web\PhpStream'], $this->pos, $count);
        $this->pos += strlen($return);
        
        return $return;
    }

    public function stream_eof()
    {
        return 0 == strlen($GLOBALS['aura\web\PhpStream']);
    }
    
    public function stream_stat()
    {
        return array();
    }
}