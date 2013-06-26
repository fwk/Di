<?php
namespace Fwk\Di\Definitions;

use Fwk\Di\Definition;
use Fwk\Di\AbstractDefinition;

class CallableDefinition extends AbstractDefinition implements Definition
{
    protected $callable;
    
    public function __construct(\Closure $callable, array $parameters = array())
    {
        $this->callable     = $callable;
        $this->parameters   = $parameters;
    }
    
    public function invoke(Container $container)
    {
    }
}