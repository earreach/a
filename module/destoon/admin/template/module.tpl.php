<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<select name="mod">
<option value=""<?php if(!$mod) echo ' selected';?>>模块</option>
<?php foreach($sysmodules as $k=>$v) { ?>
<option value="<?php echo $k;?>"<?php if($mod == $k) echo ' selected';?>><?php echo $v['name'].' '.$v['module'];?></option>
<?php } ?>
</select>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</form>
</div>
<form action="?" method="post">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="order"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="60">排序</th>
<th width="40">ID</th>
<th width="200">名称</th>
<th width="100">目录</th>
<th width="100">模型</th>
<th width="40">导航</th>
<th width="40">状态</th>
<th width="40">修改</th>
<th width="40">删除</th>
<th></th>
</tr>
<?php foreach($modules as $k=>$v) {?>
<tr align="center">
<td><input type="text" size="2" name="listorder[<?php echo $v['moduleid'];?>]" value="<?php echo $v['listorder'];?>"/></td>
<td><?php echo $v['moduleid'];?></td>
<td><a href="<?php echo $v['linkurl'];?>" target="_blank" class="t"><?php echo set_style($v['name'], $v['style']);?></a></td>
<td id="dir-<?php echo $v['moduleid'];?>">
<?php if($v['moduledir']) {?>
<a href="javascript:Dconfirm('确定要重建[<?php echo $v['name'];?>]模块目录吗?<br/>如果没有误删除 <?php echo $v['moduledir'];?> 目录，则无需重建', '?file=<?php echo $file;?>&action=remkdir&mid=<?php echo $v['moduleid'];?>');" title="点击重建目录"><?php echo $v['moduledir'];?></a>
<?php } else {?>
--
<?php } ?>
<td title="module/<?php echo $v['moduleen'];?>"><a href="javascript:;" onclick="Dq('mod','<?php echo $v['moduleen'];?>');"><?php echo $v['modulecn'];?></a></td>
<td><?php echo $v['ismenu'] ? '<img src="'.DT_STATIC.'image/yes.png"/>' : ''; ?></td>
<td>
<?php if($v['moduleid'] < 5) {?>
<span class="f_green">正常</span>
<?php } else { ?>
<a href="javascript:Dconfirm('确定要禁用[<?php echo $v['name'];?>]模块吗?', '?file=<?php echo $file;?>&action=disable&value=1&mid=<?php echo $v['moduleid'];?>');" title="点击禁用"><span class="f_green">正常</span></a>
<?php } ?>
</td>
<td>
<?php if($v['moduleid'] == 3) {?>

<?php } else { ?>
<a href="?file=<?php echo $file;?>&action=edit&mid=<?php echo $v['moduleid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a>
<?php } ?>
</td>
<td>
<?php if($v['moduleid'] < 5) {?>

<?php } else { ?>
<a href="?file=<?php echo $file;?>&action=delete&mid=<?php echo $v['moduleid'];?>" onclick="return _delete();"><img src="<?php echo DT_STATIC;?>admin/delete.png" width="16" height="16" title="删除" alt=""/></a>
<?php } ?>
</td>
<td></td>
</tr>
<?php }?>
<?php if($_modules) { ?>
<tr>
<th>排序</th>
<th>ID</th>
<th>名称</th>
<th>目录</th>
<th>模型</th>
<th>导航</th>
<th>状态</th>
<th>修改</th>
<th>删除</th>
<th></th>
</tr>
<?php foreach($_modules as $k=>$v) {?>
<tr align="center">
<td><input type="text" size="2" name="listorder[<?php echo $v['moduleid'];?>]" value="<?php echo $v['listorder'];?>"/></td>
<td><?php echo $v['moduleid'];?></td>
<td><?php echo set_style($v['name'], $v['style']);?></td>
<td id="dir-<?php echo $v['moduleid'];?>"><?php echo $v['moduledir'] ? $v['moduledir'] : '--';?></td>
<td title="module/<?php echo $v['moduleen'];?>"><a href="javascript:;" onclick="Dq('mod','<?php echo $v['moduleen'];?>');"><?php echo $v['modulecn'];?></a></td>
<td>--</td>
<td><a href="javascript:Dconfirm('确定要启用[<?php echo $v['name'];?>]模块吗?', '?file=<?php echo $file;?>&action=disable&value=0&mid=<?php echo $v['moduleid'];?>');" title="点击启用"><span class="f_red">禁用</span></a></td>
<td><a href="?file=<?php echo $file;?>&action=edit&mid=<?php echo $v['moduleid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
<td><a href="?file=<?php echo $file;?>&action=edit&mid=<?php echo $v['moduleid'];?>"><a href="?file=<?php echo $file;?>&action=delete&mid=<?php echo $v['moduleid'];?>" onclick="return _delete();"><img src="<?php echo DT_STATIC;?>admin/delete.png" width="16" height="16" title="删除" alt=""/></a></td>
<td></td>
</tr>
<?php } ?>
<?php } ?>
</table>
<div class="btns">
<input type="submit" value=" 更新排序 " class="btn-g"/>&nbsp;
</div>
</form>
<form method="post" action="?">
<div class="tt">快捷操作</div>
<table cellspacing="0" class="tb">
<tr align="center">
<td>
<div style="float:left;padding:10px;">
<select name="mid" id="mid" size="2" style="width:200px;height:360px;font-size:14px;">
<?php 
foreach($MODULE as $k=>$v) {	
	if($v['islink']) continue;
	if($v['module'] == 'destoon') continue;
?>
<option value="<?php echo $v['moduleid'];?>"<?php echo $v['moduleid'] == $fmid ? ' selected' : '';?> module="<?php echo $v['module'];?>"><?php echo $v['name'];?></option>
<?php } ?>
</select>
</div>
<div style="float:left;">
	<table class="ctb">
	<tr>
	<td><input type="button" value="模块设置" class="btn" onclick="Dwidget('?file=setting&moduleid='+$('#mid').val(), this.value+' - '+$('#mid').find('option:selected').text());"></td>
	</tr>
	<tr>
	<td><input type="button" value="管理分类" class="btn" onclick="if($('#mid').val() < 4){return alert($('#mid').find('option:selected').text()+'模块不支持此操作');}Dwidget('?file=category&mid='+$('#mid').val(), this.value+' - '+$('#mid').find('option:selected').text());"></td>
	</tr>
	<tr>
	<td><input type="button" value="定义字段" class="btn" onclick="if($('#mid').val() == 3){return alert($('#mid').find('option:selected').text()+'模块不支持此操作');}Dwidget('?file=fields&mid='+$('#mid').val(), this.value+' - '+$('#mid').find('option:selected').text());"></td>
	</tr>
	<tr>
	<td><input type="button" value="电脑模板" class="btn" onclick="Dwidget('?file=template&dir=<?php echo $CFG['template'];?>/'+$('#mid').find('option:selected').attr('module'), this.value+' - '+$('#mid').find('option:selected').text());"></td>
	</tr>
	<tr>
	<td><input type="button" value="手机模板" class="btn" onclick="Dwidget('?file=template&dir=<?php echo $CFG['template_mobile'];?>/'+$('#mid').find('option:selected').attr('module'), this.value+' - '+$('#mid').find('option:selected').text());"></td>
	</tr>
	<tr>
	<td><input type="button" value="数据统计" class="btn" onclick="if($('#mid').val() == 3){return alert($('#mid').find('option:selected').text()+'模块不支持此操作');}Dwidget('?file=count&action=stats&mid='+$('#mid').val(), this.value+' - '+$('#mid').find('option:selected').text());"></td>
	</tr>
	<tr>
	<td><input type="button" value="数据更新" class="btn" onclick="if($('#mid').val() == 2){return alert($('#mid').find('option:selected').text()+'模块不支持此操作');}Dwidget('?file=html&moduleid='+$('#mid').val(), this.value+' - '+$('#mid').find('option:selected').text());"></td>
	</tr>
	</table>
</div>
</td>
</tr>
</table>
</div>
</form>
<script type="text/javascript">Menuon(1);</script>
<?php if(isset($update)) { ?>
<script type="text/javascript">window.parent.frames[0].location.reload();</script>
<?php } ?>
<?php include tpl('footer');?>