<?php
namespace Fwk\Di;

class ClassDefinition extends AbstractDefinition implements Invokable
{
    protected $className;
    protected $methodCalls  = array();
    
    public function __construct($className, array $arguments = array()) {
        $this->className    = $className;
        $this->arguments    = $arguments;
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
        
        $instance = $this->newInstance($container);
        $this->executeMethodCalls($instance, $container);
        
        return $instance;
    }
    
    protected function executeMethodCalls($instance, Container $container) {
        foreach ($this->methodCalls as $idx => $methodCall) {
            $callable = $methodCall->getCallable();
            $methodCall->setCallable(array($instance, $callable));
            try {
                $methodCall->invoke($container);
            } catch(Exception $exp) {
                throw new Exceptions\InvalidClassDefinition($this->className, $exp);
            }
            $methodCall->setCallable($callable);
        }
    }
    
    protected function newInstance(Container $container) {
        if (!class_exists($this->className, true)) {
            throw new Exceptions\ClassNotFound($this->className);
        }
        
        $reflect    = new \ReflectionClass($this->className);
        
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

    /**
     *
     * @param type $methodName
     * @param array $arguments
     * @return CallableDefinition 
     */
    public function addMethodCall($methodName, array $arguments = array())
    {
        return $this->methodCalls[] = new CallableDefinition(
            $methodName, 
            $arguments
        );
    }
    
    /**
     *
     * @param string $methodName
     * 
     * @return ClassDefinition 
     */
    public function removeMethodClass($methodName)
    {
        $this->methodCalls = array_filter(
            $this->methodCalls, function($call) use ($methodName) {
                return $methodName !== $call->getCallable();
            }
        );
        
        return $this;
    }
}