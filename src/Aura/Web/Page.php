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
     * The action to perform, typically discovered from the params.
     * 
     * @var string
     * 
     */
    protected $action;
    
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
     * Collection point for data, typically for rendering the page.
     * 
     * @var StdClass
     * 
     */
    protected $data;
    
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
     * Path-info parameters, typically from the route.
     * 
     * @var array
     * 
     */
    protected $params;
    
    /**
     * 
     * A data transfer object for the eventual HTTP response.
     * 
     * @var Response
     * 
     */
    protected $response;
    
    /**
     * 
     * Constructor.
     * 
     * @param Context $context The request environment.
     * 
     */
    public function __construct(
        Context          $context,
        Response $response,
        array            $params = array()
    ) {
        $this->context  = $context;
        $this->response = $response;
        $this->params   = $params;
        $this->data     = new \StdClass;
        $this->action   = isset($this->params['action'])
                        ? $this->params['action']
                        : null;
        $this->format   = isset($this->params['format'])
                        ? $this->params['format']
                        : null;
    }
    
    /**
     * Getters
     * -------
     */
    
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
     * Returns the Context object.
     * 
     * @return Context
     * 
     */
    public function getContext()
    {
        return $this->context;
    }
    
    /**
     * 
     * Returns the data collection object.
     * 
     * @return StdClass
     * 
     */
    public function getData()
    {
        return $this->data;
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
     * Returns the params.
     * 
     * @return array
     * 
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * 
     * Returns the Response object.
     * 
     * @return Response
     * 
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * The Execution Cycle
     * -------------------
     */
    
    /**
     * 
     * Executes the action and pre/post hooks:
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
     * - calls `render()` to execute the RenderHandler for this page
     * 
     * - calls `postRender()`
     * 
     * - calls `postExec()` and then returns the Response object
     * 
     * @return Response
     * 
     */
    public function exec()
    {
        // prep
        $this->preExec();
        
        // the action cycle
        $this->preAction();
        $this->action();
        $this->postAction();
        
        // the render cycle
        $this->preRender();
        $this->render();
        $this->postRender();
        
        // done
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
