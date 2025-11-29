<?php
defined('IN_DESTOON') or exit('Access Denied');
/*
如果PHP或者DESTOON内置函数无法直接处理数据，可以在此自定义函数处理
在采集规则设置字段的处理函数处填写，例如myfunc(*, '123') 代表将字段数据作为第一个参数传入myfunc函数处理
*/
function myfunc($par1, $par2) {
	return $par1.$par2;
}
?>