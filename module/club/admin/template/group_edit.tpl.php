<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 所属分类</td>
<td><?php echo $_admin == 1 ? category_select('post[catid]', '选择分类', $catid, $moduleid) : ajax_category_select('post[catid]', '选择分类', $catid, $moduleid);?> <span id="dcatid" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 商圈名称</td>
<td><input name="post[title]" type="text" id="title" size="20" value="<?php echo $title;?>"/> <?php echo level_select('post[level]', '级别', $level);?> <?php echo dstyle('post[style]', $style);?> <span id="dtitle" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 商圈图标</td>
<td>
<input type="hidden" name="post[thumb]" id="thumb" value="<?php echo $thumb;?>"/>
<div class="thumbu">
<div><img src="<?php if($thumb) { ?><?php echo $thumb;?><?php } else { ?><?php echo DT_STATIC;?>image/upload-image.png<?php } ?>" id="pthumb" onerror="this.src='<?php echo DT_STATIC;?>image/upload-image.png';Dd('thumb').value='';" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(this.src, 1);}else{Dthumb(<?php echo $moduleid;?>,128,128, Dd('thumb').value);}">&nbsp;&nbsp;<span onclick="Dthumb(<?php echo $moduleid;?>,128,128, Dd('thumb').value);"/></div>
<p><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传" onclick="Dthumb(<?php echo $moduleid;?>,128,128, Dd('thumb').value);">&nbsp;&nbsp;<span onclick="Dthumb(<?php echo $moduleid;?>,128,128, Dd('thumb').value);"/><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="删除" onclick="Dd('thumb').value='';Dd('pthumb').src='<?php echo DT_STATIC;?>image/upload-image.png';"/></p>
</div><span id="dthumb" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 商圈简介</td>
<td><textarea name="post[content]" id="content" style="width:90%;height:80px;"><?php echo $content;?></textarea><br/><span id="dcontent" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 加圈方式</td>
<td>
<label><input type="radio" name="post[join_type]" value="0" <?php if($join_type == 0) echo 'checked';?>/> 自由</label>
<label><input type="radio" name="post[join_type]" value="1" <?php if($join_type == 1) echo 'checked';?>/> 申请</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 粉丝群聊</td>
<td>
<label><input type="radio" name="post[chat]" value="1" <?php if($chat == 1) echo 'checked';?>/> 开启</label>
<label><input type="radio" name="post[chat]" value="0" <?php if($chat == 0) echo 'checked';?>/> 关闭</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 浏览商圈</td>
<td>
<label><input type="radio" name="post[list_type]" value="0" <?php if($list_type == 0) echo 'checked';?>/> 不限</label>
<label><input type="radio" name="post[list_type]" value="1" <?php if($list_type == 1) echo 'checked';?>/> 成员</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 浏览帖子</td>
<td>
<label><input type="radio" name="post[show_type]" value="0" <?php if($show_type == 0) echo 'checked';?>/> 不限</label>
<label><input type="radio" name="post[show_type]" value="1" <?php if($show_type == 1) echo 'checked';?>/> 成员</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 发帖限制</td>
<td>
<label><input type="radio" name="post[post_type]" value="0" <?php if($post_type == 0) echo 'checked';?>/> 不限</label>
<label><input type="radio" name="post[post_type]" value="1" <?php if($post_type == 1) echo 'checked';?>/> 成员</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 回复限制</td>
<td>
<label><input type="radio" name="post[reply_type]" value="0" <?php if($reply_type == 0) echo 'checked';?>/> 不限</label>
<label><input type="radio" name="post[reply_type]" value="1" <?php if($reply_type == 1) echo 'checked';?>/> 成员</label>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 创建者</td>
<td><input name="post[username]" type="text" size="20" value="<?php echo $username;?>" id="username"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('username').value);"/> &nbsp; <span id="dusername" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 版主</td>
<td><input name="post[manager]" type="text" size="70" value="<?php echo $manager;?>" id="manager"/><?php tips('请填写版主会员昵称，多个版主用|分隔');?> <span id="dmanager" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 所在地区</td>
<td><?php echo ajax_area_select('post[areaid]', '请选择', $areaid);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 商圈状态</td>
<td>
<label><input type="radio" name="post[status]" value="3" <?php if($status == 3) echo 'checked';?>/> 通过</label>
<label><input type="radio" name="post[status]" value="2" <?php if($status == 2) echo 'checked';?>/> 待审</label>
<label><input type="radio" name="post[status]" value="1" <?php if($status == 1) echo 'checked';?> onclick="if(this.checked) Dd('note').style.display='';"/> 拒绝</label>
<label><input type="radio" name="post[status]" value="0" <?php if($status == 0) echo 'checked';?>/> 删除</label>
</td>
</tr>
<tr id="note" style="display:<?php echo $status==1 ? '' : 'none';?>">
<td class="tl"><span class="f_red">*</span> 拒绝理由</td>
<td><input name="post[note]" type="text"  size="40" value="<?php echo $note;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 创建时间</td>
<td><?php echo dcalendar('post[addtime]', $addtime, '-', 1);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 创建理由</td>
<td><textarea name="post[reason]" id="reason" style="width:90%;height:80px;"><?php echo $reason;?></textarea><br/><span id="dreason" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 商圈模板</td>
<td><?php echo tpl_select('group', $module, 'post[template]', '默认模板', $template, 'id="template"');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 帖子模板</td>
<td><?php echo tpl_select('show', $module, 'post[show_template]', '默认模板', $show_template, 'id="show_template"');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 粉丝模板</td>
<td><?php echo tpl_select('fans', $module, 'post[fans_template]', '默认模板', $fans_template, 'id="fans_template"');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 群聊模板</td>
<td><?php echo tpl_select('chat', $module, 'post[chat_template]', '默认模板', $chat_template, 'id="chat_template"');?></td>
</tr>
<?php if($MOD['list_html'] && $action == 'edit') { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 静态目录</td>
<td><input type="text" size="22" name="post[filepath]" value="<?php echo $filepath;?>"/> <?php echo tips('限英文、数字、中划线、下划线、斜线，该商圈相关的html文件将保存在此目录');?></td>
</tr>
<?php } ?>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $action == 'edit' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<?php load('clear.js'); ?>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'catid_1';
	if(Dd(f).value == 0) {
		Dmsg('请选择所属分类', 'catid', 1);
		return false;
	}
	f = 'title';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写商圈名称', f);
		return false;
	}
	f = 'thumb';
	l = Dd(f).value.length;
	if(l < 10) {
		Dmsg('请上传商圈LOGO', f);
		return false;
	}
	f = 'username';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写创建者', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>