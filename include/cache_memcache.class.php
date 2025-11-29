<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
class dcache {
	var $pre;
	var $obj;
	var $con;

    function __construct() {
		$this->obj = new Memcache;
		include DT_ROOT.'/file/config/memcache.inc.php';
		$num = count($MemServer);
		$key = $num == 1 ? 0 : abs(crc32(DT_IP))%$num;
		$this->con = $this->obj->connect($MemServer[$key]['host'], $MemServer[$key]['port'], 2);
    }

    function dcache() {
		$this->__construct();
    }

	function get($key) {
        return $this->obj->get($this->pre.$key);
    }

    function set($key, $val, $ttl = 600) {
        return $this->obj->set($this->pre.$key, $val, 0, $ttl);
    }

    function rm($key) {
        return $this->obj->delete($this->pre.$key);
    }

	function remove($sql) {
		$this->rm(md5($sql));
	}

    function clear() {
        return $this->obj->flush();
    }

	function expire() {
		return true;
	}
}
?>