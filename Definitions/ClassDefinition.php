<?php
namespace Fwk\Di\Definitions;

use Fwk\Di\Definition;
use Fwk\Di\Container;
use Fwk\Di\AbstractDefinition;
use Fwk\Di\Reference;

class ClassDefinition extends AbstractDefinition implements Definition
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
        $className = $this->className;
        if ($className instanceof Reference) {
            $className = $container->get($className);
        }
        
        if (!is_string($className)) {
            throw new \InvalidArgumentException("Classname must be a string");
        }
        
        $refClass = new \ReflectionClass($className);
        
        return $refClass->newInstanceArgs(
            $this->getComputedParameters($container)
        );
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