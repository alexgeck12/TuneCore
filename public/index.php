<?php
define('ROOTDIR', realpath(__DIR__.'/../'));
include_once ROOTDIR.'/Core/Boot.php';
if (substr($_SERVER['REQUEST_URI'], 0, 6) == '/admin') {
	Core\Kernel::app('Admin')->run();
} else {
	Core\Kernel::app()->run();
}