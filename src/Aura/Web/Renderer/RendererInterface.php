<?php
namespace Aura\Web\Renderer;

use Aura\Web\Controller\ControllerInterface;

interface RendererInterface
{
    public function setController(ControllerInterface $controller);
    
    public function exec();
}
