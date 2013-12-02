<?php
namespace Core;

class Kernel
{
    protected static $instance;
	public $application;
    public $root_dir;

    private function __clone()    {  }
    private function __wakeup()   {  }

    public static function app($application = 'Main')
    {
        if (is_null(self::$instance) ) {
            self::$instance = new self($application);
        }
        return self::$instance;
    }

    private function __construct($application)
    {
		$this->application = $application;
        $this->root_dir = realpath(__DIR__.'/../');
    }

    public function run()
    {
		$Registry = Registry::getInstance();

        $config = parse_ini_file($this->root_dir.'/config.ini', true);
        foreach ($config as $var => $data) {
            $Registry->set($var, $data);
        }

        $route_file = $this->root_dir.'/'.$this->application.'/routes.php';
        if($Registry->ctime() < filectime($route_file)) {
            $routes = include_once $route_file;
            $Registry->set('routes', $routes);
        }

		Route::start();
		$controller = $this->application.'\Controllers\\'.ucfirst(Route::$controller);
		$controller = new $controller();
		$controller->beforeAction();
		$controller->{Route::$action}();
		$controller->afterAction();
    }
}