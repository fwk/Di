<?php
namespace Fwk\Di\Tests\Xml;;

use Fwk\Di\Events\BeforeServiceLoadedEvent;

class TestXmlServiceListener
{
    public function onBeforeServiceLoaded(BeforeServiceLoadedEvent $event)
    {
        $event->getDefinition()->set('service-listener', true);
    }
}