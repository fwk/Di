<?php

namespace Fwk\Di\Definitions;
use Fwk\Di\Container;

/**
 * Test class for ClassDefinition.
 */
class LazyClassDefinitionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ClassDefinition
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new LazyClassDefinition('\stdClass');
        
    }
    
    protected function getContainer()
    {
        $container = new Container();

        return $container;
    }
    
   public function testLazyClassLoading()
   {
       $class = $this->object->invoke($this->getContainer());
       $this->assertArrayHasKey('ProxyManager\Proxy\LazyLoadingInterface', class_implements($class));
   }
}