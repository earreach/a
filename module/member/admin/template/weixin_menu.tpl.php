<?php
defined('IN_DESTOON') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="200">菜单名称</th>
<th width="400">地址/事件</th>
<th></th>
</tr>
<?php foreach($menu as $k=>$v) { ?>
<?php foreach($v as $kk=>$vv) { ?>
<tr>
<td><?php echo $kk == 0 ? '' : '<img src="'.DT_STATIC.'admin/tree.png" align="absmiddle"/>';?><input name="post[<?php echo $k;?>][<?php echo $kk;?>][name]" type="text" style="width:<?php echo $kk == 0 ? 120 : 100;?>px;" value="<?php echo $vv['name'];?>" maxlength="<?php echo $kk == 0 ? 4 : 7;?>" id="n-<?php echo $k;?>-<?php echo $kk;?>"/></td>
<td><input name="post[<?php echo $k;?>][<?php echo $kk;?>][key]" type="text" size="50" value="<?php echo $vv['key'];?>" id="k-<?php echo $k;?>-<?php echo $kk;?>"/></td>
<td></td>
</tr>
<?php } ?>
<?php } ?>
</table>
<div class="btns">
<input type="submit" name="submit" value="提 交" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;  
<input type="button" value="恢复默认" class="btn" onclick="wx_menu();"/>
</div>
</form>
<div class="tt">地址/事件类型说明</div>
<table cellspacing="0" class="tb">
<tr>
<td class="ts">
留空：一级菜单包含子菜单时，需要留空<br/>
链接：直接填写http开头的网址，例如 <?php echo DT_MOB;?><br/>
事件：直接填写自定义事件名称，例如 V_member<br/>
小程序：appid|pagepath|url，例如 wx286b93c14bbf93aa|pages/lunar/index|http://mp.weixin.qq.com<br/>
</td>
</tr>
</table>
<script type="text/javascript">
function wx_menu() {
	if(confirm('确定要恢复默认设置吗？当前设置将被覆盖')) {
		$('#n-0-0').val('最新');
		$('#n-0-1').val('商城');$('#k-0-1').val('V_mid16');
		$('#n-0-2').val('供应');$('#k-0-2').val('V_mid5');
		$('#n-0-3').val('求购');$('#k-0-3').val('V_mid6');
		$('#n-0-4').val('招商');$('#k-0-4').val('V_mid22');
		$('#n-0-5').val('资讯');$('#k-0-5').val('V_mid21');
		$('#n-1-0').val('会员');$('#k-1-0').val('V_member');
		$('#n-2-0').val('更多');$('#k-2-0').val('<?php echo DT_MOB;?>');
	}
}
Menuon(4);
</script>
<?php include tpl('footer');?>