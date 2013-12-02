<?php
namespace Core;

class Db extends \mysqli
{
	protected static $instance;
	private function __clone()    {  }
	private function __wakeup()   {  }

	public static function getInstance($db = 'main') {
		if ( is_null(self::$instance) ) {
			self::$instance[$db] = new self($db);
		}
		return self::$instance[$db];
	}

	private function __construct($db){
        $conf = Registry::getInstance()->get('db/'.$db);
		parent::__construct($conf['host'], $conf['user'], $conf['pass'], $conf['name']);
        $this->set_charset('utf8');
	}

	public function insert($table, $data, $duplicate_update = false)
	{
		foreach ($data as $field => $value) {
			$values[] = "`$field` = '".addcslashes($value, "'")."'";
		}

		$sql = "INSERT INTO `$table` SET ".implode(',' ,$values);
        if($duplicate_update) {
            $sql .= " ON DUPLICATE KEY UPDATE ".implode(',' ,$values);
        }

        $this->query($sql);
        return $this->error?$this->error:$this->insert_id;
	}

	public function multi_insert($table, $data, $duplicate_update = false){
		foreach ($data as $i => $row) {
			foreach ($row as $field => $value) {
				$fields[$field] = "`$field`";
				$values[$i][$field] = "'".addcslashes($value, "'")."'";
			}
			ksort($values[$i]);
			$values[$i] = '('.implode(',', $values[$i]).')';
		}
		ksort($fields);

		if($duplicate_update) {
			foreach ($fields as $field) {
				$update_fields[] = "`$field` = VALUE(`$field`)";
			}
		}

		$sql = "INSERT INTO `$table`(".implode(', ' ,$fields).") VALUES ".implode(',' ,$values);
		if($duplicate_update) {
			$sql .= " ON DUPLICATE KEY UPDATE ".implode(',',$update_fields);
		}
		return $this->query($sql)->affected_rows;
	}

    public function replace($table, $data)
    {
        foreach ($data as $field => $value) {
            $values[] = "`$field` = '".addcslashes($value, "'")."'";
        }

        $sql = "REPLACE INTO `$table` SET ".implode(', ' ,$values);
		return $this->query($sql)->affected_rows;
    }

	public function update($table, $data, $params)
	{
		if(is_array($data) && !empty($data)) {
			foreach ($data as $field => $value) {
				$values[] = "`$field` = '".addcslashes($value, "'")."'";
			}

			foreach ($params as $field => $value) {
				$where[] = "`$field` = '".addcslashes($value, "'")."'";
			}

			$sql = 'UPDATE `'.$table.'` SET '.implode(', ' ,$values).' WHERE '.implode(' AND ', $where);
			return $this->query($sql)->affected_rows;
		} else {
			return false;
		}
	}

	public function delete($table, $params)
	{
		foreach ($params as $field => $value) {
            if(is_array($value)) {
                foreach ($value as &$val) {
                    if((int)$val != $val) {
                        $val = '"'.addcslashes($val, '"').'"';
                    }
                }
                $value = implode(',', $value);
                $where[] = "`$field` IN (".$value.")";
            } else {
                $where[] = "`$field` = '".addcslashes($value, "'")."'";
            }
		}

		$sql = 'DELETE FROM `'.$table.'` WHERE '.implode(' AND ', $where);
		return $this->query($sql)->affected_rows;
	}

	public function getFiltered($table, $params){
		foreach ($params as $field => $value) {
			if(!is_array($value)) {
				$where[] = "`$field` = '".addcslashes($value, "'")."'";
			} else {
				foreach ($value as &$item) {
					$item = addcslashes($item, "'");
				}

				$where[] = "`$field` IN ('".implode("','", $value)."')";
			}

		}
		$sql = 'SELECT * FROM `'.$table.'` WHERE '.implode(' AND ', $where);
		return $this->query($sql)->fetch_all(MYSQL_ASSOC);
	}

	public function getRowByKeys($table, $params){
		foreach ($params as $field => $value) {
			$where[] = "`$field` = '".addcslashes($value, "'")."'";
		}
		$sql = 'SELECT * FROM `'.$table.'` WHERE '.implode(' AND ', $where);
		return $this->query($sql)->fetch_assoc();
	}
}