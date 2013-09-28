<?php
/**
 * 
 * This file is part of Aura for PHP.
 * 
 * @package Aura.Web
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 */
namespace Aura\Web\Controller;

use Aura\Web\Exception as WebException;
use Aura\Web\Renderer\RendererInterface;
use Exception;
use ReflectionMethod;

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
     * The exception caught inside `exec()`.
     * 
     * @var Exception
     * 
     */
    protected $exception;


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
        $this->signal->handler($this, 'pre_action',      [$this, 'preAction']);
        $this->signal->handler($this, 'post_action',     [$this, 'postAction']);

        // the exception-catching signal handler on this class is intended as
        // a final fallback; other handlers most likely need to run before it.
        $this->signal->handler(
            $this,
            'catch_exception',
            [$this, 'catchException'],
            999
        );
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
     * @return string
     * 
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * 
     * Returns the exception caught by exec().
     * 
     * @return Exception
     * 
     */
    public function getException()
    {
        return $this->exception;
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
     * - signals `catch_exception` when a exception is thrown, thereby
     *   calling `catchException()`
     * 
     * - returns the Response transfer object
     * 
     * @return Response
     * 
     */
    public function exec()
    {
        try {
            // pre-action
            $this->signal->send($this, 'pre_action', $this);
            
            // determine the params for the action method
            $args = [];
            $method = new ReflectionMethod($this, 'action');
            foreach ($method->getParameters() as $param) {
                if (isset($this->params[$param->name])) {
                    $args[] = $this->params[$param->name];
                } elseif ($param->isDefaultValueAvailable()) {
                    $args[] = $param->getDefaultValue();
                } else {
                    $args[] = null;
                }
            }
            
            // call the action method
            call_user_func_array([$this, 'action'], $args);
            
            // post-action
            $this->signal->send($this, 'post_action', $this);
            
        } catch (Exception $exception) {
            
            $this->exception = $exception;
            $this->signal->send($this, 'catch_exception', $this);
            
        }
    }

    /**
     * 
     * Determines the action method, then invokes it.
     * 
     * @param string $name The name of the action to invoke.
     * 
     * @return void
     * 
     */
    protected function action($name)
    {
        $method = 'action' . ucfirst($name);
        if (! method_exists($this, $method)) {
            throw new WebException\NoMethodForAction($name);
        }
        $this->invokeMethod($method);
    }

    /**
     * 
     * Invokes a method by name, matching method params to `$this->params`.
     * 
     * @param string $name The method name to execute, typically an action.
     * 
     * @return void
     * 
     */
    protected function invokeMethod($name)
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
     * Runs after `preExec()` and before the main `action()`.
     * 
     * @return void
     * 
     */
    public function preAction()
    {
    }

    /**
     * 
     * Runs after the main `action()` and before `preRender()`.
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
     * Runs when `exec()` catches an exception.
     * 
     * @return mixed
     * 
     */
    public function catchException()
    {
        // get the current exception
        $e = $this->getException();

        // throw a copy, with the original as the previous exception so that
        // we can see a full trace.
        $class = get_class($e);
        throw new $class($e->getMessage(), $e->getCode(), $e);
    }
}
