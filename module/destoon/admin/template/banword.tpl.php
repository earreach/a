<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<?php echo $type_select;?>&nbsp;
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
<th width="300">查找词语</th>
<th width="300">替换为</th>
<th width="200">拦截模式</th>
<th width="200">词语分类</th>
<th></th>
</tr>
<?php foreach($lists as $k=>$v) { ?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['bid'];?>"/></td>
<td><input name="post[<?php echo $v['bid'];?>][replacefrom]" type="text" size="40" value="<?php echo $v['replacefrom'];?>"/></td>
<td><input name="post[<?php echo $v['bid'];?>][replaceto]" type="text" size="40" value="<?php echo $v['replaceto'];?>"/></td>
<td>
<label><input name="post[<?php echo $v['bid'];?>][deny]" type="radio" value="0" <?php if($v['deny'] == 0) echo 'checked';?>/> 替换</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input name="post[<?php echo $v['bid'];?>][deny]" type="radio" value="1" <?php if($v['deny'] == 1) echo 'checked';?>/> 拦截</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input name="post[<?php echo $v['bid'];?>][deny]" type="radio" value="2" <?php if($v['deny'] == 2) echo 'checked';?>/> 提示</label>
</td>
<td><?php echo $v['type_select'];?></td>
<td></td>
</tr>
<?php } ?>
<tr align="center">
<td class="f_green">新增</td>
<td><textarea name="post[0][replacefrom]" rows="10" cols="40"></textarea></td>
<td><textarea name="post[0][replaceto]" rows="10" cols="40"></textarea></td>
<td>
<label><input name="post[0][deny]" type="radio" value="0" checked/> 替换</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input name="post[0][deny]" type="radio" value="1"/> 拦截</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input name="post[0][deny]" type="radio" value="2"/> 提示</label>
</td>
<td><?php echo $type_select_post;?></td>
<td></td>
</tr>
<tr>
<td align="center"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></td>
<td height="30" colspan="5">&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="保 存" class="btn-g" onclick="this.form.action='?action=update';"/>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" value="删 除" class="btn-r" onclick="if($(':checkbox:checked').length){if(confirm('确定要删除'+$(':checkbox:checked').length+'个选中项吗？此操作将不可撤销')) {this.form.action='?action=delete';}else{return false;}}else{confirm('请选择要删除的项目');return false;}"/>&nbsp;&nbsp;&nbsp;&nbsp;
</td>
</tr>
<?php if($pages) { ?>
<tr>
<td colspan="6"><?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?></td>
</tr>
<?php } ?>
<tr>
<td></td>
<td colspan="5" class="ts">
&nbsp;&nbsp;批量添加时，查找和替换词语一行一个，互相对应<br/>
&nbsp;&nbsp;例如“您*好”格式，可替换“您好”之间的干扰字符<br/>
&nbsp;&nbsp;为不影响程序效率，请不要设置过多过滤内容<br/>
&nbsp;&nbsp;过滤仅对前台会员提交信息生效，后台不受限制<br/>
&nbsp;&nbsp;如果选择替换，则匹配到查找词语时直接替换，正常提交<br/>
&nbsp;&nbsp;如果选择拦截，则匹配到查找词语时直接提示拦截，拒绝提交<br/>
&nbsp;&nbsp;如果选择提示，则匹配到查找词语时直接提示拦截词语，拒绝提交<br/>
</td>
</tr>
</table>
</form>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>