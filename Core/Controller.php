<?php
namespace Core;

abstract class Controller
{
    public $layout;

	public function beforeAction(){}
	public function afterAction(){}

    public function render($template = false, $data = array())
    {
        if(!$this->layout) {
            $this->layout = 'main';
        }
		$app = kernel::app();
        $layout = $app->root_dir.'/'.$app->application.'/Views/Layout/'.$this->layout.'.php';
        $content = $this->renderPartial($template, $data);
        ob_start();
        include_once $layout;
        ob_end_flush();
    }

    public function renderPartial($template, $data)
    {
        ob_start();
        if(!$template) {
            foreach (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $trace) {
                if ($trace['class'] == get_called_class()) {
                    $template = $trace['function'];
                }
            }
        }

		$app = kernel::app();
        $template = $app->root_dir.'/'.$app->application.'/Views/'.str_replace(array($app->application."\Controllers\\", "\\"), array("", "/"), get_called_class()).'/'.$template.'.php';
        if(!file_exists($template)) {
            return '<h3 style="display: block; text-align: center; width: 100%;">не найден шаблон '.$template.'</h3>';
        }
        extract($data);
        include_once $template;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    public function redirect($url)
    {
        header('Location: '.$url);
    }

	public function widget($name, $data)
	{
		$widget = kernel::app()->application.'\Widgets\\'.ucfirst($name);
		$widget = new $widget($data);
		$widget->init();
	}
}