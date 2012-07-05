<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web\Controller;

use Aura\Web\Renderer\RendererInterface;

/**
 * 
 * A page controller.
 * 
 * @package Aura.Web
 * 
 */
abstract class AbstractPage extends AbstractController
{
    /**
     * 
     * The action to perform, typically discovered from the params.
     * 
     * @var string
     * 
     */
    protected $action;
    
    /**
     * 
     * The page format to render, typically discovered from the params.
     * 
     * @var string
     * 
     */
    protected $format;
    
    /**
     * 
     * Initialize after construction.
     * 
     * @return void
     * 
     */
    public function init()
    {
        // set the action
        $this->action = isset($this->params['action'])
                      ? $this->params['action']
                      : null;
        
        // set the format
        $this->format = isset($this->params['format'])
                      ? $this->params['format']
                      : null;
    }
    
    /**
     * 
     * Returns the action, typically discovered from the params.
     * 
     * @return string
     * 
     */
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * 
     * Returns the page format, typically discovered from the params.
     * 
     * @return StdClass
     * 
     */
    public function getFormat()
    {
        return $this->format;
    }
    
    /**
     * The Execution Cycle
     * -------------------
     */
    
    /**
     * 
     * Executes the action and all hooks:
     * 
     * - calls `preExec()`
     * 
     * - calls `preAction()`
     * 
     * - calls `action()` to find and invoke the action method
     * 
     * - calls `postAction()`
     * 
     * - calls `preRender()`
     * 
     * - calls `render()` to generate a presentation (does nothing by default)
     * 
     * - calls `postRender()`
     * 
     * - calls `postExec()` and then returns the Response transfer object
     * 
     * @return Response
     * 
     */
    public function exec()
    {
        // pre-exec hook
        $this->preExec();
        
        // the action cycle
        $this->preAction();
        $this->action();
        $this->postAction();
        
        // the render cycle
        $this->preRender();
        $this->render();
        $this->postRender();
        
        // post-exec hook
        $this->postExec();
        
        // done!
        return $this->getResponse();
    }
    
    /**
     * 
     * Runs at the beginning of `exec()` before `preAction()`.
     * 
     * @return void
     * 
     */
    public function preExec()
    {
    }
    
    /**
     * 
     * Runs after `preExec()` and before `action()`.
     * 
     * @return void
     * 
     */
    public function preAction()
    {
    }
    
    /**
     * 
     * Determines the action method, then invokes it.
     * 
     * @return void
     * 
     */
    protected function action()
    {
        $method = 'action' . ucfirst($this->action);
        if (! method_exists($this, $method)) {
            throw new Exception\NoMethodForAction($this->action);
        }
        $this->invokeMethod($method);
    }
    
    /**
     * 
     * Invokes a method by name, matching method params to `$this->params`.
     * 
     * @param string $name The method name to execute, typcially an action.
     * 
     * @return void
     * 
     */
    protected function invokeMethod($name)
    {
        $args = [];
        $method = new \ReflectionMethod($this, $name);
        foreach ($method->getParameters() as $param) {
            if (isset($this->params[$param->name])) {
                $args[] = $this->params[$param->name];
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                $args[] = null;
            }
        }
        $method->invokeArgs($this, $args);
    }
    
    /**
     * 
     * Runs after `action()` and before `preRender()`.
     * 
     * @return void
     * 
     */
    public function postAction()
    {
    }
    
    /**
     * 
     * Runs after `postAction()` and before `render()`.
     * 
     * @return void
     * 
     */
    public function preRender()
    {
    }
    
    /**
     * 
     * Renders the page into the response object.
     * 
     * @return void
     * 
     */
    protected function render()
    {
        $this->renderer->exec();
    }
    
    /**
     * 
     * Runs after `render()` and before `postExec()`.
     * 
     * @return void
     * 
     */
    public function postRender()
    {
    }
    
    /**
     * 
     * Runs at the end of `exec()` after `postRender()`.
     * 
     * @return mixed
     * 
     */
    public function postExec()
    {
    }
}
