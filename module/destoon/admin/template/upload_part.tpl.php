<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<input type="text" name="url" size="70" id="url" placeholder="文件网址或会员名"/> &nbsp; <input type="button"  value="搜 索" class="btn" onclick="Dfind();"/> <span id="durl" class="f_red"></span>
</div>
<table cellspacing="0" class="tb ls">
<tr>
<th width="156">名称</th>
<th width="150">表名</th>
<th width="100">记录</th>
<th width="100">分表</th>
<th></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&id=<?php echo $k;?>', '上传记录[<?php echo $k;?>]');"><?php echo $v['name'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&id=<?php echo $k;?>', '上传记录[<?php echo $k;?>]');"><?php echo $v['table'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?file=<?php echo $file;?>&id=<?php echo $k;?>', '上传记录[<?php echo $k;?>]');"><?php echo $v['rows'];?></a></td>
<td><?php echo $k;?></td>
<td></td>
</tr>
<?php }?>
</table>

<div class="tt">删除上传</div>
<form method="post" action="?" onsubmit="return Dcheck();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="delete_user"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">会员名</td>
<td><input type="text" name="username" size="20" id="username"/> &nbsp; <input type="submit"  value=" 删除 " class="btn-r"/> <span id="dusername" class="f_red"></span>&nbsp;&nbsp;<span class="f_gray">删除会员的所有上传文件</span>
</td>
</tr>
</table>
</form>
<script type="text/javascript">
function Dcheck() {
	var l;
	var f;
	f = 'username';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写会员名', f);
		return false;
	}
	return confirm('确定要删除会员 '+Dd(f).value+' 的所有上传文件吗？此操作不可撤销');
}
function Dfind() {
	var l;
	var f;
	f = 'url';
	l = Dd(f).value.length;
	if(l < 3) {
		Dmsg('请填写文件网址或会员名', f);
		return false;
	}
	Dwidget('?file=<?php echo $file;?>&action=find&url='+Dd(f).value, '查找文件');
}
Menuon(0);
</script>
<?php include tpl('footer');?>