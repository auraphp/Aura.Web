<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @package Aura.Web
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web;

/**
 * 
 * Signal
 * 
 * @package Aura.Web
 * 
 */
class Signal implements SignalInterface
{
    /**
     * 
     * Handlers
     * 
     * @var array
     * 
     */
    public $handlers = [];
    
    // FIXME
    /**
     *
     * @param type $origin
     * 
     * @param type $signal
     * 
     * @param Callback $callback 
     * 
     */
    public function handler($origin, $signal, $callback)
    {
        $this->handlers[$signal] = $callback;
    }
    
    // FIXME
    /**
     *
     * @param type $origin
     * 
     * @param type $signal 
     * 
     */
    public function send($origin, $signal)
    {
        $args = func_get_args();
        array_pop($args);
        array_pop($args);
        $func = $this->handlers[$signal];
        call_user_func_array($func, $args);
    }
}
