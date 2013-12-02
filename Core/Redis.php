<?php
namespace Core;

class Redis
{
    protected static $instance;
    private function __clone()    { /* ... @return Singleton */ }
    private function __wakeup()   { /* ... @return Singleton */ }
	private function __construct(){ /* ... @return Singleton */ }

    public static function getInstance($rdb) {
        if ( is_null(self::$instance[$rdb]) ) {
            self::$instance = new \Redis();
			$conf = Registry::getInstance()->get('redis/'.$rdb);

            self::$instance->connect($conf['host'], $conf['port']);
        }
        return self::$instance[$rdb];
    }
}