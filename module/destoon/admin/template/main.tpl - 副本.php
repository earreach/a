<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
$ign = cache_read('ignore.php');
?>
<style type="text/css">
#todo {display:none;width:100%;border-bottom:#E7E7EB 1px solid;padding-bottom:10px;}
#todo ul {margin-top:10px;}
#todo li {float:left;width:180px;line-height:32px;padding:0 16px;}
#todo li b {color:red;padding:0 2px;font-size:12px;}
.bub {color:#FFFFFF;font-style:normal;background:#FA5A57;border-radius:10px;color:#FFFFFF;font-size:12px;display:inline-block;height:14px;line-height:14px;padding:0 4px;margin:0 6px;}
</style>
<div id="tips_update<?php echo $ign ? '_ignore' : '';?>" style="display:none;">
<div class="tt">更新提示</div>
<table cellspacing="0" class="tb">
<tr>
<td>
<div style="padding:20px;" title="当前版本V<?php echo DT_VERSION; ?> 更新时间<?php echo DT_RELEASE;?>"><img src="<?php echo DT_STATIC;?>admin/tips-update.png" width="32" height="32" align="absmiddle"/>&nbsp;&nbsp; 您的当前软件版本有新的更新，请注意升级&nbsp;&nbsp;&nbsp;&nbsp;最新版本：V<span id="last_v"></span>&nbsp;&nbsp;更新时间：<span id="last_r"></span>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="?file=cloud&action=update" class="t">立即检查</a>&nbsp;&nbsp;&nbsp;&nbsp;
<a href="javascript:;" class="t" onclick="Ign();">本次忽略</a>
</div></td>
</tr>
</table>
</div>
<div class="tt"><span class="f_r" style="font-weight:normal;font-size:12px;">IP:<?php echo $user['loginip']; ?> <?php echo ip2area($user['loginip']);?></span>欢迎，<?php echo $_username;?></div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">管理角色</td>
<td>&nbsp;<?php echo $_role ? $_role : ($_admin == 1 ? ($_founder ? '网站创始人' : '超级管理员') : ($_aid ? '<span class="f_blue">'.$AREA[$_aid]['areaname'].'站</span>管理员' : '普通管理员')); ?></td>
<td class="tl">登录次数</td>
<td data-side="1">&nbsp;<?php echo $user['logintimes']; ?> 次</td>
</tr>
<tr>
<td class="tl">站内信件</td>
<td>&nbsp;<a href="<?php echo $MODULE[2]['linkurl'].'message.php';?>" target="_blank">收件箱<?php echo $_message ? '<i class="bub">'.$_message.'</i>' : '';?></a><?php echo $_chat ? ' &nbsp; &nbsp; <a href="'.$MODULE[2]['linkurl'].'im.php" target="_blank">新交谈<i class="bub">'.$_chat.'</i></a>' : '';?></td>
<td class="tl">登录时间</td>
<td>&nbsp;<?php echo timetodate($user['logintime'], 5); ?> </td>
</tr>
<tr>
<td class="tl">账户余额</td>
<td>&nbsp;<?php echo $DT['money_sign'].number_format($_money, 2); ?></td>
<td class="tl">会员<?php echo $DT['credit_name'];?></td>
<td>&nbsp;<?php echo $_credit; ?> </td>
</tr>
<form method="post" action="?">
<tr>
<td class="tl">工作便笺</td>
<td colspan="2">
<input type="hidden" name="action" value="note"/>
<textarea name="note" style="width:98%;height:48px;overflow:visible;color:#444444;"><?php echo $note;?></textarea></td>
<td>&nbsp;<input type="submit" name="submit" value="保 存" class="btn-g"/></td>
</tr>
</form>
</table>
<script type="text/javascript">
function Ign() {
	var release = parseInt(destoon_lastrelease.replace("-", "").replace("-", ""));
	if(confirm('确定要忽略'+release+'更新吗？此更新将不再提醒')) {		
		$.post('?', 'file=update&action=ignore&release='+release, function(data) {$('#tips_update<?php echo $ign ? '_ignore' : '';?>').slideUp(300);});
	}
}
<?php if($ign) { ?>
$(function(){
	var interval = window.setInterval(function() {
		var ir = '<?php echo $ign['release'];?>';
		var cr = '<?php echo DT_RELEASE; ?>';
		var lr = $('#last_r').html().replace("-", "").replace("-", "");
		if(lr.length == 8) {
			clearInterval(interval);
			if(lr != cr && lr != ir) $.post('?', 'file=update&action=new&release='+lr, function(data) {$('#tips_update_ignore').slideDown(600);});
		}
	},  1000);
});
<?php } ?>
Menuon(0);
</script>
<div id="todo"></div>
<?php if($_founder) {?>
<div id="destoon"></div>
<div class="tt">系统信息</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">程序信息</td>
<td>&nbsp;<a href="?file=cloud&action=update" class="t">DESTOON Version <?php echo DT_VERSION;?> Release <?php echo DT_RELEASE;?> <?php echo DT_CHARSET;?> <?php echo strtoupper(DT_LANG);?> [检查更新]</a></td>
</tr>
<tr>
<td class="tl">软件版本</td>
<?php if($edition == '&#20010;&#20154;&#29256;') { ?>
<td id="destoon_edition">&nbsp;<span class="f_blue"><?php echo $edition;?></span> <span class="f_red">(未授权)</span>&nbsp;&nbsp;<a href="?file=cloud&action=buy" target="_blank" class="t">[购买授权]</a></td>
<?php } else { ?>
<td id="destoon_edition">&nbsp;<span class="f_blue">商业<?php echo $edition;?></span>&nbsp;&nbsp;<a href="?file=cloud&action=biz" target="_blank" class="t" title="技术支持">[技术支持]</a></td>
<?php } ?>
</tr>
<tr>
<td class="tl">安装时间</td>
<td>&nbsp;<?php echo $install;?></td>
</tr>
<tr>
<td class="tl">官方网站</td>
<td>&nbsp;<a href="https://www.destoon.com/?tracert=AdminMain" target="_blank">https://www.destoon.com/</a></td>
</tr>
<tr>
<td class="tl">服务器时间</td>
<td>&nbsp;<?php echo timetodate(DT_TIME, 'Y-m-d H:i:s l');?></td>
</tr>
<tr>
<td class="tl">服务器IP</td>
<td>&nbsp;<?php $sip = gethostbyname($_SERVER['SERVER_NAME']); echo $sip;?>:<?php echo $_SERVER["SERVER_PORT"];?> - <?php echo ip2area($sip);?></td>
</tr>
<tr>
<td class="tl">服务器信息</td>
<td>&nbsp;<?php echo PHP_OS.'&nbsp;'.$_SERVER["SERVER_SOFTWARE"];?> / <a href="?file=doctor&action=phpinfo" target="_blank">PHP<?php echo PHP_VERSION;?></a> / <a href="?file=database&action=process" target="_blank">MySQL<?php echo DB::version();?></a>&nbsp;&nbsp;<a href="?file=doctor" class="t">[系统体检]</a></td>
</tr>
<tr>
<td class="tl">站点路径</td>
<td>&nbsp;<?php echo DT_ROOT;?></td>
</tr>
</table>
<div class="tt">使用协议</div>
<table cellspacing="0" class="tb">
<tr>
<td style="padding:10px;"><textarea style="width:99%;height:100px;" onmouseover="this.style.height='600px';" onmouseout="this.style.height='100px';"><?php echo file_get(DT_ROOT.'/license.txt');?></textarea></td>
</tr>
</table>
<script type="text/javascript" src="?file=count&action=todo&rand=<?php echo DT_TIME;?>"></script>
<?php } ?>