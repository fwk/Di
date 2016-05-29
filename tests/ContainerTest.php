<?php

namespace Fwk\Di;
use Fwk\Di\Definitions\CallableDefinition;
use Fwk\Di\Definitions\ClassDefinition;

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
        $inst = $this->object->get('test');
        $this->assertInstanceOf('\stdClass', $inst);
        $this->assertFalse($inst === $this->object->get('test'));
    }
    
    public function testSharedSetAndGetCallable() {
        $this->assertFalse($this->object->has('test'));
        $callable = function () { $a = new \stdClass(); $a->mt = microtime(true); return $a; };
        $this->object->set('test', CallableDefinition::factory($callable)->setShared(true));
        $this->assertTrue($this->object->has('test'));
        $inst = $this->object->get('test');
        $this->assertInstanceOf('stdClass', $inst);
        $this->assertTrue($inst === $this->object->get('test'));
    }
    
    public function testInvalidDefinition() {
        $this->setExpectedException('Fwk\Di\Exceptions\DefinitionNotFoundException');
        $inst = $this->object->get('test');
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
        $inst = $this->object->get('test');
        $this->assertInstanceOf('\stdClass', $inst);
        $this->assertFalse($inst === $this->object->get('test'));
    }
    
    public function testSharedSetAndGetDefinition() {
        $this->assertFalse($this->object->has('test'));
        $def = ClassDefinition::factory('stdClass');
        $this->object->set('test', $def->setShared(true));
        $this->assertTrue($this->object->has('test'));
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

    public function __testServicesSearch()
    {
        $this->object->set('testDef', 'definitionDataTest', false, array('dataOne' => true, 'text' => 'hello John'));
        $this->object->set('testDef2', 'definitionDataTest', false, array('dataOne' => false, 'text' => 'hello Doe'));
        $this->object->set('testDef3', 'definitionDataTest', false, array('dataTwo' => false, 'text' => 'Hey guys!'));

        $results = $this->object->search(array('dataOne' => true));
        $this->assertEquals(1, count($results));
        $this->assertArrayHasKey('testDef', $results);
        $results = $this->object->search(array('dataOne' => false));
        $this->assertEquals(1, count($results));
        $this->assertArrayHasKey('testDef2', $results);

        $results = $this->object->search(array('nothing'));
        $this->assertEquals(0, count($results));

        $results = $this->object->search(array('text' => 'hello*'));
        $this->assertEquals(2, count($results));
        $this->assertArrayHasKey('testDef', $results);
        $this->assertArrayHasKey('testDef2', $results);

        $results = $this->object->search(array('text' => '*guys?'));
        $this->assertEquals(1, count($results));
        $this->assertArrayHasKey('testDef3', $results);
    }
}