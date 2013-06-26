<?php

namespace Fwk\Di;


/**
 */
class ParameterTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Parameter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new Parameter("testParam");
    }


    /**
     */
    public function testGetterAndSetterName() {
        $this->assertEquals("testParam", $this->object->getName());
        $this->object->setName("testParamName");
        $this->assertEquals("testParamName", $this->object->getName());
    }

    /**
     */
    public function testGetterAndSetterValue() {
        $this->assertEquals(null, $this->object->getValue());
        $this->object->setValue("testValue");
        $this->assertEquals("testValue", $this->object->getValue());
        
        $this->object = new Parameter("testParam", "testValueConstructor");
        $this->assertEquals("testValueConstructor", $this->object->getValue());
    }
}
