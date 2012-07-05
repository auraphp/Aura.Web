<?php
namespace Aura\Web\Renderer;

use Aura\Framework\Inflect;
use Aura\View\TwoStep;

class AuraViewTwoStep extends AbstractRenderer
{
    protected $twostep;
    
    protected $inflect;
    
    public function __construct(
        TwoStep $twostep,
        Inflect $inflect
    ) {
        $this->twostep = $twostep;
        $this->inflect = $inflect;
    }
    
    // allows us to call, e.g., $renderer->addInnerPath() to override stuff
    // in a seemingly-direct manner.
    public function __call($method, array $params)
    {
        return call_user_func_array([$this->twostep, $method], $params);
    }
    
    protected function prep()
    {
        // get all included files
        $includes = array_reverse(get_included_files());
        
        // get the controller class hierarchy stack
        $class = get_class($this->controller);
        $stack = class_parents($class);
        
        // drop Aura.Web and Aura.Framework
        array_pop($stack);
        array_pop($stack);
        
        // add the controller class itself
        array_unshift($stack, $class);
        
        // go through the hierarchy and look for each class file.
        // N.b.: this will not work if we concatenate all the classes into a
        // single file.
        foreach ($stack as $class) {
            $match = $this->inflect->classToFile($class);
            $len = strlen($match) * -1;
            foreach ($includes as $i => $include) {
                if (substr($include, $len) == $match) {
                    $dir = dirname($include);
                    $this->twostep->addInnerPath($dir . DIRECTORY_SEPARATOR . 'views');
                    $this->twostep->addOuterPath($dir . DIRECTORY_SEPARATOR . 'layouts');
                    unset($includes[$i]);
                    break;
                }
            }
        }
    }
    
    public function exec()
    {
        // set up the view based on the controller values
        $this->twostep->setInnerView($this->controller->getView());
        $this->twostep->setOuterView($this->controller->getLayout()));
        $this->twostep->setFormat($this->controller->getFormat());
        $this->twostep->setInnerData($this->controller->getData());
        $this->twostep->setAccept($this->controller->getContext()->getAccept());
        
        // render the view into the response content, and set the type
        $response = $this->controller->getResponse();
        $response->setContent($this->twostep->render());
        $response->setContentType($this->twostep->getContentType());
    }
}
