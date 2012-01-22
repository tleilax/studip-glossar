<?php
/**
 * Requires PHP version 5.3
 */

if (version_compare(phpversion(), '5.3', '<')) {
	throw new Exception('Plain_ORM requires at least PHP version 5.3.0');
 }

class Plain_ORM implements ArrayAccess {

	protected static $TABLE = false,
		$ID_COLUMN = false,
		$ORDER = false,
		$COLUMNS = array(),
		$CONNECTIONS = array();
/** **/

	protected static $cache = array(), // TODO: Implement
		$connection_cache = array();

	protected static $context = false;

	public static function SetContext($context = false) {
		$old = self::$context;
		self::$context = $context;
		return $old;
	}

    public static function Fetch($id) {
        return new static($id);
    }

	protected static function LoadFromDB($ids) {
		if (empty($ids)) {
			return array();
		}
		
		$columns = '`'.implode('`, `', array_keys(static::$COLUMNS)).'`';
		$id_string = implode(',', array_map(array(DBManager::get(), 'quote'), $ids));

		$query = "SELECT {$columns} FROM `".static::$TABLE."`";
		$query .= " WHERE `".static::$ID_COLUMN."` IN (".$id_string.")";
		if (self::$context) {
		    $query .= " AND `context` = ".DBManager::get()->quote(self::$context);
		}
		if (!empty(static::$ORDER)) {
			$query .= " ORDER BY ".static::$ORDER;
		}

		// Hate to not use prepared statement but php gives me strange errors
		$statement = DBManager::get()->query($query);

		return $statement->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function Retrieve($ids) {

		if (!isset(self::$cache[static::$TABLE])) {
			self::$cache[static::$TABLE] = array();
		}

		$return_array = is_array($ids);
		if (!is_array($ids)) {
			$ids = array($ids);
		}

		$not_in_cache = (array)array_diff($ids, array_keys(self::$cache[static::$TABLE]));
		foreach (self::LoadFromDB($not_in_cache) as $row) {
			self::$cache[static::$TABLE][$row[static::$ID_COLUMN]] = $row;
		}

		$result = array();
		foreach ($ids as $id) {
			if (!isset(self::$cache[static::$TABLE][$id])) {
				self::$cache[static::$TABLE][$id] = false;
			}
			$result[$id] = self::$cache[static::$TABLE][$id];
		}

		return $return_array ? $result : reset($result);

	}

	public static function GetAllIDs($conditions = array()) {

		$query = "SELECT `".static::$ID_COLUMN."` FROM `".static::$TABLE."`";

		$cond_sql = $cond_data = array();
		
		$condition_array = func_get_args();		
		foreach ($condition_array as $conditions) {
			
			$sql = array();
			foreach ($conditions as $key => $value) {
				if (is_array($value)) {
				    if (count($value)) {
    					$placeholders = implode(', ', array_fill(0, count($value), '?'));
    					$sql[] = "`{$key}` IN (".$placeholders.")";
    					$cond_data = array_merge($cond_data, $value);
				    } else {
				        $sql[] = '0';
				    }
				} elseif (!is_array($value)) {
					$sql[] = "`{$key}` = ?";
					$cond_data[] = $value;
				}
			}
			if (!empty($sql)) {
    			$cond_sql[] = implode(' AND ', $sql);
			}
		}
				
		if (!empty($cond_sql)) {
			$query .= " WHERE ".implode(' OR ', $cond_sql);
			if (self::$context) {
    		    $query       .= " AND `context` = ? ";
    		    $cond_data[]  = self::$context;
			}
		} elseif (self::$context) {
		    $query       .= " WHERE `context` = ? ";
		    $cond_data[]  = self::$context;
		}
		if (!empty(static::$ORDER)) {
			$query .= " ORDER BY ".static::$ORDER;
		}
		
		$statement = DBManager::get()->prepare($query);
		$statement->execute($cond_data);
		return $statement->fetchAll(PDO::FETCH_COLUMN);

	}

	public static function Load($ids = null) {

		if ($ids === null) {
			$ids = static::GetAllIDs();
		} elseif (!is_array($ids)) {
			$ids = array($ids);
		}
		
		$data = self::Retrieve($ids);

		$result = array();
		foreach ($data as $row) {
			$object = new static();
			$object->populate($row);

			$id = $object[static::$ID_COLUMN];
			$result[ $id ] = $object;
		}
		return $result;

	}
	
	public static function Filter($conditions = array()) {

		$ids = self::GetAllIDs($conditions);
		$data = self::Retrieve($ids);

		$result = array();
		foreach ($data as $row) {
			$object = new static();
			$object->populate($row);

			$id = $object[static::$ID_COLUMN];
			$result[ $id ] = $object;
		}
		return $result;

	}

/** **/

	private $data;

	public function __construct($id = null) {

		$this->clear();
		if ($id !== null) {
			$this->restore($id);
		}
	}

	public function clear() {
		$this->data = array_combine(
			array_keys(static::$COLUMNS),
			array_fill(0, count(static::$COLUMNS), null)
		);

		// Return self to allow chaining
		return $this;
	}

	public function restore($id = null) {
		
		if ($id === null) {
			$id = $this->data[static::$ID_COLUMN];
		}

		if (empty($id)) {
			throw new Exception('Cannot load due to empty id');
		}

		$data = static::Retrieve($id);

		if (empty($data)) {
			throw new Exception('Tried to load invalid data, no id "'.$id.'" given in table "'.static::$TABLE.'"');
		}

		return $this->populate($data);

	}

	public function populate($db_data) {
		if (empty($db_data)) {
			return $this;
		}

		foreach ((array)$db_data as $key => $value) {
			$meta = explode(',', static::$COLUMNS[$key]);
			if (in_array('boolean', $meta)) {
				$value = !empty($value);
			} elseif (in_array('date', $meta)) {
				$value = date('d.m.Y', strtotime($value));
			} elseif (in_array('timestamp', $meta)) {
				$value = date('d.m.Y H:i:s', strtotime($value));
			}
			$this[$key] = $value;
		}

		// Return self to allow chaining
		return $this;
	}

	public function store() {

		$data = array();
		foreach (static::$COLUMNS as $key => $type) {
			$value = $this->data[$key];
			$meta = explode(',', $type);
			if ($key === 'context' and self::$context) {
				$value = self::$context;
			} elseif (in_array('required', $meta) and empty($this->data[$key])) {
				throw new Exception('Empty required field "'.$key.'"');
			} elseif (in_array('boolean', $meta)) {
				$value = (int)(bool)$value;
			} elseif (in_array('date', $meta)) {
				$value = date('Y-m-d', strtotime($value));
			} elseif (in_array('timestamp', $meta)) {
				$value = date('Y-m-d H:i:s', strtotime($value));
			}
			if ($key === 'chdate') {
			    $value = time();
			} elseif ($key === 'chuserid') {
			    $value = $GLOBALS['user']->id;
			}
			$data[] = $value;
		}
		
		$columns = '`'.implode('`, `', array_keys(static::$COLUMNS)).'`';
		$values = implode(', ', array_fill(0, count(static::$COLUMNS), '?'));
		$updates = array();
		foreach (static::$COLUMNS as $key => $type) {
			if ($key !== static::$ID_COLUMN) {
				$updates[] = "`{$key}` = VALUES(`{$key}`)";
			}
		}
		$updates = implode(',', $updates);

		$query = "INSERT INTO ".static::$TABLE." ({$columns}) VALUES ({$values})";
		$query .= " ON DUPLICATE KEY UPDATE ".$updates;

		$statement = DBManager::get()->prepare($query);
		$statement->execute($data);
		$success = $statement->rowCount() > 0;

		if ($success and empty($this->data[static::$ID_COLUMN])) {
			$this->data[static::$ID_COLUMN] = DBManager::get()->lastInsertId();
		}

		return $success;
	}

	public function delete(&$id = null) {
		if ($id === null and isset($this)) {
			$id = $this->data[static::$ID_COLUMN];
		}

		if (empty($id)) {
			throw new Exception('Trying to delete empty id');
		}

		$query = "DELETE FROM `".static::$TABLE."` WHERE `".static::$ID_COLUMN."` = ?";

		$statement = DBManager::get()->prepare($query);
		$statement->execute(array($id));

		$success = $statement->rowCount() > 0;

		if ($success and isset($this)) {
			$this->clear();
		}

		return $success;
	}
	
	public function __toString() {
		return static::$TABLE.'#'.$this['id'];
	}

    public function __isset($offset) {
        return isset($this[$offset]);
    }
    
    public function __get($offset) {
        return $this[$offset];
    }
    
    public function __set($offset, $value) {
        $this[$offset] = $value;
    }
    
    public function __unset($offset) {
        unset($this[$offset]);
    }

# ArrayAccess functions

	public function offsetExists($offset) {

		if ($offset === 'id') {
			$offset = static::$ID_COLUMN;
		}
		return array_key_exists($offset, static::$COLUMNS)
			or array_key_exists($offset, static::$CONNECTIONS);

	}

	public function offsetGet($offset) {

		if ($offset === 'id') {
			$offset = static::$ID_COLUMN;
		}

		if (array_key_exists($offset, static::$CONNECTIONS)) {
			list($class, $column) = explode('(', rtrim(static::$CONNECTIONS[$offset], ')'));
			return new $class($this->data[$column]);
		}

		return $this->data[$offset];

	}

	public function offsetSet($offset, $value) {

		if ($offset === 'id') {
			$offset = static::$ID_COLUMN;
		}
		if (!array_key_exists($offset, static::$COLUMNS)) {
			throw new Exception('Trying to set undefined property "'.$offset.'" on table "'.static::$TABLE.'"');
		}

		$this->data[$offset] = $value;

	}

	public function offsetUnset($offset) {

		if ($offset === 'id') {
			$offset = static::$ID_COLUMN;
		}
		$this->data[$offset] = null;

	}

}
