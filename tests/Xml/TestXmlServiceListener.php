<?php
namespace Fwk\Di\Tests\Xml;;

use Fwk\Di\Events\BeforeServiceLoadedEvent;

class TestXmlServiceListener
{
    public function onBeforeServiceLoaded(BeforeServiceLoadedEvent $event)
    {
        $data = $event->getDefinitionData();
        $data['service-listener'] = true;
        $event->setDefinitionData($data);
    }
}