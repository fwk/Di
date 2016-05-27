# Definitons 

Definitions describe the way a Dependency should be created.

| Name                | Type     | Class name                             | Description                          
|:--------------------|:--------:|:---------------------------------------|:-------------------------------------|
| ArrayDefinition     | array    | Fwk\Di\Definitions\ArrayDefinition     | Describes a PHP array                |
| ClassDefinition     | object   | Fwk\Di\Definitions\ClassDefinition     | Describes a PHP object instantiation |
| CallableDefinition  | callable | Fwk\Di\Definitions\CallableDefinition  | Describes a PHP callable             |
| LazyClassDefinition | object   | Fwk\Di\Definitions\LazyClassDefinition | Describes a PHP Proxy object         |

## ArrayDefinition

Describes a simple [PHP Array](http://php.net/array). This type of definition is generally used as a [Reference](./exemples.md#References) parameter. 

``` php
$container->set('my-array', array(
    'foo' => 'bar',
    'db' => '@db'
));
```
using XML:
``` php
<array-definition name="my-array">
    <param key="foo">bar</param>
    <param key="db">@db</param>
</array-definition>
```


## ClassDefinition

Describes the instantiation of a [PHP Object](http://php.net/object). This is the most common and useful definition.

``` php
use Fwk\Di\Definitions\ClassDefinition;

$container->set('db', ClassDefinition::factory(
  'MyApp\Db\Connection', // full classname
  array('@db.config') // constructor parameters
)->addMethodCall('setCharset', array('utf8'))); // optional method call
```

## CallableDefinition

Describes the call of a [PHP Callable](http://php.net/manual/en/language.types.callable.php). The callable is called each time you require the service (except if is [shared](./exemples.md#Shared-instances)). 

``` php
$container->set('std', function($container) {
   return new \stdClass();
});
```

## LazyClassDefinition

Describes a [PHP Object](http://php.net/object). It simply call the function each time you require the service (except if [shared](./exemples.md#Shared-instances))


Describes a simple [PHP Object](http://php.net/object). This is the main type of definition as it is the one that actually construct your objects. 
