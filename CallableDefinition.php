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
    }
}