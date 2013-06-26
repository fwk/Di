<?php
namespace Fwk\Di;

abstract class AbstractDefinition
{
    protected $parameters = array();
    protected $container;
    
    public function getParameters()
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }
    
    /**
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}