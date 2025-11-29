<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<?php echo $idtype_select;?>&nbsp;
<?php echo $bank_select;?>&nbsp;
<?php echo $cover_select;?>&nbsp;
<input type="text" name="username" value="<?php echo $username;?>" size="12" placeholder="会员名" title="会员名"/>&nbsp;
<input type="text" name="uid" value="<?php echo $uid;?>" size="12" placeholder="会员ID" title="会员ID"/>&nbsp;
</td>
</tr>
</table>
</form>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20" title=""></th>
<th width="48">头像</th>
<th>会员名</th>
<th>证件</th>
<th>号码</th>
<th>银行</th>
<th data-hide-1200="1" data-hide-1400="1">支行</th>
<th>账号</th>
<th width="150" data-hide-1200="1" data-hide-1400="1" data-hide-1600="1">自动回复</th>
<th width="150" data-hide-1200="1" data-hide-1400="1" data-hide-1600="1">注册理由</th>
<th width="150">备注</th>
<th width="40">登入</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><?php if($v['cover']) {?> <a href="javascript:;" onclick="_preview('<?php echo $v['cover'];?>');"><img src="<?php echo DT_STATIC;?>admin/img.png" width="16" height="16" title="空间封面,点击预览" alt=""/></a><?php } ?></td>
<td><img src="<?php echo useravatar($v['username'], 'large');?>" width="48" height="48" class="c_p avatar" onclick="_preview(this.src);"/></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('idtype', this.innerHTML);"><?php echo $v['idtype'];?></a></td>
<td><?php echo $v['idno'];?></td>
<td><a href="javascript:;" onclick="Dq('bank', this.innerHTML);"><?php echo $v['bank'];?></a></td>
<td data-hide-1200="1" data-hide-1400="1"><a href="javascript:;" onclick="Dq('fields',5,0);Dq('kw','='+this.innerHTML);"><?php echo $v['branch'];?></a></td>
<td><?php echo $v['account'];?></td>
<td data-hide-1200="1" data-hide-1400="1" data-hide-1600="1" title="<?php echo $v['reply'];?>"><textarea style="width:150px;height:32px;"><?php echo $v['reply'];?></textarea></td>
<td data-hide-1200="1" data-hide-1400="1" data-hide-1600="1" title="<?php echo $v['reason'];?>"><textarea style="width:150px;height:32px;"><?php echo $v['reason'];?></textarea></td>
<td title="<?php echo $v['note'];?>"><textarea style="width:150px;height:32px;"><?php echo $v['note'];?></textarea></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&action=login&userid=<?php echo $v['userid'];?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/import.png" width="16" height="16" title="进入会员中心" alt=""/></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&action=edit&userid=<?php echo $v['userid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(3);</script>
<?php include tpl('footer');?>