<?php
/*
	[DESTOON B2B System] Copyright (c) 2008-2020 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
class dcache {
	var $pre;

    function __construct() {
    }

    function dcache() {
		$this->__construct();
    }

	function get($key) {
		is_md5($key) or $key = md5($this->pre.$key);
		$php = DT_CACHE.'/php/'.substr($key, 0, 2).'/'.$key.'.php';
		if(is_file($php)) {
			$str = file_get($php);
			$ttl = substr($str, 13, 10);
			if($ttl < DT_TIME) return '';
			return substr($str, 23, 1) == '@' ? substr($str, 24) : unserialize(substr($str, 23));
		} else {
			return '';
		}
	}

	function set($key, $val, $ttl = 600) {
		is_md5($key) or $key = md5($this->pre.$key);
		$ttl = DT_TIME + $ttl;
		$sql = "REPLACE INTO ".DT_PRE."cache (`cacheid`,`totime`) VALUES ('$key','$ttl')";
		DB::query($sql);
		$val = '<?php exit;?>'.$ttl.(is_array($val) ? serialize($val) : '@'.$val);
		return file_put(DT_CACHE.'/php/'.substr($key, 0, 2).'/'.$key.'.php', $val);
	}

	function rm($key) {
		is_md5($key) or $key = md5($this->pre.$key);
		return file_del(DT_CACHE.'/php/'.substr($key, 0, 2).'/'.$key.'.php');
	}

	function remove($sql) {
		$this->rm(md5($sql));
	}

    function clear() {
		DB::query("DELETE FROM ".DT_PRE."cache");
        @rename(DT_CACHE.'/php/', DT_CACHE.'/'.timetodate(DT_TIME, 'YmdHis').'.tmp/');
    }

	function expire() {
		$result = DB::query("SELECT cacheid FROM ".DT_PRE."cache WHERE totime<".DT_TIME." ORDER BY totime ASC LIMIT 100");
		while($r = DB::fetch_array($result)) {
			$cid = $r['cacheid'];
			$this->rm($cid);
			DB::query("DELETE FROM ".DT_PRE."cache WHERE cacheid='$cid'");
		}
	}
}
?>