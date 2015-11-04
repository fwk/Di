# Exemple usages of Fwk\Di

Let's take the exemple from the [introduction](./README.md): this ```Database``` class:

``` php
namespace MyApp\Db;

/**
 * Connects to the database and fetches data
 */
class Database
{
    /**
     * Connection DSN
     * @var string
     */
    protected $dsn;

    /**
     * Constructor
     *
     * @param string $dsn    Connection DSN
     * @param array  $params Optional parameters
     */
    public function __construct($dsn, array $params = array())
    {
        /* ... */
    }
}

```

## Definition

To instantiate this class from the Container you need to write a Definition. You can do it using: 

### PHP Closure:
``` php
$container->set('db', function(Container $container) {  
    return new MyApp\Db\Database('mysql:dbname=testdb;host=127.0.0.1', array('charset' => 'utf8'));
});
```
Note: A Closure Definition always takes the Container as argument.

### Class Definition (```Fwk\Di\ClassDefinition```)
``` php
$container->set('db', \Fwk\Di\ClassDefinition(
    'MyApp\Db\Database',
    array(
        'mysql:dbname=testdb;host=127.0.0.1',
        array('charset' => 'utf8')
    )
));
```

### XML
``` xml
<array-definition name="db.config">
    <param key="charset">utf8</param>
</array-definition>

<class-definition name="db" class="MyApp\Db\Database">
    <argument>mysql:dbname=testdb;host=127.0.0.1</argument>
    <argument>@db.config</argument>
</class-definition> 
```
This exemple is a little more complex because we need to create an ```db.config``` definition (```Fwk\Di\ArrayDefinition```) that we use as our second constructor argument as a Reference (explained later bellow). [Learn more about XML definitions](./container-builder.md).

## Usage 

Now the Container will instantiate ```MyApp\Db\Database``` everytime we need it and call:
``` php 
$db = $container->get('db');
```

## Shared instances

As it is described above you'll create a new instance of your object everytime you call ```$container->get('db')```. You can decide to share the same instance (like Singleton) when you create the definition:

``` php
$container->set('db', $definition, $shared = true);
```
or
``` xml
<class-definition name="db" class="MyApp\Db\Database" shared="true" />
```

## Method Calls

It is sometimes useful to call methods from the object we've just instantiated. We can easily do so by using ```$definition->addMethodCall(<methodName>, <arguments[]>)```

``` php
$definition = new ClassDefinition('MyApp\Db\Database', array('mysql:dbname=testdb;host=127.0.0.1'));
// define the charset
$definition->addMethodCall('setCharset', array('utf8'));
```
or
``` xml
<class-definition name="db" class="MyApp\Db\Database">
    <argument>mysql:dbname=testdb;host=127.0.0.1</argument>
    <call method="setCharset">
        <argument>utf8</argument>
    </call>
</class-definition> 
```

## References

A Reference is a symbolic link to another definition. Just like we did before with ```@db.config``` in the XML definition. References are always prefixed with ```@``` to be used as constructor arguments and methods arguments.

``` php
// define options using the @db.config definition
$definition->addMethodCall('setOptions', array('@db.config'));
```

## Properties

The Definitions can become extremely complex to write and read. Properties are simple string variables inside your definitions that can be used to configure the Container without editing the definitions. 

Properties are always prefixed with ```:``` like ```:databaseDsn``` and can be used everywhere in your definitions.

The ```$dsn``` argument is a good example of what can be a property:
``` php
if (ENV == "prod") {
    $container->setProperty('databaseDsn', 'mysql:dbname=myapp;host=mysql-host.prod');
} else {
    $container->setProperty('databaseDsn', 'mysql:dbname=testdb;host=127.0.0.1');
}

$definition = new ClassDefinition('MyApp\Db\Database', array(':databaseDsn'));
```
or
``` xml
<class-definition name="db" class="MyApp\Db\Database">
    <argument>:databaseDsn</argument>
    <call method=":dynamicMethodName" />
</class-definition> 
```

Properties can also be defined in an external ini file of your choice and used like so:
``` ini
[services]
databaseDsn=mysql:dbname=testdb;host=127.0.0.1
```

Import the file:
``` php
$container->iniProperties('/path/to/properties.ini', $category = "services")
```
or
``` xml
<ini category="services">properties.ini</ini>
```
Note: INI sections will be ignored if ```$category``` is not defined.

Last but not least, you can use Properties inside Properties:
``` php
$container->setProperty('baseDir', __DIR__);
$container->setProperty('tempDir', ':baseDir/tmp');
```
