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

/**
 * 
 * A default strategy that does no rendering at all.
 * 
 * @package Aura.Web
 * 
 */
class None extends AbstractRenderer
{
    /**
     * 
     * A helper method to prepare the renderer after setController().
     * 
     * @return void
     * 
     */
    public function prep()
    {
        // do nothing
    }

    /**
     * 
     * Executes the renderer.
     * 
     * @return void
     * 
     */
    public function exec()
    {
        // do nothing
    }
}
 