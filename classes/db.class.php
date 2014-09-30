<?php

/**
 * Common Module Interface
 * 
 * 数据库操作类
 *
 * @copyright (c) 2009-2014 Yaosine.com
 * @author yaosine@gmail.com
 *
 */

class cmi_db extends cmi{

	var $connection;
	var $query_id = '';
	var $db_host = '';
	var $db_port = 3306;
	var $db_user = '';
	var $db_password = '';
	var $db_database = '';
	var $sql_debug = 0;
	var $mysqli = 0;

	function cmi_db($unit = null){
		if(strnatcasecmp(PHP_VERSION, '5.2.9') >= 0) $this->mysqli = 1;
	}

	/**
	 * 加载指定单元数据库对象
	 * @param string $unit 数据库单元
	 * @return DB 引用
	 */
	function load($unit){
		$database_unit = $this->load_config('db');
		$conf = $database_unit[$unit];
		if($conf){
			if(!isset($this->$unit)){
				$this->db_port = $conf['port'];
				$this->db_host = $conf['host'];
				$this->db_user = $conf['user'];
				$this->db_password = $conf['pwd'];
				$this->db_database = $conf['db'];
				$this->$unit = $this;
			}
			return $this->$unit;
		}else{
			return false;
		}
	}

	function connect(){

		//mysqli
		if($this->mysqli == 1){
			if (!$this->connection || !@mysqli_ping($this->connection)) {
				@mysqli_close($this->connection);
				$this->connection = mysqli_connect($this->db_host.':'.$this->db_port, $this->db_user, $this->db_password, $this->db_database);
				if(mysqli_connect_errno($this->connection)) {
					$this->error('Cannot connect to database host (' . $this->db_host . ') by user (' . $this->db_user . '). at database:'.$this->db_database);
				}
			}
			return;
		}

		if (!$this->connection || !@mysql_ping($this->connection)) {
			@mysql_close($this->connection);
			$this->connection = mysql_connect($this->db_host.':'.$this->db_port, $this->db_user, $this->db_password,true);
			if(!is_resource($this->connection)) {
				$this->error('Cannot connect to database host (' . $this->db_host . ') by user (' . $this->db_user . ').');
			} else {
				if (!@mysql_select_db($this->db_database, $this->connection)) {
					$this->error('Cannot select database (' . $this->db_database . ').');
				}
				mysql_query("SET NAMES 'UTF8'");			
			}
		}
	}

	/*-------------------------------------------------------------------------*/
	// resource query(string sqlstr)
	// ------------------
	// Execute an SQL query.
	/*-------------------------------------------------------------------------*/
	function query($sqlstr,$is_echo=true){
		
		$this->connect();

		//mysqli
		if($this->mysqli == 1) $this->query_id = @mysqli_query($this->connection, $sqlstr);
		else $this->query_id = @mysql_query($sqlstr, $this->connection);
		
		if($this->sql_debug == 1) echo "$sqlstr";
		if($this->query_id) return $this->query_id;
		else {
			if($is_echo === true) {
				$exit = true;
			} else {
				$exit = false;
			}
			$this->error(sprintf("Invalid SQL@%s %s [%s] %.256s:%s\n" ,$_SERVER["SERVER_ADDR"], date('Y-m-d G:i:s'),  $_SERVER['PHP_SELF'], $sqlstr, is_resource($this->connection) ? mysql_error($this->connection) : mysql_error()), $exit);
		}
		return false;
	}


	/*-------------------------------------------------------------------------*/
	// int get_inc_id(void)
	// ------------------
	// Returns the newly inserted auto_increment id.
	/*-------------------------------------------------------------------------*/
	function get_inc_id(){
		$this->connect();
		return $this->mysqli == 1 ? mysqli_insert_id($this->connection) : mysql_insert_id($this->connection);
	}


	/*-------------------------------------------------------------------------*/
	// mixed result(string sqlstr)
	// ------------------
	// Select a single line or a single cell.
	// eg: $SDB->result("SELECT name FROM user WHERE uid=5")
	//	   (returns a string)
	//	 $SDB->result("SELECT name,email FROM user WHERE login_hash='$hash'")
	//	   (returns an object)
	// Returns NULL if no such entry could be found.
	/*-------------------------------------------------------------------------*/
	function result($sqlstr){
		$this->connect();
		$result = $this->query($sqlstr . " LIMIT 1", $this->connection);

		//mysqli
		if($this->mysqli == 1){
			if (mysqli_num_rows($result) == 0) return NULL;
			if (mysqli_num_fields($result) <= 1){
				$arr = mysqli_fetch_row($result);
				mysqli_free_result($result);
				return $arr[0];
			}
			$arr = mysqli_fetch_array($result);
			mysqli_free_result($result);
			return $arr;
		}
		
		if (!is_resource($result) || mysql_num_rows($result) == 0) return NULL;
		if (mysql_num_fields($result) <= 1){
			$arr = mysql_fetch_row($result);
			mysql_free_result($result);
			return $arr[0];
		}
		$arr = mysql_fetch_array($result);
		mysql_free_result($result);
		return $arr;
	}

	/*-------------------------------------------------------------------------*/
	// array fetch_array(resource id)
	// ------------------
	// Fetch a row as an array from result by both assoc and num.
	/*-------------------------------------------------------------------------*/
	function fetch_array($id = NULL){
		$this->connect();
		$queryId = $id ? $id : $this->query_id;

		//mysqli
		if($this->mysqli == 1) {
			if($queryId) {
				if($id) return mysqli_fetch_array($queryId);
				$data = array();
				while($r = mysqli_fetch_array($queryId)) {
					$data[] = $r;		
				}
				return $data;
			}
			return NULL;
		}

		if(is_resource($queryId)) {
			if($id) return mysql_fetch_array($queryId);
			$data = array();
			while($r = mysql_fetch_array($queryId)) {
				$data[] = $r;		
			}
			return $data;
		}
		return NULL;
	}

	/*-------------------------------------------------------------------------*/
	// array fetch_assoc(resource id)
	// ------------------
	// Fetch a row as an array from result by assoc.
	/*-------------------------------------------------------------------------*/
	function fetch_assoc($id = NULL){
		$this->connect();
		$queryId = $id ? $id : $this->query_id;

		//mysqli
		if($this->mysqli == 1) {
			if($queryId) {
				if($id) return mysqli_fetch_assoc($queryId);
				$data = array();
				while($r = mysqli_fetch_assoc($queryId)) {
					$data[] = $r;		
				}
				return $data;
			}
			return NULL;
		}

		if(is_resource($queryId)){
			return 	mysql_fetch_assoc($queryId);
		}
		return NULL;
	}

	/*-------------------------------------------------------------------------*/
	// array fetch_row(resource id)
	// ------------------
	// Fetch a row as an array from result.
	/*-------------------------------------------------------------------------*/
	function fetch_row($id = NULL){
		$this->connect();
		$queryId = $id ? $id : $this->query_id;

		//mysqli
		if($this->mysqli == 1) {
			if($queryId) return mysqli_fetch_row($queryId);
			return NULL;
		}

		if(is_resource($queryId)) {
			return 	mysql_fetch_row($queryId);
		} 
		return NULL;	
	}

	/*-------------------------------------------------------------------------*/
	// object fetch_object(resource id)
	// ------------------
	// Fetch a row as an object from result.
	/*-------------------------------------------------------------------------*/
	function fetch_object($id = NULL){
		$this->connect();
		$queryId = $id ? $id : $this->query_id;

		//mysqli
		if($this->mysqli == 1) {
			if($queryId) return mysqli_fetch_object($queryId);
			return NULL;
		}

		if(is_resource($queryId)) {
			return 	mysql_fetch_object($queryId);
		}
		return NULL;			
	}

	/*-------------------------------------------------------------------------*/
	// int num_rows(resource id)
	// ------------------
	// Returns number of collected rows.
	/*-------------------------------------------------------------------------*/
	function num_rows($id = NULL){
		$this->connect();
		$queryId = $id ? $id : $this->query_id;

		//mysqli
		if($this->mysqli == 1) {
			if($queryId) return mysqli_num_rows($queryId);
			return 0;
		}

		if(is_resource($queryId)) {
			return 	mysql_num_rows($queryId);
		} 
		return 0;							
	}

	/*-------------------------------------------------------------------------*/
	// int affected_rows(resource id)
	// ------------------
	// Returns number of affected rows.
	/*-------------------------------------------------------------------------*/
	function affected_rows(){
		return $this->mysqli == 1 ? mysqli_affected_rows($this->connection) : mysql_affected_rows($this->connection);
	}

	/*-------------------------------------------------------------------------*/
	// close_database()
	// ------------------
	// Close Database.
	/*-------------------------------------------------------------------------*/
	function close_database(){
		if($this->connection){
			$return = $this->mysqli == 1 ? @mysqli_close($this->connection) : @mysql_close($this->connection);
			$this->connection = false;
			return $return;
		}
	}


	/*-------------------------------------------------------------------------*/
	// void error(string msg)
	// ------------------
	// Record an error message and crash.
	/*-------------------------------------------------------------------------*/
	function error($msg, $exit=true){
		$errno = $this->mysqli == 1 ? mysqli_errno($this->connection) : mysql_errno();
		$url = $_SERVER['HTTP_HOST']."/".$_SERVER['REQUEST_URI'];
		$url = base64_encode($url);
		//$msg = base64_encode($msg);
		print "很抱歉，有错误产生!<br>$errno <br> $url <br> $msg <br>";
		if($exit) {
			exit;
		}
	}

	/**
	 * execute add query on target table
	 * @param string $table	- target table name
	 * @param array $values - key-value array
	 */
	function insert($table, $values) {
		if (!count($values)){
			return false;
		}

		$query = 'INSERT INTO `'.$table.'` (';
		foreach ($values AS $key => $value){
			$query .= '`'.$key.'`,';
		}
		$query = rtrim($query, ',').') VALUES (';

		foreach ($values AS $key => $value){
			$query .= '\''.$value.'\',';
		}

		$query = rtrim($query, ',').')';

		return $this->query($query);
	}

	/**
	 * execute replace into clause on target table
	 * @param string $table	- target table name
	 * @param array $values - key-value array
	 */
	function replace($table, $values) {
		if (!count($values)){
			return false;
		}

		$query = 'REPLACE INTO `'.$table.'` (';
		foreach ($values AS $key => $value){
			$query .= '`'.$key.'`,';
		}
		$query = rtrim($query, ',').') VALUES (';
		foreach ($values AS $key => $value){
			$query .= '\''.$value.'\',';
		}
		$query = rtrim($query, ',').')';
		return $this->query($query);		
	}

	/**
	 * execute update query on target table
	 * @param string $table	- target table
	 * @param array $values - key-value array
	 * @param string $where - condition clause
	 */
	function update($table, $values, $conditionArray = false) {
		if(!count($values)){
			return false;
		}

		$query = 'UPDATE `'.$table.'` SET ';
		foreach ($values AS $key => $value){
			$query .= '`'.$key.'` = \''.$value.'\',';
		}
		$query = rtrim($query, ',');
		if ($conditionArray){
			$query .= ' where ';
			foreach ($conditionArray AS $key => $value){
				$query .= '`'.$key.'` = \''.$value.'\',';
			}
			$query = rtrim($query, ',');
		}
		return $this->query($query);
	}

	/**
	 * delete entries from target table
	 * @param $table
	 * @param $conditionArray - key-value array
	 */
	function delete($table, $conditionArray, $limit = false) {

		$query = 'DELETE FROM `'.$table.'` where ';
		if(is_array($conditionArray)){
			foreach ($conditionArray AS $key => $value){
				$query .= '`'.$key.'` = \''.$value.'\',';
			}
			$query = rtrim($query, ',');
		}
		elseif(is_string($conditionArray)) $query .= $conditionArray;
		else return false;

		if($limit) {
			$query .= ' limit '.intval($limit);
		}

		return $this->query($query);
	}

	/**
	 * Fetch one piece of record from database
	 * @param $table
	 * @param $conditionArray
	 * @return object or false
	 */
	function get_one($table, $conditionArray, $cols = false) {
		if(!count($conditionArray)){
			return false;
		}

		if($cols == false){
			$query = 'SELECT * FROM `'.$table.'` where ';
		} 
		else {
			$colStr = '';
			if(!is_string($cols)){
				foreach($cols as $column) {
					if($column && $column != '')
						$colStr .= '`'.$column.'`,';
				}
				$colStr = rtrim($colStr, ',');
			}
			else $colStr = $cols;
			$query = 'SELECT '.$colStr.' FROM `'.$table.'` where ';
		}

		$cnt = 0;
		foreach ($conditionArray AS $key => $value){
			if($cnt != 0){
				$query .= ' AND ';
			}
			$query .= '`'.$key.'` = \''.$value.'\'';
			$cnt++;
		}
		return $this->result($query);
	}

}

?>
