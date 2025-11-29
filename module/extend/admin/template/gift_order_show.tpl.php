<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">礼品</td>
<td><a href="<?php echo $linkurl;?>" target="_blank" class="t"><?php echo $title;?></a></td>
</tr>
<tr>
<td class="tl"><?php echo $DT['credit_name'];?></td>
<td><?php echo $credit;?></td>
</tr>
<tr>
<td class="tl">会员</td>
<td><a href="javascript:;" onclick="_user('<?php echo $username;?>');" class="t"><?php echo $username;?></a></td>
</tr>
<tr>
<td class="tl">IP</td>
<td><?php echo $ip;?> - <?php echo ip2area($ip);?></td>
</tr>
<tr>
<td class="tl">下单</td>
<td><?php echo $adddate;?></td>
</tr>
<tr>
<td class="tl">更新</td>
<td><?php echo $editdate;?></td>
</tr>
<tr>
<td class="tl">状态</td>
<td>
<input name="post[<?php echo $itemid;?>][status]" type="text" size="10" value="<?php echo $status;?>" id="status_<?php echo $itemid;?>"/>&nbsp;
<select onchange="if(this.value)Dd('status_<?php echo $itemid;?>').value=this.value;">
<option value="">备选状态</option>
<option value="处理中">处理中</option>
<option value="审核中">审核中</option>
<option value="已取消">已取消</option>
<option value="已发货">已发货</option>
<option value="已完成">已完成</option>
</select>&nbsp;
<input type="hidden" name="post[<?php echo $itemid;?>][item_status]" value="<?php echo $status;?>"/>
<input type="hidden" name="post[<?php echo $itemid;?>][item_note]" value="<?php echo $note;?>"/>
</td>
</tr>
<tr>
<td class="tl">快递</td>
<td>
<input name="post[<?php echo $itemid;?>][expressid]" type="text" size="20" value="<?php echo $expressid;?>" placeholder="快递单号：" title="快递单号："/>&nbsp;
<?php echo dselect($send_types, 'post['.$itemid.'][express]', '快递类型', $express, '', 0, '', 1);?>&nbsp;
<?php if($express && $expressid) {?>
<a href="<?php echo DT_PATH;?>api/express<?php echo DT_EXT;?>?action=home&e=<?php echo urlencode($express);?>&n=<?php echo $expressid;?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/link.png" width="16" height="16" title="快递追踪" alt=""/></a>
<?php } ?>
<input type="hidden" name="post[<?php echo $itemid;?>][item_expressid]" value="<?php echo $expressid;?>"/>
<input type="hidden" name="post[<?php echo $itemid;?>][item_express]" value="<?php echo $express;?>"/>
</td>
</tr>
<?php if($auth) {?>
<tr>
<td class="tl">进程</td>
<td style="line-height:200%;"><div id="express"><img src="<?php echo DT_SKIN;?>loading.gif" align="absmiddle"/> 正在查询...</div></td>
</tr>
<?php } ?>
<tr>
<td class="tl">备注</td>
<td><textarea name="post[<?php echo $itemid;?>][note]" style="width:400px;height:200px;overflow:visible;"><?php echo $note;?></textarea></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="更 新" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="try{window.parent.cDialog();}catch(e){window.history.go(-1);}"/></div>
</form>
<script type="text/javascript">
<?php if($auth) {?>
$(function(){
	$('#express').load(AJPath+'?action=express&moduleid=2&auth=<?php echo $auth;?>');
});
<?php } ?>
Menuon(<?php echo $menuid;?>);
</script>
<?php include tpl('footer');?>