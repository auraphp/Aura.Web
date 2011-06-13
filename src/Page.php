<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web;
use Aura\Signal\Manager as SignalManager;

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
 * @package aura.web
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
     * A signal manager.
     * 
     * @var Aura\Signal\Manager
     * 
     */
    protected $signal;
    
    /**
     * 
     * Path-info parameters from the route.
     * 
     * @var \ArrayObject
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
     * When set to `true` before `action()` is called, the `action()` will not
     * be called after all.
     * 
     * @var bool
     * 
     */
    protected $skip_action = false;
    
    /**
     * 
     * Constructor.
     * 
     * @param Context $context The request environment.
     * 
     */
    public function __construct(
        Context          $context,
        SignalManager    $signal,
        ResponseTransfer $response,
        array            $params = array()
    ) {
        $this->context  = $context;
        $this->signal   = $signal;
        $this->response = $response;
        $this->params   = $params;
        
        // get an action out of the params
        $this->action = isset($this->params['action'])
                      ? $this->params['action']
                      : null;
        
        // get a format out of the params
        $format = isset($this->params['format'])
                ? $this->params['format']
                : null;
        $this->response->setFormat($format);
        
        // add self and parents to the view/layout stacks
        $class = get_class($this);
        $stack = class_parents($class);
        array_unshift($stack, $class);
        foreach ($stack as $name) {
            $pos  = strrpos($name, '\\');
            $spec = substr($name, 0, $pos);
            $this->response->addViewStack($spec);
            $this->response->addLayoutStack($spec);
        }
        
        // add signals
        $this->signal->handler($this, 'pre_exec', array($this, 'preExec'));
        $this->signal->handler($this, 'pre_action', array($this, 'preAction'));
        $this->signal->handler($this, 'post_action', array($this, 'postAction'));
        $this->signal->handler($this, 'post_exec', array($this, 'postExec'));
    }
    
    /**
     * 
     * Executes the Page action.  In order, it does these things:
     * 
     * - signals `'pre_exec'`
     * 
     * - is the action is not to be skipped ...
     * 
     *     - signals `'pre_action'`
     * 
     *     - calls `invokeMethod()` to run the action
     * 
     *     - signals `'post_action'`
     * 
     * - signals `'post_exec'`
     * 
     * @signal 'pre_exec'
     * 
     * @signal 'pre_action'
     * 
     * @signal 'post_action'
     * 
     * @signal 'post_exec'
     * 
     * @return void
     * 
     */
    public function exec()
    {
        $this->signal->send($this, 'pre_exec', $this);
        if (! $this->isSkipAction()) {
            $method = 'action' . ucfirst($this->action);
            if (! method_exists($this, $method)) {
                throw new Exception\NoMethodForAction($this->action);
            }
            $this->signal->send($this, 'pre_action', $this);
            $this->invokeMethod($method);
            $this->signal->send($this, 'post_action', $this);
        }
        $this->signal->send($this, 'post_exec', $this);
        
        // done, return the response transfer object
        return $this->response;
    }
    
    /**
     * 
     * Stops `exec()` from calling `action()` if it has not already done so.
     * 
     * @return void
     * 
     */
    public function skipAction()
    {
        $this->skip_action = true;
    }
    
    /**
     * 
     * Should the call to `action()` be skipped?
     * 
     * @return bool
     * 
     */
    public function isSkipAction()
    {
        return (bool) $this->skip_action;
    }
    
    /**
     * 
     * Runs before `action()` as part of the `'pre_exec'` signal.
     * 
     * @return mixed
     * 
     */
    public function preExec()
    {
    }
    
    /**
     * 
     * Runs before `action()` as part of the `'pre_action'` signal.
     * 
     * @return mixed
     * 
     */
    public function preAction()
    {
    }
    
    /**
     * 
     * Runs after `action()` as part of the `'post_action'` signal.
     * 
     * @return mixed
     * 
     */
    public function postAction()
    {
    }
    
    /**
     * 
     * Runs after `action()` as part of the `'post_exec'` signal.
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
