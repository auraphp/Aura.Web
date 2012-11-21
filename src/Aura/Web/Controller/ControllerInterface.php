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

/**
 * 
 * An interface for web controllers.
 * 
 * @package Aura.Web
 * 
 */
interface ControllerInterface
{
    /**
     * 
     * Returns the Accept object.
     * 
     * @return Accept
     * 
     */
    public function getAccept();

    /**
     * 
     * Returns the Context object.
     * 
     * @return Context
     * 
     */
    public function getContext();

    /**
     * 
     * Returns the data collection object.
     * 
     * @return object
     * 
     */
    public function getData();

    /**
     * 
     * Returns the params.
     * 
     * @return array
     * 
     */
    public function getParams();

    /**
     * 
     * Returns the Response object.
     * 
     * @return Response
     * 
     */
    public function getResponse();

    /**
     * 
     * Returns the SignalInterface object.
     * 
     * @return SignalInterface
     * 
     */
    public function getSignal();

    /**
     * 
     * Executes the controller.
     * 
     * @return Response
     * 
     */
    public function exec();
}
