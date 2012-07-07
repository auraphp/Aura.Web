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

use Aura\Web\Renderer\RendererInterface;
use Aura\Web\Exception;

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
    protected function init()
    {
        // call the parent
        parent::init();
        
        // set the action
        $this->action = isset($this->params['action'])
                      ? $this->params['action']
                      : null;
        
        // set the format
        $this->format = isset($this->params['format'])
                      ? $this->params['format']
                      : null;
        
        // set the signal handlers
        $this->signal->handler($this, 'pre_exec',    [$this, 'preExec']);
        $this->signal->handler($this, 'pre_action',  [$this, 'preAction']);
        $this->signal->handler($this, 'post_action', [$this, 'postAction']);
        $this->signal->handler($this, 'pre_render',  [$this, 'preRender']);
        $this->signal->handler($this, 'post_render', [$this, 'postRender']);
        $this->signal->handler($this, 'post_exec',   [$this, 'postExec']);
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
     * 
     * Executes the action and all hooks:
     * 
     * - signals `pre_exec`, thereby calling `preExec()`
     * 
     * - signals `pre_action`, thereby calling `preAction()`
     * 
     * - calls `action()` to find and invoke the action method
     * 
     * - signals `post_action`, thereby calling `postAction()`
     * 
     * - signals `pre_render`, thereby calling `preRender()`
     * 
     * - calls `render()` to render a view (does nothing by default)
     * 
     * - signals `post_render`, thereby calling `postRender()`
     * 
     * - signals `post_exec`, thereby calling `postExec()`
     * 
     * - returns the Response transfer object
     * 
     * @return Response
     * 
     */
    public function exec()
    {
        // pre-exec signal
        $this->signal->send($this, 'pre_exec', $this);
        
        // the action cycle
        $this->signal->send($this, 'pre_action', $this);
        $this->action();
        $this->signal->send($this, 'post_action', $this);
        
        // the render cycle
        $this->signal->send($this, 'pre_render', $this);
        $this->render();
        $this->signal->send($this, 'post_render', $this);
        
        // post-exec signal
        $this->signal->send($this, 'post_exec', $this);
        
        // done!
        return $this->getResponse();
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
