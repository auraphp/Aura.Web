<?php
namespace Aura\Invoker;

use BadMethodCallException;
use ReflectionMethod;

trait InvokeMethodTrait
{
    protected function invokeMethod($object, $method, $params)
    {
        // is the method callable?
        if (! is_callable([$object, $method])) {
            $message = get_class($object) . '::' . $method;
            throw new BadMethodCallException($message);
        }
        
        // reflect on the method
        $method = new ReflectionMethod($object, $method);
        
        // sequential arguments when invoking the method
        $args = [];
        
        // match named action params with method arguments
        foreach ($method->getParameters() as $param) {
            if (isset($params[$name])) {
                // a named param value is available
                $args[] = $params[$name];
            } else {
                // use the default value, or null if there is none
                $args[] = $param->isDefaultValueAvailable()
                        ? $param->getDefaultValue()
                        : null;
            }
        }
        
        // invoke the method with the args, and done
        return $method->invokeArgs($args);
    }
}
