<?php
namespace Fwk\Di;

class CallableDefinition extends AbstractDefinition implements Invokable
{
    protected $callable;
    
    public function __construct($callable, array $arguments = array())
    {
        $this->callable     = $callable;
        $this->arguments    = $arguments;
    }
    
    public function invoke(Container $container)
    {
        if (!is_callable($this->callable)) {
            throw new Exceptions\InvalidCallableDefinition($this->callable);
        }
        
        $args = array();
        try {
            $args = $this->getConstructorArguments($container);
        } catch(Exception $exp) {
            throw new Exceptions\InvalidCallableDefinition($this->callable, $exp);
        }
        
        return call_user_func_array($this->callable, $args);
    }
    
    public function getCallable()
    {
        return $this->callable;
    }
    
    public function setCallable($callable)
    {
        $this->callable = $callable;
    }
}