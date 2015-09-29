<?php

namespace Fwk\Di;
use Fwk\Di\Events\AfterServiceLoadedEvent;
use Fwk\Di\Events\BeforeServiceLoadedEvent;

/**
 */
class GeneralEventsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Container
     */
    protected $object;

    public function setUp()
    {
        $this->object = new Container();
        $this->object->set('test.service', function () {
            $obj = new \stdClass();
            $obj->test = "test";

            return $obj;
        });
    }
    
    public function testBeforeServiceLoadedEventStop()
    {
        $this->object->on('beforeServiceLoaded', function(BeforeServiceLoadedEvent $event) {
            $obj = new \stdClass();
            $obj->notTest = "test";

            $event->setReturnValue($obj);
            $event->stop();
        });

        $service = $this->object->get('test.service');
        $this->assertFalse(isset($service->test));
        $this->assertTrue(isset($service->notTest));
    }

    public function testAfterServiceLoadedEventIsSent()
    {
        $ref = new \stdClass();
        $ref->testing = false;
        $this->object->on('afterServiceLoaded', function(AfterServiceLoadedEvent $event) use ($ref) {
             $ref->testing = true;
        });
        $this->assertFalse($ref->testing);
        $this->object->get('test.service');
        $this->assertTrue($ref->testing);
    }
}