<?php
/**
 * 
 * This file is part of the Aura Project for PHP.
 * 
 * @package Aura.Web
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web\Renderer;

use Aura\Web\Controller\ControllerInterface;

/**
 * 
 * No method for action exception
 * 
 * @package Aura.Web
 * 
 */
abstract class AbstractRenderer implements RendererInterface
{
    protected $controller;
    
    /**
     * 
     * Set the controller
     *
     * @param ControllerInterface $controller 
     * 
     */
    public function setController(ControllerInterface $controller)
    {
        $this->controller = $controller;
        $this->prep();
    }
    
    abstract protected function prep();
    
    abstract public function exec();
}
