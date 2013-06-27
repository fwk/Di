<?php
namespace Fwk\Di;

class ClassDefinition extends AbstractDefinition implements Invokable
{
    protected $className;
    protected $methodCalls  = array();
    
    public function __construct($className, $arguments = array(), 
        $methodCalls = array()
    ) {
        $this->className    = $className;
        $this->arguments    = $arguments;
        $this->methodCalls  = $methodCalls;
    }
    
    public function invoke(Container $container)
    {
        if ($this->className instanceof Invokable) {
            try {
                $this->className = $this->className->invoke($container);
            } catch(Exception $exp) {
                throw new Exceptions\InvalidClassDefinition($this->className, $exp);
            }
        } 
        
        if (!is_string($this->className)) {
            throw new Exceptions\InvalidClassDefinition(
                    '???', 
                    new \InvalidArgumentException(
                        sprintf(
                            'Classname must be a string or a Fwk\Di\Reference ' .
                            'instance (' . (is_object($this->className) 
                                ? get_class($this->className) 
                                : get_type($this->className)
                            ) . ' given)'
                        )
                    )
            );
        }
        
        if (!class_exists($this->className, true)) {
            throw new Exceptions\ClassNotFound($this->className);
        }
        
        $reflect    = new \ReflectionClass($this->className);
        $return     = null;
        
        if (null !== $reflect->getConstructor()) {
            $args = array();
            try {
                $args = $this->getConstructorArguments($container);
            } catch(Exception $exp) {
                throw new Exceptions\InvalidClassDefinition($this->className, $exp);
            }
            
            $return = $reflect->newInstanceArgs($args);
        } else {
            $return = new $this->className();
        }
        
        return $return;
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