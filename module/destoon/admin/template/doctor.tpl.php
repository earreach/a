<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
$warn = 0;
?>
<style type="text/css">
.dm {color:green;text-align:center;}
.dm span {color:red;}
.dr {padding:10px 16px !important;line-height:2.0;color:#666666;}
</style>
<table cellspacing="0" class="tb ls">
<tr>
<th>项目</th>
<th width="120">值</th>
<th>说明</th>
</tr>
<?php
	if(strpos(get_env('self'), '/admin'.DT_EXT) !== false) {
	$warn++;
?>
<tr>
<td class="tl">后台登录地址</td>
<td class="dm"><span>admin.php</span></td>
<td class="dr">
如果管理帐号泄漏，后台容易遭受攻击，为了系统安全，请修改根目录admin.php的文件名
</td>
</tr>
<?php } ?>
</tr>
<?php
	if(is_dir(DT_ROOT.'/8.0')) {
	$warn++;
?>
<tr>
<td class="tl">升级备份目录</td>
<td class="dm"><span>8.0</span></td>
<td class="dr">
如果已经升级成功，建议将备份目录删除或备份至非站点目录
</td>
</tr>
<?php } ?>
<?php
	if(is_dir(DT_ROOT.'/upgrade')) {
	$warn++;
?>
<tr>
<td class="tl">升级目录</td>
<td class="dm"><span>upgrade</span></td>
<td class="dr">
如果已经升级成功，建议将升级目录删除
</td>
</tr>
<?php } ?>
<?php
	if(is_dir(DT_ROOT.'/install')) {
	$warn++;
?>
<tr>
<td class="tl">安装目录</td>
<td class="dm"><span>install</span></td>
<td class="dr">
如果已经安装成功，建议将安装目录删除
</td>
</tr>
<?php } ?>

<?php
	$D = substr(DT_PATH, 0, 5) == 'https' ? 1 : 0;
	if(!$D) $warn++;
?>
<tr>
<td class="tl">SSL证书</td>
<td class="dm"><?php echo $D ? '启用' : '<span>未启用</span>';?></td>
<td class="dr">
HTTPS绿色加密通道，网站数据的加密传输 <a href="<?php echo gourl('https://www.destoon.com/ssl/?tracert=Doctor');?>" target="_blank" class="t"><?php echo $D ? '了解详情' : '购买证书';?></a>
</td>
</tr>

<?php
	$total = round(disk_total_space(DT_ROOT)/1024/1024/1024, 2);
	$free = round(disk_free_space(DT_ROOT)/1024/1024/1024, 2);
	if($total > 0 && $free > 0 && $total > $free) {
	$used = $total - $free;
	$D = $free > 1 ? 1 : 0;
	if(!$D) $warn++;
?>
<tr>
<td class="tl">磁盘空间</td>
<td class="dm"><?php echo $D ? '充足' : '<span>不足</span>';?></td>
<td class="dr">
总计<?php echo $total;?>G，已使用<?php echo $used;?>G，剩余<?php echo $free;?>G<br/></td>
</tr>
<?php } ?>

<?php
	$D = is_write(DT_ROOT.'/file/') && is_write(DT_ROOT.'/file/cache/') && is_write(DT_ROOT.'/file/cache/tpl/') && is_write(DT_ROOT.'/file/upload/');
	if(!$D) $warn++;
?>
<tr>
<td class="tl">file目录是否可写</td>
<td class="dm"><?php echo $D ? '可写' : '<span>不可写</span>';?></td>
<td class="dr">
file目录及所有子目录和子文件都必须设置可写，否则会出现以下问题：<br/>
系统缓存无法更新<br/>
后台无法登录<br/>
登录后台不显示密码输入框<br/>
前台页面无法正常显示<br/>
文件无法上传<br/>
</td>
</tr>
<?php
	$S = 0;
	foreach($MODULE as $v) {
		if($v['moduleid'] > 1 && $v['domain']) $S = 1;
	}
	if($CFG['com_domain']) $S = 1;
	if(!$S) {
		foreach($EXT as $k=>$v) {
			if(strpos($k, '_domain') !== false && strpos($v, '://') !== false) {
				$S = 1;
				break;
			}
		}
	}
	if(!$S && $DT['city']) {
		$r = $db->get_one("SELECT areaid FROM {$DT_PRE}city WHERE domain<>''");
		if($r) $S = 1;
	}
	$D = $CFG['cookie_domain'];
	if($S) {
	if(!$D) $warn++;
?>
<tr>
<td class="tl">Cookie作用域</td>
<td class="dm"><?php echo $D ? $D : '<span>未设置</span>';?></td>
<td class="dr">
当前系统使用过二级域名，未设置Cookie作用域会出现以下问题：<br/>
验证码/验证问题校验错误<br/>
会员登录状态显示错误<br/>
评论不显示<br/>
</td>
</tr>
<?php } ?>

<?php
	if($CFG['skin'] == $CFG['template'] && $CFG['template'] != 'default') {
	$warn++;
?>
<tr>
<td class="tl">模板和风格目录</td>
<td class="dm"><span>同名</span></td>
<td class="dr">
模板和风格目录同名可能导致模板被下载，建议模板和风格使用不相同的目录名称
</td>
</tr>
<?php } ?>

<?php
	$dc->set('destoon', 'com', 3600);
	$D = $dc->get('destoon') == 'com' ? 1 : 0;
	if(!$D) $warn++;
?>
<tr>
<td class="tl">系统缓存测试</td>
<td class="dm"><?php echo $D ? '成功' : '<span>失败</span>';?></td>
<td class="dr">
当前缓存类型为<?php echo $CFG['cache'];?>，<?php echo $D ? '缓存运行正常' : ($CFG['cache'] == 'file' ? '请检查file目录是否可写' : '请<a href="?file=setting&tab=2" target="_blank" class="t">立即更换</a>可用的缓存类型');?>
</td>
</tr>

<tr>
<td class="tl">客户端IP</td>
<td class="dm"><?php echo DT_IP;?></td>
<td class="dr">
查看<a href="<?php echo gourl('https://www.baidu.com/s?wd=ip');?>" target="_blank" class="t">真实IP</a>，如果IP错误可能会影响注册和发布信息<br/>
所属地区为<?php echo ip2area(DT_IP);?>，如果所在地错误，请及时<a href="<?php echo gourl('https://www.destoon.com/doc/skill/28.html');?>" target="_blank" class="t">更新IP数据库</a>
</td>
</tr>

<?php
	$D = ini_get('allow_url_fopen');
	if(!$D) $warn++;
?>
<tr>
<td class="tl">允许使用URL打开文件<br/>allow_url_fopen</td>
<td class="dm"><?php echo $D ? 'On' : '<span>Off</span>';?></td>
<td class="dr">
建议设置为On，否则会出现以下问题：<br/>
远程图片无法保存<br/>
网络图片无法上传<br/>
一键登录无法登录<br/>
</td>
</tr>

<?php
	$D = ini_get('memory_limit');
?>
<tr>
<td class="tl">程序最多允许使用内存量<br/>memory_limit</td>
<td class="dm"><?php echo $D;?></td>
<td class="dr">
内存设置过小会导致部分操作无法进行，显示空白
</td>
</tr>

<?php
	$D = ini_get('post_max_size');
?>
<tr>
<td class="tl">POST最大字节数<br/>post_max_size</td>
<td class="dm"><?php echo $D;?></td>
<td class="dr">
大于<?php echo $D;?>的文件无法上传<br/>
大于<?php echo $D;?>的信息无法提交
</td>
</tr>

<?php
	$D = ini_get('upload_max_filesize');
?>
<tr>
<td class="tl">允许最大上传文件<br/>upload_max_filesize</td>
<td class="dm"><?php echo $D;?></td>
<td class="dr">
大于<?php echo $D;?>的文件无法上传
</td>
</tr>

<?php
	$D = function_exists('curl_init');
	if(!$D) $warn++;
?>
<tr>
<td class="tl">curl</td>
<td class="dm"><?php echo $D ? '支持' : '<span>不支持</span>';?></td>
<td class="dr">
如果不支持，将会出现以下问题：<br/>
一键登录无法登录<br/>
短信无法发送<br/>
系统无法在线更新<br/>
系统无法运行采集<br/>
</td>
</tr>

<?php
	$D = function_exists('json_decode');
	if(!$D) $warn++;
?>
<tr>
<td class="tl">json</td>
<td class="dm"><?php echo $D ? '支持' : '<span>不支持</span>';?></td>
<td class="dr">
如果不支持，将会出现以下问题：<br/>
一键登录无法登录<br/>
</td>
</tr>
<?php
	$D = function_exists('openssl_sign');
	if(!$D) $warn++;
?>
<tr>
<td class="tl">OpenSSL</td>
<td class="dm"><?php echo $D ? '支持' : '<span>不支持</span>';?></td>
<td class="dr">
如果不支持，将会出现以下问题：<br/>
一键登录无法登录<br/>
支付接口无法使用<br/>
SSL邮箱无法发信<br/>
</td>
</tr>
</table>
</div>
<script type="text/javascript">Menuon(0);</script>
<?php 
cache_write('doctor.php', array('warn' => $warn));
include tpl('footer');
?>