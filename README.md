# blade-debugm2o

支持blade环境下切换模板路径，解决多前端对单后端联调情况下，容易覆盖模板的问题

## 使用

页面url中通过debug-ns参数指定需要切换的路径，如http://localhost:9999?debug-ns=abc，则会自动切换至默认模板目录下的abc目录。

退出浏览器或指定url为http://localhost:9999?debug-ns，则自动清空记录


## 项目配置

### laravel

安装
```sh
composer require blade/debugm2o
```

项目providers配置 config/app.php
```php
<?php
return [
    'providers' => [
        //some some some provider
        'Blade\DebugM2O\DebugProvider'
    ]
];
```

### blade独立包，[传送](https://github.com/jenssegers/blade)

安装
```sh
composer require blade/debugm2o
```

使用
```php
<?php
define('ROOT', dirname(__DIR__));
define('CACHE_ROOT', ROOT . '/cache');
define('VIEW_ROOT', ROOT . '/view');

require ROOT . '/vendor/autoload.php';

use Illuminate\Container\Container;
use Jenssegers\Blade\Blade;
use Blade\DebugM2O\DebugProvider;

$container = new Container;
$blade = new Blade(VIEW_ROOT, CACHE_ROOT, $container);
$config = $container['config'];
//兼容下独立blade包无法正常读取 view.xx的bug
$config['view'] = [
    'paths' => $blade->viewPaths,
    'compiled' => $blade->cachePath
];
$container['config'] = $config;

(new DebugProvider($container))->register();
echo $blade->make($path, array(/*页面数据*/))->render();
```

