<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
define('DB_ASSOC', '');
class db_pdo {
	var $connid;
	var $pdo;
	var $querynum = 0;
	var $ttl;
	var $cursor = 0;
	var $halt = 0;
	var $linked = 1;
	var $result = array();

	function connect($dbhost, $dbuser, $dbpass, $dbname, $dbttl, $dbcharset, $pconnect = 0) {
		$this->ttl = $dbttl;
		if(strpos($dbhost, ':') === false) {
			$dbport = 3306;
		} else {
			list($dbhost, $dbport) = explode(':', $dbhost);
		}
		try {
			$this->pdo = new Pdo("mysql:dbname=$dbname;host=$dbhost;charset=$dbcharset", $dbuser, $dbpass);
		} catch (Exception $e) {
			$this->halt($e);
		}
	}

	function query($sql, $type = '', $ttl = 0) {
		$act = strtoupper(cutstr($sql, '', ' '));
		if($this->ttl > 0 && $type == 'CACHE' && $act == 'SELECT') {
			$this->cursor = 0;
			$this->result = array();
			return $this->_query($sql, $ttl ? $ttl : $this->ttl);
		}
		$this->querynum++;
		if(in_array($act, array('SELECT', 'SHOW'))) {
			return $this->pdo->query($sql);
		} else {
			return $this->connid = $this->pdo->exec($sql);
		}
	}

	function get_one($sql, $type = '', $ttl = 0) {
		$sql = str_replace(array('select ', ' limit '), array('SELECT ', ' LIMIT '), $sql);
		if(strpos($sql, 'SELECT ') !== false && strpos($sql, ' LIMIT ') === false) $sql .= ' LIMIT 0,1';
		$query = $this->query($sql, $type, $ttl);
		$r = $this->fetch_array($query);
		return $r ? $r : array();
	}
	
	function count($table, $condition = '', $ttl = 0, $fields = '*') {
		$sql = 'SELECT COUNT('.$fields.') AS amount FROM '.$table;
		if($condition) $sql .= ' WHERE '.$condition;
		$r = $this->get_one($sql, $ttl ? 'CACHE' : '', $ttl);
		return $r ? $r['amount'] : 0;
	}

	function fetch_array($query, $result_type = '') {
		return is_array($query) ? $this->_fetch_array($query) : $query->fetch(PDO::FETCH_ASSOC);
	}

	function affected_rows() {
		return $this->connid;
	}

	function num_rows($query) {
		return $query->rowCount();
	}

	function num_fields($query) {
		return $query->columnCount();
	}

	function result($query, $row) {
		return $query->fetch($row);
	}

	function free_result($query) {
		#return $this->pdo->closeCursor();
	}

	function insert_id() {
		return $this->pdo->lastInsertId();
	}

	function fetch_row($query) {
		return $query->fetch();
	}

	function version() {
		return $this->pdo->query("select version()")->fetchColumn();
	}

	function close() {
		return $this->pdo = null;
	}

	function halt($e = '', $sql = '')	{
		if($message && DT_DEBUG) log_write("\t\t<query>".$sql."</query>\n\t\t<errmsg>".$e->getMessage()."</errmsg>\n", 'sql');
		if($this->halt) message('MySQL Query:'.str_replace(DT_PRE, '[pre]', $sql).' <br/>Message:'.str_replace(DT_PRE, '[pre]', $e->getMessage()));
	}

	function _query($sql, $ttl) {
		global $dc;
		$cid = md5($sql);
		$this->result = $dc->get($cid);
		if(!is_array($this->result)) {
			$tmp = array(); 
			$result = $this->query($sql, '', '');
			while($r = $this->fetch_array($result, '')) {
				$tmp[] = $r; 
			}
			$this->result = $tmp;
			$this->free_result($result);
			$dc->set($cid, $tmp, $ttl + rand(0, 60));
		}
		return $this->result;
	}

	function _fetch_array($query = array()) {
		if($query) $this->result = $query; 
		if(isset($this->result[$this->cursor])) {
			return $this->result[$this->cursor++];
		} else {
			$this->cursor = 0;
			return array();
		}
	}
}
?>