<?php

namespace Fwk\Di;

/**
 */
class ContainerTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Container
     */
    protected $object;

    /**
     */
    protected function setUp() {
        $this->object = new Container;
    }

    /**
     */
    public function testBasicSetAndGet() {
        $this->assertFalse($this->object->has('test'));
        $this->object->set('test', 'just a string');
        $this->assertTrue($this->object->has('test'));
        $this->assertEquals('just a string', $this->object->get('test'));
    }
    
    /**
     */
    public function testNonSharedSetAndGetCallable() {
        $this->assertFalse($this->object->has('test'));
        $callable = function () { $a = new \stdClass(); $a->mt = microtime(true); return $a; };
        $this->object->set('test', $callable);
        $this->assertTrue($this->object->has('test'));
        $this->assertFalse($this->object->isShared('test'));
        $inst = $this->object->get('test');
        $this->assertInstanceOf('\stdClass', $inst);
        $this->assertFalse($inst === $this->object->get('test'));
    }
    
    public function testSharedSetAndGetCallable() {
        $this->assertFalse($this->object->has('test'));
        $callable = function () { $a = new \stdClass(); $a->mt = microtime(true); return $a; };
        $this->object->set('test', $callable, true);
        $this->assertTrue($this->object->has('test'));
        $this->assertTrue($this->object->isShared('test'));
        $inst = $this->object->get('test');
        $this->assertInstanceOf('\stdClass', $inst);
        $this->assertTrue($inst === $this->object->get('test'));
    }
    
    public function testInvalidDefinition() {
        $this->setExpectedException('Fwk\Di\Exceptions\DefinitionNotFoundException');
        $inst = $this->object->get('test');
    }
    
    public function testInvalidSharedDefinition() {
        $this->setExpectedException('Fwk\Di\Exceptions\DefinitionNotFoundException');
        $inst = $this->object->isShared('test');
    }
    
    /**
     *
     */
    public function testUnregisterNotShared() {
        $this->testNonSharedSetAndGetCallable();
        
        $this->assertTrue($this->object->has('test'));
        $inst = $this->object->get('test');
        $this->object->unregister('test');
        $this->assertFalse($this->object->has('test'));
    }
    
    /**
     *
     */
    public function testUnregisterShared() {
        $this->testSharedSetAndGetCallable();
        
        $this->assertTrue($this->object->has('test'));
        $this->assertTrue($this->object->isShared('test'));
        $inst = $this->object->get('test');
        $this->object->unregister('test');
        $this->assertFalse($this->object->has('test'));
    }
    
    public function testUnregisterInvalidSharedDefinition() {
        $this->setExpectedException('Fwk\Di\Exceptions\DefinitionNotFoundException');
        $inst = $this->object->unregister('test');
    }
    
    public function testNotSharedSetAndGetDefinition() {
        $this->assertFalse($this->object->has('test'));
        $def = ClassDefinition::factory('stdClass');
        $this->object->set('test', $def, false);
        $this->assertTrue($this->object->has('test'));
        $this->assertFalse($this->object->isShared('test'));
        $inst = $this->object->get('test');
        $this->assertInstanceOf('\stdClass', $inst);
        $this->assertFalse($inst === $this->object->get('test'));
    }
    
    public function testSharedSetAndGetDefinition() {
        $this->assertFalse($this->object->has('test'));
        $def = ClassDefinition::factory('stdClass');
        $this->object->set('test', $def, true);
        $this->assertTrue($this->object->has('test'));
        $this->assertTrue($this->object->isShared('test'));
        $inst = $this->object->get('test');
        $this->assertInstanceOf('\stdClass', $inst);
        $this->assertTrue($inst === $this->object->get('test'));
    }

    public function testProperties()
    {
        $this->object->setProperty('testPropOne', 'one');
        $this->object->setProperty('testPropTwo', 'two');
        $this->object->setProperty('testPhrase', ':testPropOne+:testPropOne=:testPropTwo');

        $this->assertEquals('one+one=two', $this->object->getProperty('testPhrase'));
    }
}
