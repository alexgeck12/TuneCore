<?php
namespace Core;

abstract class Model
{
    /**
     * @var $db Db
     */
    protected $db = 'main';
	protected $rdb = 'main';
    protected $redis;
	protected $table;

	public function __construct()
	{
		$this->db = Db::getInstance(($this->db)?:'main');
        $this->redis = Redis::getInstance(($this->rdb)?:'main');

        if(!$this->table) {
            $this->table = strtolower(get_class($this));
        }
	}
}