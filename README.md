Simple old school debug class from early php5 days. 

Can be handy still to this day for quick debugging in old legacy projects.

Does one thing only. Sort of a super-stupid console.log for php.

Dumps to screen or logfile.

Install:
```
composer require --dev "pa-ulander/debug @dev"
```

In composer.json add to autoload:
```json
    "autoload-dev": {
        "psr-0": {
            "debug": "vendor/pa-ulander/debug"
        }
    },
```

Use:
```php
new \debug\debug($some_thing_to_debug); // logs to screen

new \debug\debug($some_thing_to_debug, 1); // logs to file

new \debug\debug($some_thing_to_debug, 2); // logs to file with backtrace
```
