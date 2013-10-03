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
    
    public function getComputedParameters(Container $container)
    {
        $result = array();
        foreach ($this->parameters as $param) {
            if ($param instanceof Parameter) {
                $value = $param->getValue();
                if ($value instanceof Reference) {
                    $value = $container->get($value);
                }
                $result[] = $value;
            } elseif ($param instanceof Reference) {
                $result[] = $container->get($param);
            } else {
                $result[] = $param;
            }
        }
        
        return $result;
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