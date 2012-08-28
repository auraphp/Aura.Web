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
 * An interface for renderer strategies.
 * 
 * @package Aura.Web
 * 
 */
interface RendererInterface
{
    /**
     * 
     * Sets the controller to be rendered.
     * 
     * @param ControllerInterface $controller The controller to be rendered.
     * 
     * @return void
     * 
     */
    public function setController(ControllerInterface $controller);

    /**
     * 
     * Executes the renderer.
     * 
     * @return void
     * 
     */
    public function exec();
}
