<?php
/**
 * 
 * This file is part of the Aura project for PHP.
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace aura\web;
use aura\signal\Manager as SignalManager;

/**
 * 
 * A page controller.
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
     * @var Transfer
     * 
     */
    protected $transfer;
    
    /**
     * 
     * A signal manager.
     * 
     * @var aura\signal\Manager
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
        Context       $context,
        SignalManager $signal,
        Transfer      $transfer,
        array         $params = null
    ) {
        $this->context  = $context;
        $this->signal   = $signal;
        $this->transfer = $transfer;
        $this->params   = new \ArrayObject((array) $params, \ArrayObject::ARRAY_AS_PROPS);
        
        // get an action out of the params
        if (isset($this->params->action)) {
            $this->action = $this->params->action;
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
     * - signals `'pre_action'`
     * 
     * - is the action is not to be skipped, calls `action()` and signals 
     *   `'post_action'`
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
        $this->signal->send($this, 'pre_action', $this);
        if (! $this->isSkipAction()) {
            $method = 'action' . ucfirst($this->action);
            if (! method_exists($this, $method)) {
                throw new Exception_NoMethodForAction($this->action);
            }
            $this->$method();
            $this->signal->send($this, 'post_action', $this);
        }
        $this->signal->send($this, 'post_exec', $this);
        
        // done, return the transfer object
        return $this->transfer;
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
}
