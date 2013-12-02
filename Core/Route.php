<?php
namespace Core;

class Route
{
	public static $controller = 'main';
	public static $action = 'index';
    public static $args = array();
    private static $routes = array();


	public static function start()
	{
		Registry::getInstance()->get('routes');
        $request = explode('/', trim(reset(explode('?',$_SERVER['REQUEST_URI'])), '/'));

        self::prepare(Registry::getInstance()->get('routes'));

        if (isset(self::$routes[count($request)])){
            $founded = false;
            foreach (self::$routes[count($request)] as $route => $info) {
                if(!$founded) {
                    $route_part = explode('/', trim($route, '/'));
                    foreach ($route_part as $i => $rule) {
                        if(substr($rule, 0, 1) == '(') {
                            $rule = substr($rule, 1, -1);
                            list($param, $regexp)=explode(':', $rule);
                            if(preg_match('/^'.$regexp.'$/is', $request[$i])) {
                                $founded = true;
                                self::$controller = $info['controller'];
                                self::$action = $info['action'];
                                self::$args[$param] = $request[$i];
                            } else {
                                $founded = false;
								break;
                            }
                        } elseif($rule == $request[$i]) {
                            $founded = true;
                            self::$controller = $info['controller'];
                            self::$action = $info['action'];
                        } else {
                            $founded = false;
							break;
                        }
                    }
                } else {
                    break;
                }
            }
        } else {
            header("HTTP/1.0 404 Not Found");
            exit;
        }
        if(!$founded) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }

        if(strpos(self::$controller, '$') === 0) {
            self::$controller = self::$args[substr(self::$controller, 1)];
        }

        if(strpos(self::$action, '$') === 0) {
            self::$action = self::$args[substr(self::$action, 1)];
        }
    }

    public static function prepare($routes)
    {
        foreach ($routes as $rule => $info) {
            $rules = explode('/', trim($rule, '/'));
            self::$routes[count($rules)][$rule] = $info;
        }
    }
}