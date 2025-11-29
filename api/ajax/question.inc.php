<?php
defined('IN_DESTOON') or exit('Access Denied');
if(strlen($answer) < 1) exit('1');
$answer = stripslashes($answer);
$session = new dsession();
if(!isset($_SESSION['answerstr'])) exit('2');
$ansstr = decrypt($_SESSION['answerstr'], DT_KEY.'ANS');
if(strpos($ansstr, '|') !== false) {
	$ansarr = explode('|', $ansstr);
	if(!in_array($answer, $ansarr)) exit('3');
} else {
	if($ansstr != $answer) exit('3');
}
exit('0');
?>