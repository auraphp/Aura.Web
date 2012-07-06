<?php
namespace Aura\Web\Controller;

interface ControllerInterface
{
    public function getContext();
    
    public function getData();
    
    public function getParams();
    
    public function getResponse();
    
    public function getSignal();
    
    public function exec();
}
