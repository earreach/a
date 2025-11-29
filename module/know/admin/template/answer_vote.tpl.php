<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="10" placeholder="会员名" title="会员名 双击显示会员资料" ondblclick="if(this.value){_user(this.value);}"/>&nbsp;
<input type="text" name="ip" value="<?php echo $ip;?>" size="10" placeholder="IP" title="IP" ondblclick="if(this.value){_ip(this.value);}"/>&nbsp;
<input type="text" size="10" name="qid" value="<?php echo $qid;?>" placeholder="问题ID" title="问题ID"/>&nbsp;
<input type="text" size="10" name="aid" value="<?php echo $aid;?>" placeholder="答案ID" title="答案ID"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>'+(Dwidget() ? '&qid=<?php echo $qid;?>' : ''));"/>
</form>
</div>
<table cellspacing="0" class="tb ls">
<tr>
<th width="150">投票时间</th>
<th>会员名</th>
<th>昵称</th>
<th>IP</th>
<th>问题ID</th>
<th>答案ID</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
<td><a href="javascript:;" onclick="_user(this.innerHTML);"><?php echo $v['username'] ? $v['username'] : 'guest';?></a></td>
<td><a href="javascript:;" onclick="Dq('username','<?php echo $v['username'];?>');"><?php echo $v['passport'] ? $v['passport'] : '游客';?></a></td>
<td><a href="javascript:;" onclick="_ip(this.innerHTML);"><?php echo $v['ip'];?></a></td>
<td><a href="javascript:;" onclick="Dq('qid','<?php echo $v['qid'];?>');"><?php echo $v['qid'];?></a></td>
<td><a href="javascript:;" onclick="Dq('aid','<?php echo $v['aid'];?>');"><?php echo $v['aid'];?></a></td>
</tr>
<?php }?>
</table>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(3);</script>
<?php include tpl('footer');?>