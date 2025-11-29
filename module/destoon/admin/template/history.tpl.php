<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<table cellspacing="0" class="tb ls">
<tr>
<th width="10%">项目</th>
<th width="45%">修改前</th>
<th width="45%">修改为</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr>
<td><?php echo $v['name'];?></td>
<td><?php echo $v['old'];?></td>
<td><?php echo $v['new'];?></td>
</tr>
<?php }?>
</table>
<?php if($new) {?>
<div class="tt"><span class="f_r jt" style="font-weight:normal;font-size:12px;" onclick="$('#old_text').toggle();$('#old_html').toggle();$('#new_text').toggle();$('#new_html').toggle();">源代码</span>内容修改前</div>
<table cellspacing="0" class="tb">
<tr>
<td>
<div id="old_text" class="px14 lh20"><?php echo $old;?></div>
<textarea id="old_html" style="width:98%;height:300px;display:none;"><?php echo $old;?></textarea>
</td>
</tr>
</table>
<div class="tt">内容修改为</div>
<table cellspacing="0" class="tb">
<tr>
<td>
<div id="new_text" class="px14 lh20"><?php echo $new;?></div>
<textarea id="new_html" style="width:98%;height:300px;display:none;"><?php echo $new;?></textarea>
</td>
</tr>
</table>
<?php } ?>
<br/><br/><center><input type="button" value="确 定" class="btn" onclick="window.parent.cDialog();"/></center><br/><br/>
<?php include tpl('footer');?>