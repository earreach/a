<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="text" size="50" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>');"/>
</form>
</div>
<form method="post" action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="40">删除</th>
<th width="360">问题</th>
<th width="360">答案</th>
<th></th>
</tr>
<?php foreach($lists as $k=>$v) { ?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['qid'];?>"/></td>
<td><input name="post[<?php echo $v['qid'];?>][question]" type="text" size="50" value="<?php echo $v['question'];?>"/></td>
<td><input name="post[<?php echo $v['qid'];?>][answer]" type="text" size="50" value="<?php echo $v['answer'];?>"/></td>
<td></td>
</tr>
<?php } ?>
<tr align="center">
<td class="f_green">新增</td>
<td><textarea name="post[0][question]" rows="10" cols="50"></textarea></td>
<td><textarea name="post[0][answer]" rows="10" cols="50"></textarea></td>
<td></td>
</tr>
<tr>
<td align="center"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td height="30" colspan="4">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="保 存" class="btn-g" onclick="this.form.action='?action=update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?action=delete';}else{return false;}}else{confirm('请选择要删除的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
</td>
</tr>
<?php if($pages) { ?>
<tr>
<td colspan="5"><?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?></td>
</tr>
<?php } ?>
<tr>
<td> </td>
<td colspan="4" class="ts">
&nbsp;&nbsp;1、批量添加时，问题和答案一行一个，互相对应<br/>
&nbsp;&nbsp;2、如果答案不唯一，请将多个答案用“|”隔开<br/>
</td>
</tr>
</table>
</form>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>