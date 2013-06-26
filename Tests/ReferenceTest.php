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
}
