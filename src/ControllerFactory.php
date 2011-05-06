<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\web;
use aura\di\ForgeInterface as ForgeInterface;

/**
 * 
 * A factory to create controller objects; these need not be only Page
 * controllers, but (e.g.) Resource or App controllers.
 * 
 * @package aura.web
 * 
 */
class ControllerFactory
{
    /**
     * 
     * An object-creation Forge.
     * 
     * @var ForgeInterface
     * 
     */
    protected $forge;
    
    /**
     * 
     * A map of controller names to controller classes.
     * 
     * @var ForgeInterface
     * 
     */
    protected $map = array();
    
    /**
     * 
     * The controller class to instantiate when no mapping is found.
     * 
     * @var ForgeInterface
     * 
     */
    protected $not_found = null;
    
    /**
     * 
     * Constructor.
     * 
     * @param aura\di\ForgeInterface $forge An object-creation Forge.
     * 
     * @param array $map A map of controller names to controller classes.
     * 
     * @param string $not_found The controller class to instantiate when no 
     * mapping is found.
     * 
     */
    public function __construct(ForgeInterface $forge, array $map = null, $not_found = null)
    {
        $this->forge     = $forge;
        $this->map       = (array) $map;
        $this->not_found = $not_found;
    }
    
    /**
     * 
     * Creates and returns a controller class based on a controller name.
     * 
     * @param string $name The controller name.
     * 
     * @param array $params Params to pass to the controller.
     * 
     * @return Page A controller instance.
     * 
     */
    public function newInstance($name, $params)
    {
        if (isset($this->map[$name])) {
            $class = $this->map[$name];
        } elseif ($this->not_found) {
            $class = $this->not_found;
        } else {
            throw new Exception_NoClassForController("'$name'");
        }
        
        return $this->forge->newInstance($class, array(
            'params' => $params
        ));
    }
}
