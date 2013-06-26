<?php
namespace Fwk\Di\Definitions;

use Fwk\Di\Definition;
use Fwk\Di\Container;

class ClassDefinition implements Definition
{
    protected $className;
    protected $methodCalls  = array();
    
    public function __construct($className, $parameters = array(), 
        $methodCalls = array()
    ) {
        $this->className    = $className;
        $this->parameters   = $parameters;
        $this->methodCalls  = $methodCalls;
    }
    
    public function invoke(Container $container)
    {
    }
    
    public function getClassName()
    {
        return $this->className;
    }

    public function setClassName($className)
    {
        $this->className = $className;
    }

    public function getMethodCalls()
    {
        return $this->methodCalls;
    }

    public function setMethodCalls(array $methodCalls)
    {
        $this->methodCalls = $methodCalls;
    }
}