<?php
function __autoload($class){
	$class = str_ireplace('\\', '/', $class);

	$root = realpath(__DIR__.'/../');
	if (file_exists($root.'/'.$class.'.php')) {
		include_once $root.'/'.$class.'.php';
	} else {
		die("Class $class not found");
	}
}