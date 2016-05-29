<?php
namespace Fwk\Di\Tests\Xml;;

use Fwk\Di\Events\BeforeServiceLoadedEvent;

class TestXmlListenerOverride
{
    public function onBeforeServiceLoaded(BeforeServiceLoadedEvent $event)
    {
        $event->getDefinition()->set('listener-override', true);
    }
}