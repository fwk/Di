<?php

namespace Fwk\Di;

/**
 * Test class for CallableDefinition.
 * Generated by PHPUnit on 2013-06-28 at 00:09:38.
 */
class CallableDefinitionTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var CallableDefinition
     */
    protected $object;

    public $testPoint = false;
    
    public function setUp()
    {
        $me = $this;
        $this->object = new CallableDefinition(function() use ($me) { 
            $me->testPoint = true; 
        });
    }
    
    protected function getContainer()
    {
        $container = new Container();
        $container['className'] = '\stdClass';
        $container->set('temp.dir', function($c) { return sys_get_temp_dir(); });
        
        return $container;
    }
    
    public function testSimpleInvocation()
    {
        $this->assertFalse($this->testPoint);
        $this->object->invoke($this->getContainer());
        $this->assertTrue($this->testPoint);
    }
    
    public function testSimpleInvocationError()
    {
        \PHPUnit_Framework_Error_Notice::$enabled = FALSE;
        $this->object->setCallable(array('SimplyNot', 'callable'));
        $this->setExpectedException('\Fwk\Di\Exceptions\InvalidCallableDefinitionException');
        $this->object->invoke($this->getContainer());
    }
    
    /**
     */
    public function testInvokeWithErroneousArguments() {
        $this->object->setCallable('date_default_timezone_set');
        $this->object->addArgument(new Reference('invalid_ref'));
        $this->setExpectedException('Fwk\Di\Exceptions\InvalidCallableDefinitionException');
        $it = $this->object->invoke($this->getContainer());
    }
}