# Definitons 

Definitions describe the way a Dependency should be created.

| Name                | Type     | Class name                             | Description                          
|:--------------------|:--------:|:---------------------------------------|:-------------------------------------|
| ArrayDefinition     | array    | Fwk\Di\Definitions\ArrayDefinition     | Describes a PHP array                |
| ClassDefinition     | object   | Fwk\Di\Definitions\ClassDefinition     | Describes a PHP object instantiation |
| CallableDefinition  | callable | Fwk\Di\Definitions\CallableDefinition  | Describes a PHP callable             |
| LazyClassDefinition | object   | Fwk\Di\Definitions\LazyClassDefinition | Describes a PHP Proxy object         |
| ScalarDefintiion    | any      | Fwk\Di\Definitions\ScalarDefinition    | Describes any other PHP value        |

## ArrayDefinition

Describes a simple [PHP Array](http://php.net/array). This type of definition is generally used as a [Reference](./exemples.md#References) parameter. 

``` php
$container->set('my-array', array(
    'foo' => 'bar',
    'db' => '@db'
));
```
using XML:
``` xml
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
)->addMethodCall('setCharset', 'utf8')); // optional method call
```
or using XML:
``` xml
<array-definition name="db.config">
    <param key="charset">utf8</param>
</array-definition>

<class-definition name="db" class="MyApp\Db\Database">
    <argument>mysql:dbname=testdb;host=127.0.0.1</argument>
    <argument>@db.config</argument>
    <call method="setCharset">utf8</call>
</class-definition> 
```

## CallableDefinition

Describes the call of a [PHP Callable](http://php.net/manual/en/language.types.callable.php). The callable is called each time you require the service (except if is [shared](./exemples.md#Shared-instances)). 

``` php
$container->set('std', function($container) {
   return new \stdClass();
});
```

## LazyClassDefinition

TODO