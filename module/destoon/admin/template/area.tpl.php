<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<div class="sbox">
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<input type="submit" name="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 搜" class="btn" onclick="Go('?file=<?php echo $file;?>');"/>&nbsp;
</div>
</form>
<form method="post">
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<?php if($parentid) {?>
<div class="tt"><a href="?file=<?php echo $file;?>&parentid=<?php echo $AREA[$parentid]['parentid'];?>" title="返回上级"><?php echo $AREA[$parentid]['areaname'];?></a></div>
<?php }?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="100">排序</th>
<th width="100">ID</th>
<th width="100">上级ID</th>
<th width="200">地区名</th>
<th width="80">子地区</th>
<th width="40">新增</th>
<th width="40">管理</th>
<th></th>
</tr>
<?php foreach($DAREA as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="areaids[]" value="<?php echo $v['areaid'];?>"/></td>
<td><input name="area[<?php echo $v['areaid'];?>][listorder]" type="text" size="5" value="<?php echo $v['listorder'];?>"/></td>
<td>&nbsp;<?php echo $v['areaid'];?></td>
<td><input name="area[<?php echo $v['areaid'];?>][parentid]" type="text" size="10" value="<?php echo $v['parentid'];?>"/></td>
<td><input name="area[<?php echo $v['areaid'];?>][areaname]" type="text" size="20" value="<?php echo $v['areaname'];?>"/></td>
<td>&nbsp;<a href="?file=<?php echo $file;?>&parentid=<?php echo $v['areaid'];?>"><?php echo $v['childs'];?></a></td>
<td><a href="?file=<?php echo $file;?>&action=add&parentid=<?php echo $v['areaid'];?>"><img src="<?php echo DT_STATIC;?>admin/add.png" width="16" height="16" title="添加子地区" alt=""/></a></td>
<td><a href="?file=<?php echo $file;?>&parentid=<?php echo $v['areaid'];?>"><img src="<?php echo DT_STATIC;?>admin/child.png" width="16" height="16" title="管理子地区，当前有<?php echo $v['childs'];?>个子地区" alt=""/></a></td>
<td></td>
</tr>
<?php }?>
<tr>
<td><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td colspan="8">
<input type="submit" name="submit" value="保存修改" class="btn-g" onclick="this.form.action='?file=<?php echo $file;?>&parentid=<?php echo $parentid;?>&action=update'"/>&nbsp;&nbsp;
<input type="submit" value="删除选中" class="btn-r" onclick="if(confirm('确定要删除选中地区吗？此操作将不可撤销')){this.form.action='?file=<?php echo $file;?>&parentid=<?php echo $parentid;?>&action=delete'}else{return false;}"/>&nbsp;&nbsp;
<?php if($parentid) {?>
<input type="botton" value="返回上级" class="btn" onclick="Go('?file=<?php echo $file;?>&parentid=<?php echo $AREA[$parentid]['parentid'];?>');"/>&nbsp;&nbsp;
<?php }?>
&nbsp;&nbsp;
地区总数:<strong class="f_red"><?php echo count($AREA);?></strong>&nbsp;&nbsp;
当前目录:<strong class="f_blue"><?php echo count($DAREA);?></strong>&nbsp;&nbsp;
</td>
</tr>
</table>
</form>
<form method="post" action="?">
<div class="tt">快捷操作</div>
<table cellspacing="0" class="tb">
<tr align="center">
<td>
<div style="float:left;padding:10px;">
<?php echo ajax_area_select('aid', '选择地区', $parentid, 'size="2" style="width:200px;height:160px;font-size:14px;"');?></div>
<div style="float:left;">
	<table class="ctb">
	<tr>
	<td><input type="submit" value="管理地区" class="btn" onclick="this.form.action='?file=<?php echo $file;?>&parentid='+Dd('areaid_1').value;"/></td>
	</tr>
	<tr>
	<td><input type="submit" value="添加地区" class="btn" onclick="this.form.action='?file=<?php echo $file;?>&action=add&parentid='+Dd('areaid_1').value;"/></td>
	</tr>
	<tr>
	<td><input type="submit" value="删除地区" class="btn-r" onclick="if(confirm('确定要删除选中地区吗？此操作将不可撤销')){this.form.action='?file=<?php echo $file;?>&action=delete&areaid='+Dd('areaid_1').value;}else{return false;}"/></td>
	</tr>
	</table>
</div>
</td>
</tr>
</table>
</div>
</form>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>