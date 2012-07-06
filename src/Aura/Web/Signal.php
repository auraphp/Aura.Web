<?php
namespace Aura\Web;

class Signal implements SignalInterface
{
    public $handlers = [];
    
    public function handler($origin, $signal, $callback)
    {
        $this->handlers[$signal] = $callback;
    }
    
    public function send($origin, $signal)
    {
        $args = func_get_args();
        array_pop($args);
        array_pop($args);
        $func = $this->handlers[$signal];
        call_user_func_array($func, $args);
    }
}
