<?php
namespace Blade\DebugM2O;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\FileViewFinder;

class DebugProvider extends ServiceProvider{
    protected $namespace = '';

    public function register()
    {
        $this->initNamespace();
        $this->registerFeatherResource();
        $this->registerViewFinder();
    }

    public function registerFeatherResource()
    {   
        //support feather2 or lothar
        if (!class_exists('\\Feather2\\Resource\\Resources')) {
            return false;
        }

        $this->app->singleton('feather.resource', function ($app) {
            $config = $app['config']['view'];
            $config['cacheDir'] = $config['compiled'] . '/feather';

            if (!isset($config['cache'])) {
                $config['cache'] = true;
            }

            return new \Feather2\Resource\Resources($this->getViewPaths(), $config);
        });
    }

    public function registerViewFinder()
    {
        $this->app->bind('view.finder', function($app)
        {
            return new FileViewFinder($app['files'], $this->getViewPaths());
        });
    }

    protected function initNamespace(){
        if (isset($_GET['debug-ns'])) {
            if (empty($_GET['debug-ns'])) {
                setcookie('debug-ns', '', time() - 1);
            } else {
                setcookie('debug-ns', $this->namespace = $_GET['debug-ns']);
            }
        } else if (!empty($_COOKIE['debug-ns'])) {
            $this->namespace = $_COOKIE['debug-ns'];
        }
    }

    protected function getViewPaths(){
        $paths = (array)$this->app['config']['view.paths'];

        if (!$this->namespace) {
            return $paths;
        }

        foreach ($paths as $key => $path) {
            $paths[$key] = $path . '/' . $this->namespace;
        }

        return $paths;
    }
}