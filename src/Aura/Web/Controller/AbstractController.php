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

use Aura\Web\Accept;
use Aura\Web\Context;
use Aura\Web\Renderer\RendererInterface;
use Aura\Web\Response;
use Aura\Web\SignalInterface;

/**
 * 
 * An abstract controller class, suitable for Page and Application controllers.
 * 
 * @package Aura.Web
 * 
 */
abstract class AbstractController implements ControllerInterface
{
    /**
     * 
     * The Accept object for tracking accept header values.
     * 
     * @var Accept
     * 
     */
    protected $accept;

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
     * Path-info parameters, typically from the route.
     * 
     * @var array
     * 
     */
    protected $params;

    /**
     * 
     * A rendering strategy object.
     * 
     * @var RendererInterface
     * 
     */
    protected $renderer;

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
     * A signal manager.
     * 
     * @var SignalInterface
     * 
     */
    protected $signal;

    /**
     * 
     * Constructor.
     * 
     * @param Context $context The request environment.
     * 
     * @param Accept $accept The accept-headers object.
     * 
     * @param Response $response A response transfer object.
     * 
     * @param SignalInterface $signal A signal manager.
     * 
     * @param RendererInterface $renderer A renderer strategy object.
     * 
     * @param array $params The path-info parameters.
     * 
     */
    public function __construct(
        Context           $context,
        Accept            $accept,
        Response          $response,
        SignalInterface   $signal,
        RendererInterface $renderer,
        array             $params = []
    ) {
        $this->context  = $context;
        $this->accept   = $accept;
        $this->response = $response;
        $this->signal   = $signal;
        $this->renderer = $renderer;
        $this->params   = $params;
        $this->data     = new \StdClass;
        $this->init();
    }

    /**
     * 
     * Post-constructor initialization.
     * 
     * @return void
     * 
     */
    protected function init()
    {
        $this->renderer->setController($this);
    }

    /**
     * 
     * Returns the Accept object.
     * 
     * @return Accept
     * 
     */
    public function getAccept()
    {
        return $this->accept;
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
     * @return object
     * 
     */
    public function getData()
    {
        return $this->data;
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
     * 
     * Returns the SignalInterface object.
     * 
     * @return SignalInterface
     * 
     */
    public function getSignal()
    {
        return $this->signal;
    }

    /**
     * 
     * Returns the RendererInterface object.
     * 
     * @return RendererInterface
     * 
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * 
     * Executes the controller.
     * 
     * @return Response
     * 
     */
    abstract public function exec();

    /**
     * 
     * Renders the controller output, generally into the Response object.
     * 
     * @return void
     * 
     */
    abstract protected function render();
}
