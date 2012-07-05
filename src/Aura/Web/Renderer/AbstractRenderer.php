<?php
namespace Aura\Web\Renderer;

use Aura\Web\Controller\ControllerInterface;

abstract class AbstractRenderer implements RendererInterface
{
    protected $controller;
    
    public function setController(ControllerInterface $controller)
    {
        $this->controller = $controller;
        $this->prep();
    }
    
    abstract public function prep();
    
    abstract public function exec();
}
