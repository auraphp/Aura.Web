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
namespace Aura\Web\Renderer;

use Aura\Web\Controller\ControllerInterface;

/**
 * 
 * An abstract renderer strategy.
 * 
 * @package Aura.Web
 * 
 */
abstract class AbstractRenderer implements RendererInterface
{
    /**
     * 
     * The controller being rendered.
     * 
     * @var ControllerInterface
     * 
     */
    protected $controller;

    /**
     * 
     * Sets the controller to be rendered.
     * 
     * @param ControllerInterface $controller The controller to be rendered.
     * 
     * @return void
     * 
     */
    public function setController(ControllerInterface $controller)
    {
        $this->controller = $controller;
    }

    /**
     * 
     * Executes the renderer.
     * 
     * @return void
     * 
     */
    abstract public function exec();
}
