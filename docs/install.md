# Install Fwk\Di

If you don't have [Composer](http://getcomposer.org) yet, download it or just run the following command:

``` sh
$ curl -s http://getcomposer.org/installer | php
```

Then require fwk/di as a dependency of your project in ```composer.json```:
``` javascript
{
    "require": {
        "fwk/di": "dev-master",
    }
}
```

and finally install it!
``` sh
$ php composer.phar install (or update)
```

Fwk\Di is now ready to [use](./usage.md) !