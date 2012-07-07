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
interface SignalInterface
{
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
    public function handler($origin, $signal, $callback);
    
    // FIXME
    /**
     *
     * @param type $origin
     * 
     * @param type $signal 
     * 
     */
    public function send($origin, $signal);
}
