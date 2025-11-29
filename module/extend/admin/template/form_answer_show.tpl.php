<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<a name="q<?php echo $k;?>"></a>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">IP</td>
<td><?php echo $R['ip'];?> - <?php echo ip2area($R['ip']);?></td>
</tr>
<tr>
<td class="tl">会员</td>
<td><a href="javascript:;" onclick="_user('<?php echo $R['username'];?>');" class="t"><?php echo $R['username'] ? $R['username'] : 'Guest';?></a></td>
</tr>
<tr>
<td class="tl">参数</td>
<td><?php echo $R['item'];?></td>
</tr>
<tr>
<td class="tl">时间</td>
<td><?php echo timetodate($R['addtime']);?></td>
</tr>
<?php foreach($Q as $k=>$v) {?>
<tr>
<td class="tl"><?php echo $v['name'];?></td>
<td>
<?php
if($v['type'] == 'area') {
	echo area_pos($A[$k]['content'], ' ');
} else if($v['type'] == 'file') {
	$url = $A[$k]['content'];
	$ext = file_ext($url);
	if(is_image($url)) {
		echo '<a href="'.$url.'" target="_blank"><img src="'.$url.'" onload="if(this.width > 600) this.width=600;"/></a>';
	} else if($ext == 'mp4') {
		echo '<video src="'.$url.'" width="480" height="270" controls="controls"></video>';
	} else if($ext == 'mp3') {
		echo '<audio src="'.$url.'" controls="controls"></audio>';
	} else {
		echo '<a href="'.$url.'" target="_blank" class="t">'.$url.'</a>';
	}
} else if($v['type'] == 'area') {
	echo nl2br($A[$k]['content']);
} else {
	echo $A[$k]['content'];
}
?>
<?php echo $A[$k]['other'] ? '&nbsp;&nbsp;&nbsp;(填写其他:'.$A[$k]['other'].')' : '';?>
</td>
</tr>
<?php } ?>
<tr>
<td class="tl"></td>
<td><input type="button" value="返 回" class="btn-g" onclick="window.history.back(-1);"/></td>
</tr>
</table>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>