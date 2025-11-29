<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<base target="_parent"/>
<div class="sbox">
<form action="?" target="_self">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
&nbsp;<input type="text" size="50" name="kw" placeholder="请输入关键词" id="kw" value="<?php echo $kw;?>" title="关键词" style="background:url('static/admin/search.png') no-repeat 6px center;padding:8px 32px;border:#A6A6A6 1px solid;" x-webkit-speech speech/>
&nbsp;<input type="submit" name="submit" value="开始搜索" class="btn-g"/>
&nbsp;<input type="button" value="重新搜索" class="btn" onclick="Go('?file=<?php echo $file;?>');"/>
&nbsp;&nbsp;<span class="f_gray">输入关键词，例如“<a href="?file=<?php echo $file;?>&kw=<?php echo urlencode('手机短信');?>" class="b" target="_self">手机短信</a>”、“<a href="?file=<?php echo $file;?>&kw=<?php echo urlencode('支付接口');?>" class="b" target="_self">支付接口</a>”</span>
</form>
</div>
<?php if($kw) { ?>
<table cellspacing="0" class="tb">
<?php if($lists || $files) { ?>
<tr>
<th>名称</th>
<th width="60">查看</th>
</tr>

<?php if($files) { ?>
<?php foreach($files as $k=>$v) {?>
<tr align="center">
<td align="left" height="22">&nbsp;&nbsp;<img src="file/ext/folder.gif" align="absmiddle"/> <a href="<?php echo $v[1];?>&search=1#high"><?php echo $v[0];?></a></td>
<td><a href="<?php echo $v[1];?>&search=1#high" target="_blank"><img src="<?php echo DT_STATIC;?>admin/link.png" width="16" height="16"/></a></td>
</tr>
<?php }?>
<?php }?>

<?php if($lists) { ?>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td align="left" height="22">&nbsp;&nbsp;<img src="file/ext/folder.gif" align="absmiddle"/> <a href="?moduleid=<?php echo $k;?>&file=setting&kw=<?php echo $ukw;?>&search=1#high"><?php echo $v['name'];?></a></td>
<td><a href="?moduleid=<?php echo $k;?>&file=setting&kw=<?php echo urlencode($kw);?>&search=1#high" target="_blank"><img src="<?php echo DT_STATIC;?>admin/link.png" width="16" height="16"/></a></td>
</tr>
<?php }?>
<?php }?>

<?php } else { ?>
<tr>
<td class="f_blue" height="40">&nbsp;- 未找到到相关设置，请调整关键词再试&nbsp;&nbsp;&nbsp;&nbsp;<a href="?file=<?php echo $file;?>" class="t" target="_self">[重新搜索]</a></td>
</tr>
<?php } ?>
</table>
<?php } else { ?>

<style type="text/css">
dl{margin:0;width:100%;overflow:hidden;}
dd{line-height:36px;height:36px;margin-left:0;text-align:center;}
dd a{width:100%;height:36px;display:block;font-size:14px;}
dd a:hover{background:#2B579A;color:#FFFFFF;}
</style>
<table cellspacing="0" class="tb">
<tr>
<th>我的面板</th>
<th>系统维护</th>
<th>功能模块</th>
<th>会员管理</th>
<th>公司管理</th>
<th>财务管理</th>
<th>会员相关</th>
<th>扩展功能</th>
<th>系统工具</th>
</tr>
<tr id="map">
<td valign="top">
<?php
include DT_ROOT.'/module/destoon/admin/menu.inc.php';
$panel = cache_read('admin-panel-'.$_userid.'.php');
$menu_tool = $menu;
?>
<dl data-side="1">
	<?php
		foreach($panel as $m) {
	?>
	<dd><a href="<?php echo substr($m['url'], 0, 1) == '?' ? $m['url'] : gourl($m['url']).'" target="_blank';?>"><?php echo set_style($m['title'], $m['style']);?></a></dd>
	<?php
		}
	?>	
	<dd>&nbsp;</dd>
	<dd><a href="?file=panel">定义面板</a></dd>
	<dd><a href="<?php echo DT_PATH;?>" target="_blank">网站首页</a></dd>
	<dd><a href="?action=dashboard" target="_blank">新开后台</a></dd>
	<dd><a href="?action=main">后台首页</a></dd>
	<dd><a href="?action=logout" target="_top" onclick="return confirm('确定要退出管理后台吗');">安全退出</a></dd>
</dl>
</td>
<td valign="top">
	<dl data-side="2">
	<?php
		foreach($menu_system as $m) {
			echo '<dd><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
</td>
<td valign="top">
<dl data-side="3">
	<?php
		$k = 0;
		foreach($MODULE as $v) {
			if($v['moduleid'] > 4 && !$v['islink']) {
				echo '<dd><a href="?moduleid='.$v['moduleid'].'">'.$v['name'].'管理</a></dd>';
			}
		}
	?>
</dl>
</td>
<td valign="top">
<dl data-side="4">
	<?php
		include DT_ROOT.'/module/member/admin/menu.inc.php';
		foreach($menu as $m) {
			echo '<dd><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
</dl>
</td>
<td valign="top">
<dl data-side="4">
	<?php
		include DT_ROOT.'/module/company/admin/menu.inc.php';
		foreach($menu as $m) {
			echo '<dd><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
</dl>
</td>
<td valign="top">
	<dl data-side="4">
	<?php
		foreach($menu_finance as $m) {
			echo '<dd><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
</td>
<td valign="top">
	<dl data-side="4">
	<?php
		foreach($menu_relate as $m) {
			echo '<dd><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
</td>
<td valign="top">
	<dl data-side="5">
	<?php
		include DT_ROOT.'/module/extend/admin/menu.inc.php';
		foreach($menu as $m) {
			echo '<dd><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
</td>
<td valign="top">
	<dl data-side="2"> 
	<?php
		foreach($menu_tool as $m) {
			echo '<dd><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
</td>
</tr>
</table>
<script type="text/javascript">
$(function(){
	$('dd a').click(function() {	
		top.document.getElementsByName('left')[0].contentWindow.sidelink($(this).attr('href'), $(this).parent().parent().attr('data-side'));
	});
	$('#map td').mouseover(function() {
		$(this).css('background', '#F9F9F9');
	});
	$('#map td').mouseout(function() {
		$(this).css('background', '');
	});
});
</script>
<?php } ?>
<?php include tpl('footer');?>