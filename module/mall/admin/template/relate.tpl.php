<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<form method="post" action="?action=relate_update" id="dform">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 关联名称</td>
<td class="f_gray"><input type="text" size="20" name="relate_name" id="relate_name" value="<?php echo $M['relate_name'];?>"/>&nbsp;&nbsp; 例如<span class="c_p" onclick="Dd('relate_name').value='颜色';">“颜色”</span>、<span class="c_p" onclick="Dd('relate_name').value='尺寸';">“尺寸”</span>、<span class="c_p" onclick="Dd('relate_name').value='型号';">“型号”</span>等 <span id="drelate_name" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 选择商品</td>
<td><input type="button" value="点击选择" onclick="add();" class="btn"/></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 商品列表</td>
<td>
<?php foreach($lists as $k=>$v) { ?>
<div style="width:160px;float:left;">
	<table width="150" class="ctb">
	<tr align="center" height="110" class="c_p">
	<td width="120"><a href="<?php echo $MOD['linkurl'];?><?php echo $v['linkurl'];?>" target="_blank"><img src="<?php echo $v['thumb'];?>" width="100" height="100" alt="" title="<?php echo $v['title'];?>"/></a></td>
	</tr>
	<tr align="center">
	<td><textarea name="post[<?php echo $v['itemid'];?>][relate_title]" style="width:90px;height:40px;" placeholder="简略标题：" title="<?php echo $v['relate_title'];?>" onmouseover="this.title=this.value;"><?php echo $v['relate_title'];?></textarea></td>
	</tr>
	<tr align="center">
	<td><input type="text" name="post[<?php echo $v['itemid'];?>][listorder]" value="<?php echo $k > -1 ? $k : 0;?>" style="width:90px;" placeholder="排序：" title="排序"/></td>
	</tr>
	<tr align="center">
	<td><?php if($v['itemid'] == $itemid) { ?>&nbsp;<?php } else { ?><label class="f_gray"><a href="?moduleid=<?php echo $moduleid;?>&action=relate_del&itemid=<?php echo $itemid;?>&id=<?php echo $v['itemid'];?>" onclick="return confirm('确定要移除此商品吗？');"><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="移除"/></a><input type="checkbox" name="id[]" value="<?php echo $v['itemid'];?>" title="选择并移除" style="margin:0 0 0 72px;"/><?php } ?></td>
	</tr>
	</table>
</div>
<?php } ?>
</td>
</tr>
<tr>
<td class="tl"><label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label></td>
<td height="48"> &nbsp; 
<input type="submit" value="保存数据" class="btn-g" onclick="this.form.action='?action=relate_update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="移除选中" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要移除'+$(':checkbox:checked').length+'个选中商品吗？')) {this.form.action='?action=relate_del';}else{return false;}}else{confirm('请选择要移除的商品');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="button" value="完成关联" onclick="window.parent.cDialog();" class="btn"/>
</td>
</tr>
</table>
</form>
<form method="post" action="?" id="dform_add">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="relate_add"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="id" id="id" value="0"/>
<input type="hidden" name="relate_name" id="relate_name_add" value=""/>
</form>
<script type="text/javascript">
function add() {
	if(Dd('relate_name').value.length < 2) {
		Dmsg('请先填写关联名称', 'relate_name');
		return;
	}
	Dd('relate_name_add').value = Dd('relate_name').value;
	select_item('<?php echo $moduleid;?>&username=<?php echo $M['username'];?>', 'relate');
}
</script>
<?php include tpl('footer');?>