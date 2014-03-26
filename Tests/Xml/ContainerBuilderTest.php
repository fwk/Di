<?php

namespace Fwk\Di\Xml;

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-25 at 00:16:15.
 */
class ContainerBuilderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var ContainerBuilder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new ContainerBuilder();
    }

    /**
     */
    public function testClassDefinition() {
        $container = $this->object->execute(__DIR__ .'/../test-di.xml');
        $this->assertInstanceOf('Fwk\Di\Container', $container);
        $this->assertTrue($container->exists('myObj'));
        $this->assertInstanceOf('Fwk\Di\ClassDefinition', $container->get('myObj'));
        $this->assertEquals($container->get('myObj'), $container->get('myObj'));
        $this->assertTrue($container->isShared('myObj'));
    }
    
    public function testDefinition() {
        $container = new \Fwk\Di\Container();
        $this->assertFalse($container->exists('testDef'));
        $this->object->execute(__DIR__ .'/../test-di.xml', $container);
        $this->assertTrue($container->exists('testDef'));
        $this->assertEquals('valueOfDefinition', $container->get('testDef'));
        $this->assertFalse($container->isShared('testDef'));
    }

    /**
     */
    public function testMapGetterAndSetter() {
        $this->assertInstanceOf('Fwk\Di\Xml\ContainerXmlMap', $this->object->getMap());
        $this->object->setMap(new \Fwk\Xml\Maps\Rss());
        $this->assertInstanceOf('Fwk\Xml\Maps\Rss', $this->object->getMap());
    }
    
    public function testIniProperty()
    {
        $container = new \Fwk\Di\Container();
        $this->assertFalse($container->getProperty('iniProp', false));
        $this->object->execute(__DIR__ .'/../test-di.xml', $container);
        $this->assertEquals('testing', $container->getProperty('iniProp'));
    }
}
