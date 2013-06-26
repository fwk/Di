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
        $this->assertFalse($this->object->exists('test'));
        $this->object->set('test', 'just a string');
        $this->assertTrue($this->object->exists('test'));
        $this->assertEquals('just a string', $this->object->get('test'));
    }
    
    /**
     */
    public function testNonSharedSetAndGetCallable() {
        $this->assertFalse($this->object->exists('test'));
        $callable = function () { $a = new \stdClass(); $a->mt = microtime(true); return $a; };
        $this->object->set('test', $callable);
        $this->assertTrue($this->object->exists('test'));
        
        $inst = $this->object->get('test');
        $this->assertInstanceOf('\stdClass', $inst);
        $this->assertFalse($inst === $this->object->get('test'));
    }
    
    public function testSharedSetAndGetCallable() {
        $this->assertFalse($this->object->exists('test'));
        $callable = function () { $a = new \stdClass(); $a->mt = microtime(true); return $a; };
        $this->object->set('test', $callable, true);
        $this->assertTrue($this->object->exists('test'));
        
        $inst = $this->object->get('test');
        $this->assertInstanceOf('\stdClass', $inst);
        $this->assertTrue($inst === $this->object->get('test'));
    }
    
    public function testInvalidDefinition() {
        $this->setExpectedException('Fwk\Di\Exceptions\DefinitionNotFound');
        $inst = $this->object->get('test');
    }
}
