<?php
defined('IN_DESTOON') or exit('Access Denied');
if($DT['stats']) include DT_ROOT.'/api/stats.inc.php';
if($DT_BOT) {
	//
} else {
	include template('line', 'chip');
}
?>