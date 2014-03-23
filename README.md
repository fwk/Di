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

// database
$dbDef = new ClassDefinition('App\Services\Database');
$dbDef->addMethodCall('connect');

$container->set('db', $dbDef, true /* singleton */);
```

(Only) when needed, the service is created:

``` php
$db = $container->get('db');
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

## Legal 

Fwk is licensed under the 3-clauses BSD license. Please read LICENSE for full details.

```
Copyright (c) 2012-2013, Julien Ballestracci <julien@nitronet.org>.
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions
are met:

 * Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.

 * Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in
   the documentation and/or other materials provided with the
   distribution.

 * Neither the name of Julien Ballestracci nor the names of his
   contributors may be used to endorse or promote products derived
   from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
POSSIBILITY OF SUCH DAMAGE.
```