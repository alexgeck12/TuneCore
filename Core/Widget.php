<?php
namespace Core;

abstract class Widget
{
	public function __construct($data)
	{
		if(is_array($data)) {
			foreach ($data as $param => $value) {
				$this->$param = $value;
			}
		}
	}

	abstract function init();

	public function render($template = 'index', $data = array(), $return = false)
	{
		$app = kernel::app();
		$template = $app->root_dir.'/'.str_ireplace('\\', '/', get_called_class()).'/'.$template.'.php';
		if(!file_exists($template)) {
			return '<h3 style="display: block; text-align: center; width: 100%;">не найден шаблон '.$template.'</h3>';
		}
        if($return){
            ob_start();
        }
		extract($data);
		include_once $template;
        if($return){
            $html = ob_get_contents();
            ob_end_clean();
            return $html;
        }
	}

	public function widget($name, $data)
	{
		$widget = kernel::app()->application.'\Widgets\\'.ucfirst($name);
		$widget = new $widget($data);
		$widget->init();
	}
}