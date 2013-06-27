<?php

namespace Fwk\Di;

/**
 */
class ReferenceTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Reference
     */
    protected $object;

    /**
     */
    protected function setUp() {
        $this->object = new Reference("testRef");
    }

    protected function getContainer() {
        $container = new Container();
        $container->set('test.param', 'parameterValue');
        $container->set('callable', function($c) { return 'callValue'; });
        $container->set('shared', function($c) { $a = new stdClass(); $a->mt = microtime(true); return $a; }, true);
        
        return $container;
    }
    
    /**
     */
    public function testGetterAndSetter() {
        $this->assertEquals("testRef", $this->object->getName());
        $this->object->setName("testRefName");
        $this->assertEquals("testRefName", $this->object->getName());
    }

    /**
     */
    public function test__toString(){
        $this->assertEquals("testRef", (string)$this->object);
    }
    
    public function testBasicInvocation()
    {
        $this->object->setName('test.param');
        $this->assertEquals('parameterValue', $this->object->invoke($this->getContainer()));
    }
    
    public function testInvocationError()
    {
        $this->setExpectedException('Fwk\Di\Exceptions\InvalidReference');
        $this->object->invoke($this->getContainer());
    }
}
