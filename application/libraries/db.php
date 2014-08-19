<?php
/**
 * @copyright	© 2010 plhwin.com
 * @author		Peter Pan <plhwin@plhwin.com>
 * @since		version - 2010-8-18下午08:08:08
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Db{
	private $CI;
	private $RO;
	private $RW;
	public  $queries;

	public function __construct() {
		$this->CI =& get_instance();
		
		$this->RO = null;
		$this->CI->rodb = null;
		
		$this->RW = null;
		$this->CI->rwdb = null;
		
		$this->queries = array();
	}
	
	public function query($sql){
		
		$isselectsql = preg_match('/^\s*"?(select)\s+/i', $sql);
		$ro = $query = false;
		
		if($isselectsql){
			$ro = true;
		} else {
			//读写都在从库的表，这些表必须在slave数据库的my.cnf配置中被设置为忽略同步。
			$slave_table_arr = array(
				tname('session')
			);
			$ro = is_sql_in_tables($sql, $slave_table_arr);
		}
		
		if($ro){ 	
			if(!$this->RO && DB_SWITCH){
				$this->RO = $this->CI->load->database('ro', true, true);
			}
			
			if($this->RO){
				$query = $this->RO->query($sql);
			} else {
				$query = false;
			}

			//如果slave没有查询到记录，需要到master进行再次查询的表 (与会员相关的两张表必须这样进行，确保状态正确)。
			$rw_tables_arr = array(
				tname('member'),
				tname('member_profile'),
				tname('landing')
			);
 			
 			$is_rw_tables = is_sql_in_tables($sql, $rw_tables_arr);
 			if($is_rw_tables){
 				$row = $query?$query->row_array():array();
 				if(empty($row)){
 					$ro = false;
 				} 
 				//2011.5.25 add by plhwin
 				else {
 					$row = array_values($row);
 					if(empty($row[0])){
 						$ro = false;
 					}
 				}
 				//add end
 			}
 			$this->CI->rodb =& $this->RO;
		} 

		if(!$ro) {
			if(!$this->RW && DB_SWITCH){
				$this->RW = $this->CI->load->database('rw', true, true);
			}
			if($this->RW){
				$query = $this->RW->query($sql);
			} else {
				$query = false;
			}
			
			$this->CI->rwdb =& $this->RW;		
		}
		
 		$this->queries[] = $sql;
 		return $query;
	}
	
	public function insert_id(){
		$id = 0;
		if($this->CI->rwdb){
			$id = $this->CI->rwdb->insert_id();
			$id = intval($id);
		}
		return $id;
	}
	
	public function close(){
		if($this->CI->rwdb){
			$this->CI->rwdb->close();
		}
		if($this->CI->rodb){
			$this->CI->rodb->close();
		}
	}
	
	
	public function where($key, $value = NULL, $escape = TRUE){
		if($this->CI->rodb){
			return $this->CI->rodb->where($key, $value, $escape);
		}
		return false;
		
	}
	
	public function insert_string($table, $data) {
		if($this->CI->rodb) {
			return $this->CI->rodb->insert_string($table, $data);
		}
		return false;
	}
	
	public function get($table = '', $limit = null, $offset = null){
		if($this->CI->rodb){
			return $this->CI->rodb->get($table, $limit, $offset);
		}
		return false;
	}
	
	public function update_string($table, $data, $where){
		if($this->CI->rodb){
			return $this->CI->rodb->update_string($table, $data, $where);
		}
		return false;
	}
	
	public function update($table = '', $set = NULL, $where = NULL, $limit = NULL){
		if(!$this->CI->rwdb && DB_SWITCH){
			$this->CI->rwdb = $this->CI->load->database('rw', true, true);
		}
		return $this->CI->rwdb->update($table, $set, $where, $limit);
	}
	
	public function delete($table = '', $where = '', $limit = NULL, $reset_data = TRUE){
		if(!$this->CI->rwdb && DB_SWITCH){
			$this->CI->rwdb = $this->CI->load->database('rw', true, true);
		}
		return $this->CI->rwdb->delete($table, $where, $limit, $reset_data);
	}
}