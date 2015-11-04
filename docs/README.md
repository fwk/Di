# Fwk\Di Documentation

Fwk\Di is a simple and efficient [Dependency Injection](https://en.wikipedia.org/wiki/Dependency_injection) Container for PHP 5.3+. 

Dependency Injection is a Design Pattern helping to organize dependencies and the way they's created. It's a good way to optimize an the efficiency of an application as the required resources are created only when they're needed. 

Fwk\Di comes with useful features out of the box:

* Events: implement rapidly and easily extensions and plugins
* Proxies: resource-hungry objects can be "proxied" when they're only used as a reference.
* Lightweight: The lighter, the faster.

### Vocabulary

* Dependency: Something (an Object, an Array, a Resource ...) the application will require at runtime. 
* Service: Common term for Dependency.
* Definition: The description of a dependency (read: the way it is created).
* Property: String parameter defined in the container usable in Definitions.
* Reference: Symbolic link to another definition.
* Proxy: A fake, lightweight object replacing the instance of the real one until it is really needed.

## Basic Usage

Here is an example of a common dependency in an application: the *Database* object (aka [DAO](https://en.wikipedia.org/wiki/Data_access_object)). 

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

Without DI we would normally have instanciated this class on startup to make it available for our Controllers. 

``` php
$db = new MyApp\Db\Database('mysql:dbname=testdb;host=127.0.0.1', array('charset' => 'utf8'));
```
This approach is fine because it works like it should. However the object is instantiated even when not needed, which can lead to memory problems when a lot of dependencies are needed.

This is where Dependency Injection comes in! With the help of Definitions, the  Container will learn how to instantiate this object and make it available only when called. To keep it simple, we'll just use a Closure as a Defintion.

``` php
$container->set('db', function($container) {  
    return new MyApp\Db\Database('mysql:dbname=testdb;host=127.0.0.1', array('charset' => 'utf8'));
});
```
Now that the Definition of the Database object is registered, the Container will instantiate the object when needed:
``` php 
$db = $container->get('db');
```
Huge isn't it? :)

## Dig the documentation

Of course, this is a really simple exemple of what can be done with Fwk\Di.

* [Exemples usages](./exemples.md)
* [Definitions Types](./definitions.md)
* [Write Definitions using XML](./xml-container-builder.md)
* [Extend functionalities with Listeners](./extend.md)
