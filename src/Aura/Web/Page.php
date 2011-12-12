<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web;

/**
 * 
 * A page controller.
 * 
 *      public function actionBrowse()
 *      {
 *          // blah blah blah
 *          
 *          // now set the view
 *          $this->response->view = array(
 *              'text/html' => 'index',
 *              // closure always takes one param, the response transfer object
 *              'application/json' => function($response) {
 *                  $response->layout = null;
 *                  return json_encode($response->view_data->list);
 *              },
 *              'application/xml' => 'default.xml',
 *          );
 *      }
 *     
 * @package Aura.Web
 * 
 */
abstract class Page
{
    /**
     * 
     * The context of the request environment.
     * 
     * @var Context
     * 
     */
    protected $context;
    
    /**
     * 
     * A data transfer object for the eventual HTTP response.
     * 
     * @var ResponseTransfer
     * 
     */
    protected $response;
    
    /**
     * 
     * Path-info parameters, typically from the route.
     * 
     * @var array
     * 
     */
    protected $params;
    
    /**
     * 
     * The action to perform.
     * 
     * @var string
     * 
     */
    protected $action;
    
    /**
     * 
     * Constructor.
     * 
     * @param Context $context The request environment.
     * 
     */
    public function __construct(
        Context          $context,
        ResponseTransfer $response,
        array            $params = array()
    ) {
        $this->context  = $context;
        $this->response = $response;
        $this->params   = $params;
        $this->initAction();
        $this->initResponseFormat();
        $this->initResponseStacks();
    }
    
    /**
     * 
     * Initialize the action name from the params.
     * 
     * @return void
     * 
     */
    protected function initAction()
    {
        $this->action = isset($this->params['action'])
                      ? $this->params['action']
                      : null;
    }
    
    /**
     * 
     * Initialize the response format.
     * 
     * @return void
     * 
     */
    protected function initResponseFormat()
    {
        $format = isset($this->params['format'])
                ? $this->params['format']
                : null;
        $this->response->setFormat($format);
    }
    
    /**
     * 
     * Initialize the response view and layout stacks.
     * 
     * @return void
     * 
     */
    protected function initResponseStacks()
    {
        $class = get_class($this);
        $stack = class_parents($class);
        array_unshift($stack, $class);
        foreach ($stack as $name) {
            $pos  = strrpos($name, '\\');
            $spec = substr($name, 0, $pos);
            $this->response->addViewStack($spec);
            $this->response->addLayoutStack($spec);
        }
    }
    
    /**
     * 
     * Executes the action and pre/post hooks.
     * 
     * - calls `preExec()`
     * 
     * - calls `preAction()`
     * 
     * - calls `action()` to find the action method to run, and runs it
     * 
     * - calls `postAction()`
     * 
     * - calls `postExec()`
     * 
     * @return ResponseTransfer
     * 
     */
    public function exec()
    {
        $this->preExec();
        $this->preAction();
        $this->action();
        $this->postAction();
        $this->postExec();
        return $this->response;
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
     * Runs after `preExec()` but before `action()`.
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
    public function action()
    {
        $method = 'action' . ucfirst($this->action);
        if (! method_exists($this, $method)) {
            throw new Exception\NoMethodForAction($this->action);
        }
        $this->invokeMethod($method);
    }
    
    /**
     * 
     * Runs after `action()` but before `postExec()`.
     * 
     * @return mixed
     * 
     */
    public function postAction()
    {
    }
    
    /**
     * 
     * Runs at the end of `exec()` after `postAction()`.
     * 
     * @return mixed
     * 
     */
    public function postExec()
    {
    }
    
    /**
     * 
     * Invokes a method by name, matching method params to `$this->params`.
     * 
     * @param string $name The method name to execute, typcially an action.
     * 
     * @return mixed The return from the executed method.
     * 
     */
    protected function invokeMethod($name)
    {
        $args = array();
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
        return $method->invokeArgs($this, $args);
    }
}
