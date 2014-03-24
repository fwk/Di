# Fwk\Di (Dependency Injection)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fwk/Di/badges/quality-score.png?s=1f384822977a9e5c941466034ab35a2266d132d4)](https://scrutinizer-ci.com/g/fwk/Di/)
[![Build Status](https://secure.travis-ci.org/fwk/Di.png?branch=master)](http://travis-ci.org/fwk/Di)
[![Code Coverage](https://scrutinizer-ci.com/g/fwk/Di/badges/coverage.png?s=1bd36bba6a4e9e86d219c91fcaef55c846f676a1)](https://scrutinizer-ci.com/g/fwk/Di/)
[![Latest Stable Version](https://poser.pugx.org/fwk/di/v/stable.png)](https://packagist.org/packages/fwk/di) 
[![Total Downloads](https://poser.pugx.org/fwk/di/downloads.png)](https://packagist.org/packages/fwk/di) 
[![Latest Unstable Version](https://poser.pugx.org/fwk/di/v/unstable.png)](https://packagist.org/packages/fwk/di) 
[![License](https://poser.pugx.org/fwk/di/license.png)](https://packagist.org/packages/fwk/di)

Dependency Injection Container for PHP 5.3+ 

## Installation

Via [Composer](http://getcomposer.org):

```
{
    "require": {
        "fwk/di": "dev-master",
    }
}
```

If you don't use Composer, you can still [download](https://github.com/fwk/Di/zipball/master) this repository and add it
to your ```include_path``` [PSR-0 compatible](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md)

## Documentation

### Simple Di Container

Creating a Container and register a service:

``` php
use Fwk\Di\Container;
use Fwk\Di\ClassDefinition;

$container = new Container();

// Use a ClassDefinition to instanciate any class
$dbDef = new ClassDefinition('App\Services\Database');
$dbDef->addMethodCall('connect');

$container->set('db', $dbDef, true /* shared = singleton */);

// Use a Closure
// NOTE: first argument of a closure is the Container itself
$container->set('myObj', function($container) {
    return new MyObj($container->get('@myObj.config'));
});

// Store any type (string, int, array, object ...) 
$container->set('myParam', 'value');
```

(Only) when needed, the service is created:

``` php
$db = $container->get('db');

// every call to the 'myObj' definition will create a new instance of MyObj
// since it was not registered as "shared"
$myObj = $container->get('myObj');
$myObj2 = $container->get('myObj');
```

### Load ini properties

config.ini:

``` ini
[services]
db.hostname = localhost
db.user = myuser
db.password = mypass
db.driver = pdo_mysql
db.database = dbname
```

``` php
$container->iniProperties(__DIR__ .'/config.ini', 'services' /* ini [section] */);

$container->set(
   'db',
   new ClassDefinition('App\Services\Database', 
    array(
        '@db.hostname', 
        '@db.user', 
        '@db.password',
        '@db.driver', 
        '@db.database', 
    )),
    true
);
```

### Use the Container Builder

The ContainerBuilder allows you to write definition in an Xml file:

``` xml
<?xml version="1.0" encoding="UTF-8"?>
<dependency-injection>
    <class-definition name="myObj" class="Fwk\Di\ClassDefinition" shared="true">
        <argument>\stdClass</argument>
        <call method="addArgument">
            <argument>ArgOne</argument>
        </call>
    </class-definition>
    <definition name="testDef">valueOfDefinition</definition>
</dependency-injection>
```

Then, you load the definitions when needed:
``` php
use Fwk\Di\Xml\ContainerBuilder;

$builder = new ContainerBuilder();

/* a new container will be created if $container is null */
$container = $builder->execute('./di-services.xml', $container);
```

## Legal 

Fwk is licensed under the 3-clauses BSD license. Please read LICENSE for full details.
