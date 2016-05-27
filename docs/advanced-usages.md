# Advanced Usages

Fwk\Di comes with handy functionnalities to help you extend it. 

## Event Listeners

The Container send Events you can interact with. 
*NOTE*: Events are not triggered when using services from other containers (aka: delegates)

| Event                   | Description                                                                          |
|:------------------------|--------------------------------------------------------------------------------------|
| beforeServiceLoaded     | Sent before a Service is loaded ($container->get())                                  |
| afterServiceLoaded      | Sent after a Service is loaded                                                       |
| beforeServiceRegistered | Sent before a Service is registered ($container->set())                              |
| afterServiceRegistered  | Sent after a Service is registered                                                   |

For example, you could use Definition data to flag certain services as deprecated:
``` php
$container->on('beforeServiceLoaded', function(BeforeServiceLoadedEvent $event) {
    $data = $event->getDefinitionData();
    $name = $event->getServiceName();

    if (isset($data['deprecated'])) {
        trigger_error("The service '$name' is deprecated and should not be used anymore.", E_USER_DEPRECATED);
    }
});
``` 

Another example is the ```ContainerAwareInterfaceListener``` who calls the ```setContainer``` every time a ```ContainerAwareInterface``` object is loaded:
``` php
class ContainerAwareInterfaceListener
{
    public function onAfterServiceLoaded(AfterServiceLoadedEvent $event)
    {
        $obj = $event->getReturnValue();
        if ($obj instanceof ContainerAwareInterface) {
            $obj->setContainer($event->getContainer());
        }
    }
}
```

## Definition Data

Definition Data is a powerful feature allowing you to define properties on Definitions and *search them*.

Imagine a LoginManager having multiples LoginProvider (eg: FormProvider, FacebookProvider...). A little event listener may help you load all registered providers easily, using a simple metadata parameter:

