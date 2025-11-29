<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
if(!$action) show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<select name="hate">
<option value="-1" <?php echo $hate == -1 ? 'selected' : '';?>>态度</option>
<option value="0" <?php echo $hate == 0 ? 'selected' : '';?>>支持</option>
<option value="1" <?php echo $hate == 1 ? 'selected' : '';?>>反对</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<?php echo $module_select;?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" size="10" name="tid" value="<?php echo $tid;?>" placeholder="信息ID" title="信息ID"/>&nbsp;
<input type="text" size="10" name="rid" value="<?php echo $rid;?>" placeholder="回复/评论ID" title="回复/评论ID"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>'+(Dwidget() ? '&tid=<?php echo $tid;?>&rid=<?php echo $rid;?>' : ''));"/>
</form>
</div>
<table cellspacing="0" class="tb ls">
<tr>
<th width="150">时间</th>
<th width="150">会员名</th>
<th width="100">态度</th>
<th width="100">模块</th>
<th width="100">信息ID</th>
<th width="100"><?php echo $rname;?></th>
<th></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('hate','<?php echo $v['hate'];?>');"><?php echo $v['hate'] ? '<span class="f_red">反对</span>' : '支持';?></a></td>
<td><a href="javascript:;" onclick="Dq('mid','<?php echo $v['mid'];?>');"><?php echo $MODULE[$v['mid']]['name'];?></a></td>
<td><?php if($v['url']) {?><a href="<?php echo $v['url'];?>" target="_blank"><?php } else {?><a href="javascript:;" onclick="Dq('mid','<?php echo $v['mid'];?>',0);Dq('tid','<?php echo $v['tid'];?>');"><?php } ?><?php echo $v['tid'];?></a></td>
<td><a href="javascript:;" onclick="Dq('mid','<?php echo $v['mid'];?>',0);Dq('rid','<?php echo $v['rid'];?>');"><?php echo $v['rid'];?></a></td>
<td></td>
</tr>
<?php }?>
</table>
<div class="btns">
<input type="button" value="清理记录" class="btn-r" onclick="if(confirm('为了系统安全，系统仅删除60天之前的记录\n此操作不可撤销，请谨慎操作')){Go('?file=<?php echo $file;?>&action=clear');}"/>
</div>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>