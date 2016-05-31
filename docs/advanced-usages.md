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
    $name = $event->getServiceName();

    if (true === $event->getDefinition()->get('deprecated', false)) {
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

## Definition (meta) Data

Definition Data is a powerful feature allowing you to define properties on Definitions and *search them*.

Imagine a LoginManager having multiples LoginProvider (eg: FormProvider, FacebookProvider...). You may want to register your Providers when your Manager is instanciated. Easy!
First, define a way to identify Providers definitions = create a parameter ```loginProvider``` set to ```true```:

``` php
$container->set(
    'login.provider.fb', 
    ClassDefinition::factory('MyApp\Providers\FacebookProvider', array(/* fb.config */))
        ->set('loginProvider', true)
);
```
or using XML:
``` xml
<class-definition name="login.provider.facebook" class="MyApp\Providers\FacebookProvider">
    <argument>@fb.config</argument>
    <data>
        <param key="loginProvider">true</param>
    </data>
</class-definition> 
```

Now, create the Listener that does the magic:
``` php
$container->on('afterServiceLoaded', function (AfterServiceLoadedEvent $event) {
    if ($event->getValueObject() instanceof LoginProvider) {
        $defs = $event->getContainer()->search(array('loginProvider' => true));
        foreach ($defs as $defName => $definition) {
            /** @var DefinitionInterface $definition */
            $event->getValueObject()->addProvider($definition->invoke($event->getContainer(), $defName));
        }
    }
});
```

The search method accepts wildcards:``` $container->search(array('dependency_type' => 'e?tity_*')); ```. 