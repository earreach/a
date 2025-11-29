<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="send" value="1"/>
<input type="hidden" name="preview" id="preview" value="0"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 发送方式</td>
<td>
	<label><input type="radio" name="sendtype" value="1" id="s1" onclick="ck(1);"<?php echo $sendtype == 1 ? ' checked' : '';?>/> 指定会员</label>&nbsp;&nbsp;
	<label><input type="radio" name="sendtype" value="2" id="s2" onclick="ck(2);"<?php echo $sendtype == 2 ? ' checked' : '';?>/> 系统广播</label>&nbsp;&nbsp;
	<label><input type="radio" name="sendtype" value="3" id="s3" onclick="ck(3);"<?php echo $sendtype == 3 ? ' checked' : '';?>/> 列表群发</label>&nbsp;&nbsp;
	<label><input type="radio" name="sendtype" value="4" id="s4" onclick="ck(4);"<?php echo $sendtype == 4 ? ' checked' : '';?>/> 条件群发</label>&nbsp;&nbsp;
</td>
</tr>
<tbody id="t1" style="display:;">
<tr>
<td class="tl"><span class="f_red">*</span> 收信会员</td>
<td><input type="text" size="70" name="touser" id="touser" value="<?php echo $touser;?>"/> <span id="dtouser" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">多个收信会员请用空格分隔</td>
</tr>
</tbody>
<tbody id="t2" style="display:none;">
<tr>
<td class="tl"><span class="f_red">*</span> 会员组</td>
<td><?php echo group_checkbox('groupids[]', '', '2,3,4');?></td>
</tr>
<tr>
<td class="tl"></td>
<td class="ts">信件不会真实发送，只是置顶在对应组的收件箱</td>
</tr>
</tbody>
<tbody id="t3" style="display:none;">
<tr>
<td class="tl"><span class="f_red">*</span> 会员列表</td>
<td class="f_red">
<?php
	$files = glob(DT_ROOT.'/file/'.$path.'/*.txt');
	echo '<select name="list" id="list"><option value="0">请选择会员列表</option>';
	if($files) {
		foreach($files as $v) {
			$c = file_get($v);
			$name = basename($v);
			$note = cutstr($c, '#', "\r\n");
			$nums = substr_count($c, "\n") + ($note ? 0 : 1);
			echo '<option value="'.$name.'" class="f_fd">'.$name.($note ? ' '.$note : '').' '.$nums.'条</option>';
		}
	} else {
		echo '<option value="">暂无列表</option>';
	}
	echo '</select> &nbsp; ';
?>
<img src="<?php echo DT_STATIC;?>image/ico-link.png" width="11" height="11" title="查看" class="c_p" onclick="if(Dd('list').value != 0){Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=view&filename='+Dd('list').value, $('#list').find('option:selected').text(), 600, 400);}else{alert('请先选择文件');Dd('list').focus();}"/> &nbsp;
<img src="<?php echo DT_STATIC;?>image/ico-add.png" width="11" height="11" title="获取" class="c_p" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=make', '获取列表');"/> &nbsp; 
</td>
</tr>
</tbody>
<tbody id="t4" style="display:none;">
<?php include tpl('sendlist_chip', $module);?>
</tbody>
<tbody id="t0" style="display:none;">
<tr>
<td class="tl"><span class="f_red">*</span> 每轮发送</td>
<td><input type="text" size="5" name="pernum" id="pernum" value="100"/></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 时间间隔</td>
<td><input type="text" size="5" name="pertime" id="pertime" value="0"/><?php tips('例如设置为3，则系统在每轮发送之后暂停3秒，以免因为发送过快而被收件服务器拒收');?></td>
</tr>
</tbody>
<tr>
<td class="tl"><span class="f_red">*</span> 信件标题</td>
<td><input type="text" size="70" name="title" id="title"/> <span id="dtitle" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 信件内容</td>
<td><textarea name="content" id="content" class="dsn"></textarea>
<?php echo deditor($moduleid, 'content', 'Message', '100%', 350);?><br/><span id="dcontent" class="f_red"></span>
</td>
</tr>
</tbody>
</table>
<div class="sbt"><input type="submit" name="submit" value=" 确 定 " class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<?php load('clear.js'); ?>
<script type="text/javascript">
var i = 1;
function ck(id) {
	Dd('t'+i).style.display='none';
	Dd('t'+id).style.display='';
	Dd('t0').style.display=(id==3||id==4) ? '' : 'none';
	i = id;
}
ck(<?php echo $sendtype;?>);
function check() {
	var l;
	var f;
	f = 'title';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('标题最少2字，当前已输入'+l+'字', f);
		return false;
	}
	f = 'content';
	l = EditorLen();
	if(l < 5) {
		Dmsg('内容最少5字，当前已输入'+l+'字', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(0);</script>
<?php include tpl('footer');?>