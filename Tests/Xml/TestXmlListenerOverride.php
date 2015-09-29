<?php
namespace Fwk\Di\Tests\Xml;;

use Fwk\Di\Events\BeforeServiceLoadedEvent;

class TestXmlListenerOverride
{
    public function onBeforeServiceLoaded(BeforeServiceLoadedEvent $event)
    {
        $data = $event->getDefinitionData();
        $data['listener-override'] = true;
        $event->setDefinitionData($data);
    }
}