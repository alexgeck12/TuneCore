<?php
namespace Core;

class Registry
{
	protected static $instance;
	private function __clone()    { /* ... @return Singleton */ }
	private function __wakeup()   { /* ... @return Singleton */ }

	private $registry = array();
	private $registry_file = '';
	private $data_changed = false;
    private $just_created = true;

	public static function getInstance()
	{
		if (is_null(self::$instance) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct()
	{
		/*$this->registry_file = Kernel::app()->root_dir.'/'.Kernel::app()->application.'/registry.dat';
		/if(file_exists($this->registry_file)) {
            $this->just_created = false;
			$this->registry = unserialize(file_get_contents($this->registry_file));
		} else {
			file_put_contents($this->registry_file, serialize($this->registry));
		}*/

	}

	public function get($var)
	{
        if (!isset($this->registry[$var])) {
            return false;
        }
		return $this->registry[$var];
	}

	public function set($var, $data)
	{
		if(!isset($this->registry[$var]) || $this->registry[$var] != $data) {
			$this->data_changed = true;
		}
		return $this->registry[$var] = $data;
	}

	public function ctime()
	{
        if($this->just_created) {
            return 0;
        }
		return filectime($this->registry_file);
	}

	public function remove($var)
	{
		unset($this->registry[$var]);
	}

    public function __destruct()
    {
        /*if($this->data_changed) {
            file_put_contents($this->registry_file, serialize($this->registry));
        }*/
    }
}