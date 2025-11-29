<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
class dcache {
	var $pre; 

    function __construct() {
		//
    }

    function dcache() {
		$this->__construct();
    }

	function get($key) {
		return xcache_get($this->pre.$key);
	}

	function set($key, $val, $ttl = 600) {
		return xcache_set($this->pre.$key, $val, $ttl);
	}

	function rm($key) {
		return xcache_unset($this->pre.$key);
	}

	function remove($sql) {
		$this->rm(md5($sql));
	}

    function clear() {
        return true;
    }

	function expire() {
		return true;
	}
}
?>