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
<input type="hidden" name="post[typeid]" value="<?php echo $typeid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 条形编码</td>
<td><input name="post[skuid]" type="text" id="skuid" size="60" value="<?php echo $skuid;?>"/> <?php echo tips('条形码是商品唯一编号，不可重复，建议用扫描枪扫描&#10;如果没有条形码，建议使用数字在保证不重复的条件下自定义');?> <span id="dskuid" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 商品名称</td>
<td><input name="post[title]" type="text" id="title" size="60" value="<?php echo $title;?>"/> <?php echo dstyle('post[style]', $style);?> <span id="dtitle" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 商品图片</td>
<td>
<input type="hidden" name="post[thumb]" id="thumb" value="<?php echo $thumb;?>"/>
<div class="thumbu">
<div><img src="<?php echo $thumb ? $thumb : DT_STATIC.'image/upload-image.png';?>" width="100" height="100" id="showthumb" title="预览图片" alt="" onclick="if(this.src.indexOf('upload-image.png') == -1){_preview(Dd('showthumb').src, 1);}else{Dalbum('',<?php echo $moduleid;?>, 200, 200, Dd('thumb').value, true);}"/></div>
<p><img src="<?php echo DT_STATIC;?>image/ico-upl.png" width="11" height="11" title="上传" onclick="Dalbum('',<?php echo $moduleid;?>, 200, 200, Dd('thumb').value, true);"/><img src="<?php echo DT_STATIC;?>image/ico-del.png" width="11" height="11" title="删除" onclick="delAlbum('');"/></p>
</div><span id="dthumb" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 商品售价</td>
<td><input name="post[price]" type="text" size="10" value="<?php echo $price;?>" id="price"/> / <input name="post[unit]" type="text" size="2" value="<?php if($unit) { ?><?php echo $unit;?><?php } else { ?>件<?php } ?>" id="unit" placeholder="单位" title="计量单位"/> <span id="dprice" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 商品进价</td>
<td><input name="post[cost]" type="text" size="10" value="<?php echo $cost;?>" id="cost"/> <span id="dcost" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> <?php echo $username ? '库存数量' : '复制次数';?></td>
<td><input name="post[amount]" type="text" size="10" value="<?php echo $amount;?>" id="amount"/> <span id="damount" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 仓储货位</td>
<td><input name="post[location]" type="text" size="20" value="<?php echo $location;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 商品品牌</td>
<td><input name="post[brand]" type="text" size="20" value="<?php echo $brand;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 主要参数</td>
<td class="nv">
<table cellspacing="1" class="ctb">
<tr bgcolor="#F7F7F7" align="center">
<td>参数名称</td>
<td>参数值</td>
</tr>
<tr>
<td><input name="post[n1]" type="text" size="10" value="<?php echo $n1;?>" id="n1"/></td>
<td><input name="post[v1]" type="text" size="20" value="<?php echo $v1;?>" id="v1"/></td>
</tr>
<tr>
<td><input name="post[n2]" type="text" size="10" value="<?php echo $n2;?>" id="n2"/></td>
<td><input name="post[v2]" type="text" size="20" value="<?php echo $v2;?>" id="v2"/></td>
</tr>
<tr>
<td><input name="post[n3]" type="text" size="10" value="<?php echo $n3;?>" id="n3"/></td>
<td><input name="post[v3]" type="text" size="20" value="<?php echo $v3;?>" id="v3"/></td>
</tr>
<tr>
<td class="f_gray">例如：规格</td>
<td class="f_gray">例如：10cm*20cm</td>
</tr>
</table>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 商品介绍</td>
<td><textarea name="post[content]" id="content" class="dsn"><?php echo $content;?></textarea>
<?php echo deditor($moduleid, 'content', 'Destoon', '100%', 350);?><br/><span id="dcontent" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 备注信息</td>
<td><textarea name="post[note]" id="note" style="width:600px;height:36px;"><?php echo $note;?></textarea></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 所属会员</td>
<td><input name="post[username]" type="text" id="username" size="20" value="<?php echo $username;?>"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('username').value);"/> &nbsp; <span id="dusername" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"></td>
<td class="f_gray">如果会员名留空，代表商品数据为公用数据，其他会员可以复制</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 添加时间</td>
<td><?php echo dcalendar('post[addtime]', $addtime, '-', 1);?></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $action == 'edit' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>'+(Dd('username').value ? '' : '&action=open'));"/></div>
</form>
<?php load('clear.js'); ?>
<script type="text/javascript">
function check() {
	var l;
	var f;
	f = 'skuid';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写条形编码', f);
		return false;
	}
	f = 'title';
	l = Dd(f).value.length;
	if(l < 1) {
		Dmsg('请填写商品名称', f);
		return false;
	}
	f = 'thumb';
	l = Dd(f).value.length;
	if(l < 10) {
		Dmsg('请上传商品图片', f);
		return false;
	}
	f = 'price';
	l = Dd(f).value;
	if(l < 0.01) {
		Dmsg('请填写商品售价', f);
		return false;
	}
	return true;
}
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>