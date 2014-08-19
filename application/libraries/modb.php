<?php
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Modb {
	private $_moCo;
	public $modb;

	public function __construct() {
		include(APPPATH.'config/database'.EXT);
		if(!isset($modb) || count($modb) == 0) {
			show_error('No database connection settings were found in the database config file.');
		}
		$this->_moCo = $modb;
		
		$dbConfStr = 'mongodb://';
		$s = '';
		foreach($this->_moCo as $val) {
			$dbConfStr .= $s.$val['host'].':'.$val['port'];
			$s = ',';
		}
		$this->modb = $this->connect($dbConfStr);
	}

	public function connect($dbConfStr) {
		try {
			$conn = new Mongo($dbConfStr, array('connect'=>false,'persist'=>'foo'));
			if($conn !== false) {
				return $conn;
			} else {
				return false;
			}
		} catch (Exception $e) {
			return false;
		}
	}

	public function update($array) {
		if(! is_array($array) || empty($array)) $this->halt('MongoDB Array Error', $array);
		if(! isset($array['database']) || empty($array['database']) || ! isset($array['table']) || empty($array['table'])) $this->halt('MongoDB Array Table Error', $array);
		$collection = $this->modb->$array['database']->$array['table'];
		$setarr = array('$set' => $array['set']);
		if(isset($array['unset'])) {
			$setarr['$unset'] = $array['unset'];
		}
		if(isset($array['_oid']) && ! empty($array['_oid'])) {
			$objectId = new MongoId($array['_oid'][0]);
			if(isset($array['set']) && ! empty($array['set'])) {
				$result = $collection->update(array('_id' => $objectId), $setarr);
			} else {
				
			}
		} else if(isset($array['_id']) && ! empty($array['_id'])) {
			if(isset($array['set']) && ! empty($array['set'])) {
				$result = $collection->update(array('_id' => $array['_id'][0]), $setarr);
			} else {
				
			}
		} else if(isset($array['where']) && ! empty($array['where'])) {
			if(isset($array['set']) && ! empty($array['set'])) {
				$result = $collection->update($array['where'], $setarr, array('multiple' => true));
			} else {
				
			}
		} else {
			$this->halt('MongoDB Array Where Error', $array);
		}

		if($result !== false) {
			return $result;
		} else {
			$this->halt('MongoDB Result Error', $array);
		}
	}

	public function delete($array) {
		if(! is_array($array) || empty($array)) $this->halt('MongoDB Array Error', $array);
		if(! isset($array['database']) || empty($array['database']) || ! isset($array['table']) || empty($array['table'])) $this->halt('MongoDB Array Table Error', $array);
		$collection = $this->modb->$array['database']->$array['table'];
		if(isset($array['_oid']) && ! empty($array['_oid'])) {
			$objectId = new MongoId($array['_oid'][0]);
			$result = $collection->remove(array('_id' => $objectId), true);
		} else if(isset($array['_id']) && ! empty($array['_id'])) {
			$result = $collection->remove(array('_id' => $array['_id'][0]), true);
		} else if(isset($array['where']) && ! empty($array['where'])) {
			if(isset($array['just']) && ! empty($array['just'])) {
				$result = $collection->remove($array['where'], array('justOne' => $array['just']));
			} else {
				$result = $collection->remove($array['where']);
			}
		} else {
			$this->halt('MongoDB Array Where Error', $array);
		}

		if($result !== false) {
			return $result;
		} else {
			$this->halt('MongoDB Result Error', $array);
		}
	}

	public function insert($array) {
		if(! is_array($array) || empty($array)) $this->halt('MongoDB Array Error', $array);
		if(! isset($array['database']) || empty($array['database']) || ! isset($array['table']) || empty($array['table'])) $this->halt('MongoDB Array Table Error', $array);
		$collection = $this->modb->$array['database']->$array['table'];
		if(isset($array['set']) && ! empty($array['set'])) {
			$result = $collection->insert($array['set']);
		} else {

		}

		if($result !== false) {
			return $result;
		} else {
			$this->halt('MongoDB Result Error', $array);
		}
	}

	public function getAll($array) {
		if(! is_array($array) || empty($array)) $this->halt('MongoDB Array Error', $array);
		if(! isset($array['database']) || empty($array['database']) || ! isset($array['table']) || empty($array['table'])) $this->halt('MongoDB Array Table Error', $array);
		if(! isset($array['where'])) $this->halt('MongoDB Array Where Error', $array);
		$collection = $this->modb->$array['database']->$array['table'];
		if(isset($array['rule']) && ! empty($array['rule'])) {
			$result = $collection->find($array['rule']);
		} else {
			$result = $collection->find($array['where']);
			if(isset($array['skip']) && isset($array['limit']) && ! empty($array['limit'])) {
				if(isset($array['sort']) && ! empty($array['sort'])) {
					$result = $result->sort($array['sort'])->skip($array['skip'])->limit($array['limit']);
				} else {
					$result = $result->skip($array['skip'])->limit($array['limit']);
				}
			} else {
				if(isset($array['sort']) && ! empty($array['sort'])) {
					$result = $result->sort($array['sort']);
				}
			}
		}

		if($result !== false) {
			return $result;
		} else {
			$this->halt('MongoDB Result Error', $array);
		}
	}
	
	public function mid($table, $dbname = 'selfincrease') {
		$update = array('$inc' => array('inc' => 1));
		$query = array('_id' => $table);
		$command = array(
			'findandmodify' => $dbname,
			'update' => $update,
			'query' => $query,
			'new' => true,
			'upsert' => true
		);
		$result = $this->modb->gingko->command($command);
		return $result['value']['inc'];
	}

	public function getRegex($regex) {
		$regexObj = new MongoRegex($regex);
		return $regexObj;
	}

	public function close() {
		return $this->modb->close();
	}

	/**
	* 取得一个某个字段的值
	*/
	public function getOne($array) {
		if(! is_array($array) || empty($array)) $this->halt('MongoDB Array Error', $array);
		if(! isset($array['database']) || empty($array['database']) || ! isset($array['table']) || empty($array['table'])) $this->halt('MongoDB Array Table Error', $array);
		if(! isset($array['where']) || empty($array['where'])) $this->halt('MongoDB Array Where Error', $array);
		$collection = $this->modb->$array['database']->$array['table'];
		if(! isset($array['field']) || empty($array['field'])) {
			$result = $collection->findOne($array['where']);
		} else {
			$result = $collection->findOne($array['where'], $array['field']);
		}

		if($result !== false) {
			return $result;
		} else {
			$this->halt('MongoDB Result Error', $array);
		}
	}

	/**
	* 执行sql语句，只得到一条记录
	*/
	public function getRow($array) {
		if(! is_array($array) || empty($array)) $this->halt('MongoDB Array Error', $array);
		if(! isset($array['database']) || empty($array['database']) || ! isset($array['table']) || empty($array['table'])) $this->halt('MongoDB Array Table Error', $array);
		$collection = $this->modb->$array['database']->$array['table'];
		if(isset($array['_oid']) && ! empty($array['_oid'])) {
			$objectId = new MongoId($array['_oid'][0]);
			$result = $collection->findOne(array('_id' => $objectId));
		} else if(isset($array['_id']) && ! empty($array['_id'])) {
			$result = $collection->findOne(array('_id' => $array['_id'][0]));
		} else {
			if(! isset($array['where']) || empty($array['where'])) $this->halt('MongoDB Array Where Error', $array);
			$result = $collection->findOne($array['where']);
		}

		if($result !== false) {
			return $result;
		} else {
			$this->halt('MongoDB Result Error', $array);
		}
	}

	public function count($array) {
		if(! is_array($array) || empty($array)) $this->halt('MongoDB Array Error', $array);
		if(! isset($array['database']) || empty($array['database']) || ! isset($array['table']) || empty($array['table'])) $this->halt('MongoDB Array Table Error', $array);
		$collection = $this->modb->$array['database']->$array['table'];
		if(isset($array['where']) && ! empty($array['where'])) {
			$result = $collection->count($array['where']);
		} else {
			$result = $collection->count();
		}

		if($result !== false) {
			return $result;
		} else {
			$this->halt('MongoDB Result Error', $array);
		}
	}

	public function halt($message = '', $info = '') {
		$dberror = $this->modb->resetError();
		$dberrno = $this->modb->lastError();
		if(is_array($info)) {
			$info = var_export($info, true);
		}
		echo "<div style=\"position:absolute;font-size:11px;font-family:verdana,arial;background:#EBEBEB;padding:0.5em;\">
				<b>MySQL Error</b><br>
				<b>Message</b>: $message<br>
				<b>Info</b>: $info<br>
				<b>Error</b>: $dberror<br>
				<b>Errno.</b>: $dberrno
				</div>";
		exit();
	}
}