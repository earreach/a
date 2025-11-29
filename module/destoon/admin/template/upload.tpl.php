<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="id" value="<?php echo $id;?>"/>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<span data-hide-1200="1"><?php echo dcalendar('fromdate', $fromdate, '-', 1);?> 至 <?php echo dcalendar('todate', $todate, '-', 1);?>&nbsp;</span>
<?php echo $module_select;?>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="checkbox" name="thumb" value="1"<?php echo $thumb ? ' checked' : '';?>/> 图片&nbsp;
<input type="text" size="10" name="username" value="<?php echo $username;?>" placeholder="会员名" title="会员名"/>&nbsp;
<input type="text" size="10" name="itemid" value="<?php echo $itemid;?>" placeholder="信息ID" title="信息ID"/>&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&id=<?php echo $id;?>');"/>
</form>
</div>
<form method="post" action="?">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="id" value="<?php echo $id;?>"/>
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th width="20"></th>
<th width="90">缩略图</th>
<th><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">大小 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('thumb', 1, 0);Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">宽度 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th data-hide-1200="1"><a href="javascript:;" onclick="Dq('thumb', 1, 0);Dq('order','<?php echo $order == 7 ? 8 : 7;?>');">高度 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 8 ? 'asc' : ($order == 7 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th>模块</th>
<th>信息ID</th>
<th>表名</th>
<th data-hide-1200="1">来源</th>
<th>会员名</th>
<th width="150"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">上传时间 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center" title="<?php echo $v['fileurl'];?>">
<td><input name="itemid[]" type="checkbox" value="<?php echo $v['pid'];?>"/></td>
<td><a href="<?php echo $v['fileurl'];?>" target="_blank"><img src="<?php echo DT_PATH.'file/ext/'.$v['ext'].'.gif';?>"/></a></td>
<td>
<a href="javascript:;" onclick="_preview('<?php echo $v['fileurl'];?>');">
	<?php if(is_image($v['fileurl'])) { ?>
	<img src="<?php echo $v['fileurl'];?>" width="80" onerror="$.get('?file=<?php echo $file;?>&id=<?php echo $id;?>&action=delete&itemid=<?php echo $v['pid'];?>&ajax=1');this.src='<?php echo DT_STATIC;?>image/nopic80.png';"/>
	<?php } else if($v['ext'] == 'mp4') { ?>
	<img src="static/image/video.gif" width="80"/>
	<?php } else if($v['ext'] == 'mp3') { ?>
	<img src="static/image/audio.gif" width="80"/>
	<?php } else { ?>
	<img src="<?php echo DT_STATIC;?>image/nopic80.png" width="80"/>
	<?php } ?>
</a>
</td>
<td><a href="javascript:;" onclick="Dq('filesize','<?php echo $v['filesize'];?>');"><?php echo $v['size'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('width','<?php echo $v['width'];?>');"><?php echo $v['width'] ? $v['width'] : '';?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('height','<?php echo $v['height'];?>');"><?php echo $v['height'] ? $v['height'] : '';?></a></td>
<td><a href="javascript:;" onclick="Dq('mid','<?php echo $v['moduleid'];?>');"><?php echo $MODULE[$v['moduleid']]['name'];?></a></td>
<td><a href="<?php echo gourl('?mid='.$v['moduleid'].'&itemid='.$v['itemid'].'&tb='.$v['tb']);?>" target="_blank"><?php echo $v['itemid'];?></a></td>
<td><a href="javascript:;" onclick="Dq('tb','<?php echo $v['tb'];?>');"><?php echo $v['tb'];?></a></td>
<td data-hide-1200="1"><a href="javascript:;" onclick="Dq('upfrom','<?php echo $v['upfrom'];?>');"><?php echo $v['upfrom'];?></a></td>
<td ondblclick="Dq('username','<?php echo $v['username'];?>');"><a href="javascript:;" onclick="_user('<?php echo $v['username'];?>');"><?php echo $v['username'];?></a></td>
<td><a href="javascript:;" onclick="Dq('date',this.innerHTML);"><?php echo $v['addtime'];?></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input title="删除记录和对应文件" type="submit" value="删除文件" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？系统同时会删除对应文件，此操作将不可撤销')){this.form.action='?file=<?php echo $file;?>&id=<?php echo $id;?>&action=delete'}else{return false;}"/>&nbsp;&nbsp;
<input title="仅删除记录" type="submit" value="删除记录" class="btn-r" onclick="if(confirm('确定要删除选中记录吗？此操作将不可撤销')){this.form.action='?file=<?php echo $file;?>&id=<?php echo $id;?>&action=delete_record'}else{return false;}"/>&nbsp;&nbsp;
<?php if(!$lists && $kw) {?>
&nbsp;&nbsp;&nbsp;&nbsp;<span class="f_red">未找到记录</span>
<?php }?>
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<?php include tpl('footer');?>