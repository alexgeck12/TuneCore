TuneCore
========

Simple Framework with MVC structure


Config
======
```ini
[db/main] // mysql config
host = '127.0.0.1';
user = 'login';
pass = 'password';
name = 'dbname';

[redis/main] // redis config
host = '127.0.0.1';
port = '6379';
db = 0;
```
Suffix /main is default if you don`t specify concrete connection config

Workflow
========

All request must go to public/index.php
In index.php you can specify concrete Application to run(by default will be Main)

Example:
```php
<?php
define(ROOTDIR, realpath(__DIR__.'/../'));
include_once ROOTDIR.'/Core/Boot.php';
if (substr($_SERVER['REQUEST_URI'], 0, 6) == '/admin') {
	Core\Kernel::app('Admin')->run();
} else {
	Core\Kernel::app()->run();
}
```

After run Application it will ask routes from [Application name]/route.php, than if it finds instructions for Controller and Action
if All is ok, it`s start beforeAction() from this controller, than [Concrete Action](), than afterAction()

