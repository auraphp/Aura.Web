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
namespace Aura\Web\Controller;

/**
 * 
 * Controller Interface
 * 
 * @package Aura.Web
 * 
 */
interface ControllerInterface
{
    public function getContext();
    
    public function getData();
    
    public function getParams();
    
    public function getResponse();
    
    public function getSignal();
    
    public function exec();
}
