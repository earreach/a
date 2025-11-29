<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<?php load('player.js');?>
<?php load('url2video.js');?>
<?php load('webuploader.min.js');?>
<form method="post" action="?" id="dform" onsubmit="return check();">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
<input type="hidden" name="forward" value="<?php echo $forward;?>"/>
<input type="hidden" name="post[mycatid]" value="<?php echo $mycatid;?>"/>
<table cellspacing="0" class="tb">
<?php if($history) { ?>
<tr>
<td class="tl" style="background:#FDE7E7;"><span class="f_red">*</span> 审核提示</td>
<td style="background:#FDE7E7;">该信息存在修改记录，<a href="javascript:;" onclick="Dwidget('?file=history&mid=<?php echo $moduleid;?>&itemid=<?php echo $itemid;?>', '修改详情');" class="t">点击查看</a> 修改详情</td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_red">*</span> 商品分类</td>
<td><div id="catesch"></div><?php echo ajax_category_select('post[catid]', '选择分类', $catid, $moduleid);?> &nbsp; <a href="javascript:schcate(<?php echo $moduleid;?>);"><img src="<?php echo DT_STATIC;?>image/ico-sch.png" width="16" height="16" title="搜索分类"/></a> <span id="dcatid" class="f_red"></span></td>
</tr>
<?php if($CP) { ?>
<script type="text/javascript">
var property_catid = <?php echo $catid;?>;
var property_itemid = <?php echo $itemid;?>;
var property_admin = 1;
</script>
<?php load('property.js');?>
<tbody id="load_property" style="display:none;">
<tr><td></td><td></td></tr>
</tbody>
<?php } ?>
<tr>
<td class="tl"><span class="f_red">*</span> 商品名称</td>
<td><input name="post[title]" type="text" id="title" size="70" value="<?php echo $title;?>"/> <?php echo level_select('post[level]', '级别', $level);?> <?php echo dstyle('post[style]', $style);?> <span id="dtitle" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 商品卖点</td>
<td><input name="post[subtitle]" type="text" size="70" value="<?php echo $subtitle;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 卖点链接</td>
<td><input type="text" size="30" name="post[sublink]" value="<?php echo $sublink;?>"/> &nbsp; 链接文字 &nbsp; <input type="text" size="20" name="post[subtext]" value="<?php echo $subtext;?>"/></td>
</tr>
<tr id="tr_skuid">
<td class="tl"><span class="f_hid">*</span> 商品条码</td>
<td><input name="post[skuid]" type="text" size="30" value="<?php echo $skuid;?>" id="skuid"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-sort.png" width="11" height="11" title="选择商品" class="c_p" onclick="stock_skuid();"/> <span id="dskuid" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 商品品牌</td>
<td><input name="post[brand]" type="text" size="30" value="<?php echo $brand;?>" id="brand"/></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 价格模式</td>
<td>
<label><input type="radio" name="post[mode]" value="0"<?php if($mode==0) { ?> checked<?php } ?> id="mode_0" onclick="Dmode(0);"/> 单售价</label>
&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[mode]" value="1"<?php if($mode==1) { ?> checked<?php } ?> id="mode_1" onclick="Dmode(1);"/> 多售价</label>
&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[mode]" value="2"<?php if($mode==2) { ?> checked<?php } ?> id="mode_2" onclick="Dmode(2);"/> 阶梯价（混批）</label>
&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[mode]" value="3"<?php if($mode==3) { ?> checked<?php } ?> id="mode_3" onclick="Dmode(3);"/> 属性价</label>
</td>
</tr>
<tbody id="tr_price">
<tr>
<td class="tl"><span class="f_red">*</span> 商品单价</td>
<td><input name="post[price]" type="text" size="10" value="<?php echo $price;?>" id="price"/> <span id="dprice" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 粉丝价</td>
<td><input name="post[fprice]" type="text" size="10" value="<?php echo $fprice;?>" id="fprice"/> <span id="dfprice" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 秒杀价</td>
<td><input name="post[sprice]" type="text" size="10" value="<?php echo $sprice;?>" id="sprice"/> &nbsp; <?php echo dcalendar('post[sfromtime]', $sfromtime, '-', 1);?> 至 <?php echo dcalendar('post[stotime]', $stotime, '-', 1);?> <span id="dsprice" class="f_red"></span></td>
</tr>
</tbody>
<tr id="tr_step">
<td class="tl"><span class="f_red">*</span> 商品价格</td>
<td>
<table cellspacing="1" bgcolor="#E7E7EB" class="ctb">
<tr bgcolor="#F5F5F5" align="center">
<td width="90">数量</td>
<td width="90">价格</td>
<td width="90"></td>
<td width="120">数量</td>
<td width="120">价格</td>
</tr>
<tr bgcolor="#FFFFFF" align="center">
<td><input name="post[step][a1]" type="text" size="10" value="<?php echo $a1;?>" id="a1"/></td>
<td><input name="post[step][p1]" type="text" size="10" value="<?php echo $p1;?>" id="p1"/></td>
<td></td>
<td id="p_a_1"></td>
<td id="p_p_1"></td>
</tr>
<tr bgcolor="#FFFFFF" align="center">
<td><input name="post[step][a2]" type="text" size="10" value="<?php echo $a2;?>" id="a2"/></td>
<td><input name="post[step][p2]" type="text" size="10" value="<?php echo $p2;?>" id="p2"/></td>
<td class="c_p f_gray" title="点击观看" onclick="if(confirm('示例数据将覆盖当前数据，确定继续吗？')){Dd('a1').value=1;Dd('p1').value='1000.00';Dd('a2').value=100;Dd('p2').value='900.00';Dd('a3').value=500;Dd('p3').value='800.00';Dstep();}">填写示例</td>
<td id="p_a_2"></td>
<td id="p_p_2"></td>
</tr>
<tr bgcolor="#FFFFFF" align="center">
<td><input name="post[step][a3]" type="text" size="10" value="<?php echo $a3;?>" id="a3"/></td>
<td><input name="post[step][p3]" type="text" size="10" value="<?php echo $p3;?>" id="p3"/></td>
<td></td>
<td id="p_a_3"></td>
<td id="p_p_3"></td>
</tr>
</table>
<span id="dstep" class="f_red"></span>
</td>
</tr>
<tr id="tr_amount">
<td class="tl"><span class="f_red">*</span> 商品库存</td>
<td><input name="post[amount]" type="text" size="10" value="<?php echo $amount;?>" id="amount"/> &nbsp; <input name="post[unit]" type="text" size="2" value="<?php echo $unit;?>" id="unit" title="计量单位"/> <span id="damount" class="f_red"></span></td>
</tr>
<tr id="tr_nv">
<td class="tl"><span class="f_hid">*</span> 商品属性</td>
<td>
<table cellspacing="1" bgcolor="#E7E7EB" class="ctb">
<tr bgcolor="#F5F5F5" align="center">
<td>属性名称</td>
<td>属性值</td>
<td data-prices="1"><span class="f_red">*</span> 价格</td>
</tr>
<tr bgcolor="#FFFFFF" align="center">
<td><input name="post[n1]" type="text" size="10" value="<?php echo $n1;?>" id="n1"/></td>
<td><input name="post[v1]" type="text" size="40" value="<?php echo $v1;?>" id="v1" onblur="Dmuti();"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-list.png" width="11" height="11" title="列表填写" class="c_p" onclick="Nlist(1);"/>
<div id="l1" class="lnv"></td>
<td data-prices="1"><input name="post[prices]" type="text" size="40" value="<?php echo $prices;?>" id="prices"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-list.png" width="11" height="11" title="列表填写" class="c_p" onclick="Nlist(1);"/>
<div id="lp" class="lnv"></td>
</tr>
<tr bgcolor="#FFFFFF" align="center">
<td><input name="post[n2]" type="text" size="10" value="<?php echo $n2;?>" id="n2"/></td>
<td><input name="post[v2]" type="text" size="40" value="<?php echo $v2;?>" id="v2" onblur="Dmuti();"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-list.png" width="11" height="11" title="列表填写" class="c_p" onclick="Nlist(2);"/>
<div id="l2" class="lnv"></td>
<td data-prices="1"></td>
</tr>
<tr bgcolor="#FFFFFF" align="center">
<td><input name="post[n3]" type="text" size="10" value="<?php echo $n3;?>" id="n3"/></td>
<td><input name="post[v3]" type="text" size="40" value="<?php echo $v3;?>" id="v3" onblur="Dmuti();"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-list.png" width="11" height="11" title="列表填写" class="c_p" onclick="Nlist(3);"/>
<div id="l3" class="lnv"></td>
<td data-prices="1"></td>
</tr>
<tr bgcolor="#FFFFFF" align="center">
<td class="f_gray"><span id="egn">例如：<span class="c_p" onclick="if(Dd('n1').value.length<1){Dd('n1').value=this.innerHTML;}">颜色</span></span></td>
<td class="f_gray"><span id="egv">例如：<span class="c_p" onclick="if(Dd('v1').value.length<1){Dd('v1').value=this.innerHTML;}">黑色|白色|红色|蓝色</span> 多个属性用|分隔</span></td>
<td class="f_gray" data-prices="1"><span id="egp">例如：<span class="c_p" onclick="if(Dd('prices').value.length<1){Dd('prices').value=this.innerHTML;}">100.00|200.00|300.00|400.00</span> 多个价格用|分隔</span></td>
</tr>
</table>
<span id="dnv" class="f_red"></span>
</td>
</tr>
<tr id="tr_muti">
<td class="tl"><span class="f_red">*</span> 销售属性</td>
<td id="tb_muti"><span class="jt" onclick="Dmuti();">点击设置</span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 起订量</td>
<td><input type="text" size="10" name="post[minamount]" value="<?php echo $minamount;?>"/> &nbsp; &nbsp; 限购 &nbsp; <input type="text" size="10" name="post[maxamount]" value="<?php echo $maxamount;?>"/> &nbsp; &nbsp; 发货 &nbsp; <input type="text" size="3" name="post[days]" value="<?php echo $days;?>"/> 天内</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 商品图片</td>
<td><?php include template('upload-album', 'chip');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 宣传视频</td>
<td><?php include template('upload-video', 'chip');?></td>
</tr>
<?php echo $FD ? fields_html('<td class="tl">', '<td>', $item) : '';?>
<tr>
<td class="tl"><span class="f_red">*</span> 商品详情</td>
<td><textarea name="post[content]" id="content" class="dsn"><?php echo $content;?></textarea>
<?php echo deditor($moduleid, 'content', $MOD['editor'], '98%', 350);?><br/><span id="dcontent" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 运费设置</td>
<td>
<table cellspacing="1" bgcolor="#E7E7EB" class="ctb">
<tr bgcolor="#F5F5F5" align="center">
<td>快递</td>
<td>默认运费</td>
<td>增加一件商品增加</td>
<td>选择模板 | <a href="<?php echo $MODULE[2]['linkurl'];?>express<?php echo DT_EXT;?>?mid=<?php echo $moduleid;?>" class="t" target="_blank">管理模板</a></td>
</tr>
<tr bgcolor="#FFFFFF" align="center">
<td><input name="post[express_name_1]" type="text" id="express_name_1" size="10" value="<?php echo $express_name_1;?>" /></td>
<td><input name="post[fee_start_1]" type="text" id="fee_start_1" size="5" value="<?php echo $fee_start_1;?>" /></td>
<td><input name="post[fee_step_1]" type="text" id="fee_step_1" size="5" value="<?php echo $fee_step_1;?>" /></td>
<td>
<select name="post[express_1]" id="express_1" onchange="Dexpress(1, this.options[selectedIndex].innerHTML);">
<option value="0">选择模板</option>
<?php if(is_array($EXP)) { foreach($EXP as $v) { ?>
<option value="<?php echo $v['itemid'];?>"<?php if($express_1==$v['itemid']) { ?> selected<?php } ?>
><?php echo $v['title'];?>[<?php echo $v['express'];?>,<?php echo $v['fee_start'];?>,<?php echo $v['fee_step'];?>,<?php echo $v['note'];?>]</option>
<?php } } ?>
</select>
</td>
</tr>
<tr bgcolor="#FFFFFF" align="center">
<td><input name="post[express_name_2]" type="text" id="express_name_2" size="10" value="<?php echo $express_name_2;?>" /></td>
<td><input name="post[fee_start_2]" type="text" id="fee_start_2" size="5" value="<?php echo $fee_start_2;?>" /></td>
<td><input name="post[fee_step_2]" type="text" id="fee_step_2" size="5" value="<?php echo $fee_step_2;?>" /></td>
<td>
<select name="post[express_2]" id="express_2" onchange="Dexpress(2, this.options[selectedIndex].innerHTML);">
<option value="0">选择模板</option>
<?php if(is_array($EXP)) { foreach($EXP as $v) { ?>
<option value="<?php echo $v['itemid'];?>"<?php if($express_2==$v['itemid']) { ?> selected<?php } ?>
><?php echo $v['title'];?>[<?php echo $v['express'];?>,<?php echo $v['fee_start'];?>,<?php echo $v['fee_step'];?>,<?php echo $v['note'];?>]</option>
<?php } } ?>
</select>
</td>
</tr>
<tr bgcolor="#FFFFFF" align="center">
<td><input name="post[express_name_3]" type="text" id="express_name_3" size="10" value="<?php echo $express_name_3;?>" /></td>
<td><input name="post[fee_start_3]" type="text" id="fee_start_3" size="5" value="<?php echo $fee_start_3;?>" /></td>
<td><input name="post[fee_step_3]" type="text" id="fee_step_3" size="5" value="<?php echo $fee_step_3;?>" /></td>
<td>
<select name="post[express_3]" id="express_3" onchange="Dexpress(3, this.options[selectedIndex].innerHTML);">
<option value="0">选择模板</option>
<?php if(is_array($EXP)) { foreach($EXP as $v) { ?>
<option value="<?php echo $v['itemid'];?>"<?php if($express_3==$v['itemid']) { ?> selected<?php } ?>
><?php echo $v['title'];?>[<?php echo $v['express'];?>,<?php echo $v['fee_start'];?>,<?php echo $v['fee_step'];?>,<?php echo $v['note'];?>]</option>
<?php } } ?>
</select>
</td>
</tr>
</table>
<span class="f_gray">&nbsp;填写示例：<span class="c_p" title="点击观看" onclick="Nexpress('0.00', '包邮');">包邮</span> / <span class="c_p" title="点击观看" onclick="Nexpress('500.00', '包邮');">满500包邮</span> / <span class="c_p" title="点击观看" onclick="Nexpress('10.00', '快递');">快递10元</span> / <span class="c_p" title="点击观看" onclick="Nexpress('500.00', '包邮');Dd('express_name_2').value = '快递';Dd('fee_start_2').value = '10.00';">快递10元，满500包邮</span></span> <span id="dexpress" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 货到付款</td>
<td>
<select name="post[cod]" id="cod">
<option value="0"<?php if($cod == 0) echo ' selected';?>>不支持货到付款</option>
<option value="1"<?php if($cod == 1) echo ' selected';?>>支持货到付款，不支持在线支付</option>
<option value="2"<?php if($cod == 2) echo ' selected';?>>支持货到付款，支持在线支付</option>
</select>
</td>
</tr>
<?php if($MOD['edit_areaid']) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 所在地区</td>
<td><?php echo ajax_area_select('post[areaid]', '请选择', $areaid);?> <span id="dareaid" class="f_red"></span></td>
</tr>
<?php } ?>
<tr>
<td class="tl"><span class="f_red">*</span> 会员名</td>
<td><input name="post[username]" type="text"  size="20" value="<?php echo $username;?>" id="username"/> &nbsp; <img src="<?php echo DT_STATIC;?>image/ico-user.png" width="16" height="16" title="会员资料" class="c_p" onclick="_user(Dd('username').value);"/> &nbsp; <span id="dusername" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 会员推荐产品</td>
<td>
<label><input type="radio" name="post[elite]" value="1" <?php if($elite == 1) echo 'checked';?>/> 是</label>&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="post[elite]" value="0" <?php if($elite == 0) echo 'checked';?>/> 否</label>
</td>
</tr>

<tr>
<td class="tl"><span class="f_hid">*</span> 信息状态</td>
<td>
<label><input type="radio" name="post[status]" value="3" <?php if($status == 3) echo 'checked';?>/> 通过</label>
<label><input type="radio" name="post[status]" value="2" <?php if($status == 2) echo 'checked';?>/> 待审</label>
<label><input type="radio" name="post[status]" value="1" <?php if($status == 1) echo 'checked';?> onclick="if(this.checked) Dd('note').style.display='';"/> 拒绝</label>
<label><input type="radio" name="post[status]" value="4" <?php if($status == 4) echo 'checked';?>/> 下架</label>
<label><input type="radio" name="post[status]" value="0" <?php if($status == 0) echo 'checked';?>/> 删除</label>
</td>
</tr>
<tr id="note" style="display:<?php echo $status==1 ? '' : 'none';?>">
<td class="tl"><span class="f_red">*</span> 拒绝理由</td>
<td><input name="post[note]" type="text"  size="40" value="<?php echo $note;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 添加时间</td>
<td><?php echo dcalendar('post[addtime]', $addtime, '-', 1);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 浏览次数</td>
<td><input name="post[hits]" type="text" size="10" value="<?php echo $hits;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 内容收费</td>
<td><input name="post[fee]" type="text" size="10" value="<?php echo $fee;?>"/><?php tips('不填或填0表示继承模块设置价格，-1表示不收费<br/>大于0的数字表示具体收费价格');?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 内容模板</td>
<td><?php echo tpl_select('show', $module, 'post[template]', '默认模板', $template, 'id="template"');?><?php tips('如果没有特殊需要，一般不需要选择<br/>系统会自动继承分类或模块设置');?></td>
</tr>
<?php if($MOD['show_html']) { ?>
<tr>
<td class="tl"><span class="f_hid">*</span> 自定义文件路径</td>
<td><input type="text" size="70" name="post[filepath]" value="<?php echo $filepath;?>" id="filepath"/>&nbsp;<input type="button" value="重名检测" onclick="ckpath(<?php echo $moduleid;?>, <?php echo $itemid;?>);" class="btn"/>&nbsp;<?php tips('可以包含目录和文件 例如 destoon/about.html<br/>请确保目录和文件名合法且可写入，否则可能生成失败');?>&nbsp; <span id="dfilepath" class="f_red"></span></td>
</tr>
<?php } ?>
</table>
<div class="sbt"><input type="submit" name="submit" value="<?php echo $action == 'edit' ? '修 改' : '添 加';?>" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo $action == 'edit' ? '返 回' : '取 消';?>" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/></div>
</form>
<?php load('clear.js'); ?>
<?php if($action == 'add' && in_array($moduleid, explode(',', $DT['fetch_module']))) { ?>
<form method="post" action="?">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<div class="tt">单页采编</div>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 目标网址</td>
<td><input name="url" type="text" size="80" value="<?php echo $url;?>"/>&nbsp;&nbsp;<input type="submit" value=" 获 取 " class="btn"/>&nbsp;&nbsp;<input type="button" value=" 管理规则 " class="btn" onclick="Dwidget('?file=fetch', '管理规则');"/></td>
</tr>
</table>
</form>
<?php } ?>
<script type="text/javascript">
var stocks = <?php if($stocks) { ?>$.parseJSON('<?php echo $stocks;?>')<?php } else { ?>[]<?php } ?>;
function _p() {
	if(Dd('tag').value) {
		Ds('reccate');
	}
}
function check() {
	var l;
	var f;
	f = 'catid_1';
	if(Dd(f).value == 0) {
		Dmsg('请选择商品分类', 'catid', 1);
		return false;
	}
	f = 'title';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('商品名称最少2字，当前已输入'+l+'字', f);
		return false;
	}
	f = 'amount';
	l = Dd(f).value;
	if(l < 1) {
		Dmsg('请填写库存', f);
		return false;
	}
	if(Dd('mode_0').checked) {
		var price = parseFloat(Dd('price').value);
		if(price < 0.01) {
			Dmsg('请填写商品单价', 'price');
			return false;
		}
		var fprice = parseFloat(Dd('fprice').value);
		if(fprice >= 0.01 && fprice >= price) {
			Dmsg('粉丝价不能高于单价', 'fprice');
			return false;
		}
		var sprice = parseFloat(Dd('sprice').value);
		if(sprice >= 0.01 && sprice >= price) {
			Dmsg('秒杀价不能高于单价', 'sprice');
			return false;
		}
		if(sprice >= 0.01 && Dd('postsfromtime').value.length != 19) {
			Dmsg('请设置秒杀开始时间', 'sprice');
			return false;
		}
		if(sprice >= 0.01 && Dd('poststotime').value.length != 19) {
			Dmsg('请设置秒杀结束时间', 'sprice');
			return false;
		}
		$('#p1').val(price);
	} else if(Dd('mode_1').checked) {
		var prices = $('#prices').val();
		if(prices.length < 1) {
			Dmsg('请填写'+Dd('n1').value+'对应价格', 'nv', 1);
			return false;
		}
		if(prices.indexOf('|') == -1) {
			$('#price').val(prices);
			$('#p1').val(prices);
		} else {
			var tmp = prices.split('|');
			$('#price').val(tmp[0]);
			$('#p1').val(tmp[0]);
		}
	} else if(Dd('mode_2').checked) {
		if(!Dstep()) return false;
	} else if(Dd('mode_3').checked) {
		if($('#tb_muti').html().indexOf('<td>') == -1) {
			Dmuti();
			Dmsg('第一行库存商品必须选择', 'nv', 1);
			return false;
		} else {
			if($('#stockid-0-0-0').val() < 1) {
				Dmsg('第一行库存商品必须选择', 'nv', 1);
				return false;
			}
		}
	}
	f = 'thumb0';
	l = Dd(f).value.length;
	if(l < 5) {
		Dmsg('请上传第一张商品图片', f, 1);
		return false;
	}
	f = 'content';
	l = EditorLen();
	if(l < 5) {
		Dmsg('详细说明最少5字，当前已输入'+l+'字', f);
		return false;
	}
	f = 'username';
	l = Dd(f).value.length;
	if(l < 2) {
		Dmsg('请填写会员名', f);
		return false;
	}
	if(Dd('v1').value) {
		if(!Dd('n1').value) {
			Dmsg('请填写属性名称', 'nv');
			Dd('n1').focus();
			return false;
		}
		if(Dd('v1').value.indexOf('|') == -1) {
			Dmsg(Dd('n1').value+'至少需要两个属性', 'nv');
			Dd('v1').focus();
			return false;
		}
	}
	if(Dd('v2').value) {
		if(!Dd('n2').value) {
			Dmsg('请填写属性名称');
			Dd('n2').focus();
			return false;
		}
		if(Dd('v2').value.indexOf('|') == -1) {
			Dmsg(Dd('n2').value+'至少需要两个属性', 'nv');
			Dd('v2').focus();
			return false;
		}
	}
	if(Dd('v3').value) {
		if(!Dd('n3').value) {
			Dmsg('请填写属性名称', 'nv');
			Dd('n3').focus();
			return false;
		}
		if(Dd('v3').value.indexOf('|') == -1) {
			Dmsg(Dd('n3').value+'至少需要两个属性', 'nv');
			Dd('v3').focus();
			return false;
		}
	}
	if(Dd('n1').value && (Dd('n1').value == Dd('n2').value || Dd('n1').value == Dd('n3').value)) {
		Dmsg('属性名称不能重复', 'nv');
		return false;
	}
	if(Dd('n2').value && (Dd('n2').value == Dd('n1').value || Dd('n2').value == Dd('n3').value)) {
		Dmsg('属性名称不能重复', 'nv');
		return false;
	}
	if(Dd('n3').value && (Dd('n3').value == Dd('n1').value || Dd('n3').value == Dd('n2').value)) {
		Dmsg('属性名称不能重复', 'nv');
		return false;
	}
	if(Dd('express_name_1').value && (Dd('express_name_1').value == Dd('express_name_2').value || Dd('express_name_1').value == Dd('express_name_3').value)) {
		Dmsg('快递名称不能重复', 'express');
		return false;
	}
	if(Dd('express_name_2').value && (Dd('express_name_2').value == Dd('express_name_1').value || Dd('express_name_2').value == Dd('express_name_3').value)) {
		Dmsg('快递名称不能重复', 'express');
		return false;
	}
	if(Dd('express_name_3').value && (Dd('express_name_3').value == Dd('express_name_1').value || Dd('express_name_3').value == Dd('express_name_2').value)) {
		Dmsg('快递名称不能重复', 'express');
		return false;
	}	
	<?php echo $FD ? fields_js() : '';?>
	<?php echo $CP ? property_js() : '';?>
	return true;
}
function Dexpress(i, s) {
	if(Dd('express_'+i).value > 0) {
		var t1 = s.split('[');
		var t2 = t1[1].split(',');
		Dd('express_name_'+i).value = t2[0];
		Dd('fee_start_'+i).value = t2[1];
		Dd('fee_step_'+i).value = t2[2];
	} else {
		Dd('express_name_'+i).value = '';
		Dd('fee_start_'+i).value = '';
		Dd('fee_step_'+i).value = '';
	}
}

function Nexpress(i, s) {
	Dd('express_name_1').value = s;
	Dd('fee_start_1').value = i;
	Dd('fee_step_1').value = '0.00';
	$('#express_1').val(0);
	Dd('express_name_2').value = '';
	Dd('fee_start_2').value = '0.00';
	Dd('fee_step_2').value = '0.00';
	$('#express_2').val(0);
	Dd('express_name_3').value = '';
	Dd('fee_start_3').value = '0.00';
	Dd('fee_step_3').value = '0.00';
	$('#express_3').val(0);
}
function Dstep() {
	Dd('p_a_1').innerHTML=Dd('p_p_1').innerHTML=Dd('p_a_2').innerHTML=Dd('p_p_2').innerHTML=Dd('p_a_3').innerHTML=Dd('p_p_3').innerHTML='';
	var a1 = parseInt(Dd('a1').value);
	var p1 = parseFloat(Dd('p1').value);
	var a2 = parseInt(Dd('a2').value);
	var p2 = parseFloat(Dd('p2').value);
	var a3 = parseInt(Dd('a3').value);
	var p3 = parseFloat(Dd('p3').value);
	var u = Dd('unit').value;
	if(u.length < 1) Dd('unit').value = u = '件';
	var m = '<?php echo $DT['money_unit'];?>';
	if(!a1 || a1 < 1) {
		Dmsg('起订量必须大于0', 'step');
		Dd('a1').value = '1';
		//Dd('a1').focus();
		return false;
	}
	if(!p1 || p1 < 0.01) {
		Dmsg('请填写商品价格', 'step');
		Dd('p1').value = '';
		//Dd('p1').focus();
		return false;
	}
	Dd('p_a_1').innerHTML = a1+u+'以上';
	Dd('p_p_1').innerHTML = p1+m+'/'+u;
	if(a2 > 1 && p2 > 0.01) {
		if(a2 <= a1) {
			Dmsg('数量必须大于'+a1, 'step');
			Dd('a2').value = '';
			//Dd('a2').focus();
			return false;
		}
		if(p2 >= p1) {
			Dmsg('价格必须小于'+p1, 'step');
			Dd('p2').value = '';
			//Dd('p2').focus();
			return false;
		}
		Dd('p_a_1').innerHTML = a1+'-'+a2+u;
		Dd('p_p_1').innerHTML = p1+m+'/'+u;
		Dd('p_a_2').innerHTML = '>'+a2+u;
		Dd('p_p_2').innerHTML = p2+m+'/'+u;
	}
	if(a3 > 1 && p3 > 0.01) {
		if(a3 <= a2) {
			Dmsg('数量必须大于'+a2, 'step');
			Dd('a3').value = '';
			//Dd('a3').focus();
			return false;
		}
		if(p3 >= p2) {
			Dmsg('价格必须小于'+p2, 'step');
			Dd('p3').value = '';
			//Dd('p3').focus();
			return false;
		}
		Dd('p_a_2').innerHTML = (a2+1)+'-'+a3+u;
		Dd('p_p_2').innerHTML = p2+m+'/'+u;
		Dd('p_a_3').innerHTML = '>'+a3+u;
		Dd('p_p_3').innerHTML = p3+m+'/'+u;
	}
	return true;
}
function Dmode(k) {
	if(k == 3) {
		$('#tr_step').hide();$('#tr_price').hide();$('#tr_amount').hide();$('#tr_skuid').hide();$('#tr_muti').show();$('[data-prices]').hide();
	} else if(k == 2) {
		$('#tr_step').show();$('#tr_price').hide();$('#tr_amount').show();$('#tr_skuid').show();$('#tr_muti').hide();$('[data-prices]').hide();
	} else if(k == 1) {
		$('#tr_step').hide();$('#tr_price').hide();$('#tr_amount').show();$('#tr_skuid').show();$('#tr_muti').hide();$('[data-prices]').show();
	} else {	
		$('#tr_step').hide();$('#tr_price').show();$('#tr_amount').show();$('#tr_skuid').show();$('#tr_muti').hide();$('[data-prices]').hide();
	}
}
function Dmuti() {
	var n1 = Dd('n1').value;
	var n2 = Dd('n2').value;
	var n3 = Dd('n3').value;
	var v1 = Dd('v1').value;
	var v2 = Dd('v2').value;
	var v3 = Dd('v3').value;
	if(n1.length < 1) {
		Dmsg('请填写属性名称', 'nv');
		//Dd('n1').focus();
		return false;
	}
	if(v1.length < 1) {
		Dmsg('请填写'+n1+'属性值', 'nv');
		//Dd('v1').focus();
		return false;
	}
	if(v2) {
		if(n2.length < 1) {
			Dmsg('请填写属性名称');
			//Dd('n2').focus();
			return false;
		}
	}
	if(v3) {
		if(n2.length < 1) {
			Dmsg('请填写属性名称');
			//Dd('n2').focus();
			return false;
		}
		if(v2.length < 1) {
			Dmsg('请填写'+n2+'属性值', 'nv');
			//Dd('v2').focus();
			return false;
		}
		if(n3.length < 1) {
			Dmsg('请填写属性名称', 'nv');
			//Dd('n3').focus();
			return false;
		}
		if(v3.length < 1) {
			Dmsg('请填写'+n3+'属性值', 'nv');
			//Dd('v3').focus();
			return false;
		}
	}
	if((n1 && (n1 == n2 || n1 == n3)) || (n2 && (n2 == n1 || n2 == n3)) || (n3 && (n3 == n1 || n3 == n2))) {
		Dmsg('属性名称不能重复', 'nv');
		return false;
	}
	var a1 = v1 ? v1.split('|') : new Array();
	var a2 = v2 ? v2.split('|') : new Array();
	var a3 = v3 ? v3.split('|') : new Array();
	var htm = '<table cellspacing="1" bgcolor="#E7E7EB" class="ctb">';
	htm += '<tr bgcolor="#F5F5F5" align="center">';
	htm += '<td>&nbsp; 图片 &nbsp;</td>';
	htm += '<td>&nbsp; '+n1+' &nbsp;</td>';
	if(v2) htm += '<td>&nbsp; '+n2+' &nbsp;</td>';
	if(v3) htm += '<td>&nbsp; '+n3+' &nbsp;</td>';
	htm += '<td>&nbsp; <span class="f_red">*</span> 价格 &nbsp;</td>';
	htm += '<td>&nbsp; <span class="f_red">*</span> 库存 &nbsp;</td>';
	htm += '<td>&nbsp; <span class="f_red">*</span> 条形编码 &nbsp;</td>';
	htm += '<td>&nbsp; 选择</td>';
	htm += '</tr>';
	if(v3) {
		for(var i = 0; i < a1.length; i++) {
			for(var j = 0; j < a2.length; j++) {
				for(var k = 0; k < a3.length; k++) {
					var key = i+'-'+j+'-'+k;
					var obj = typeof stocks[key] == 'undefined' ? '' : stocks[key];
					htm += '<tr bgcolor="#FFFFFF" align="center" class="c_p" onclick="stock_select(\''+key+'\');">';
					htm += '<td><img src="'+(obj && obj.thumb ? obj.thumb : '<?php echo DT_STATIC;?>image/nopic50.png')+'" width="50" height="50" id="thumb-'+key+'" title="'+(obj ? obj.title : '')+'"/><input type="hidden" name="post[stock]['+key+']" id="stockid-'+key+'" value="'+(obj ? obj.itemid : '')+'"/></td>';
					htm += '<td>'+a1[i]+'</td>';
					htm += '<td>'+a2[j]+'</td>';
					htm += '<td>'+a3[k]+'</td>';
					htm += '<td class="f_red" id="price-'+key+'">'+(obj ? obj.price : '')+'</td>';
					htm += '<td id="amount-'+key+'">'+(obj ? obj.amount : '')+'</td>';
					htm += '<td id="skuid-'+key+'">'+(obj ? obj.skuid : '')+'</td>';
					htm += '<td><img src="<?php echo DT_STATIC;?>image/ico-sort.png" width="11" height="11" title="从库存中选择商品"/></td>';
					htm += '</tr>';
				}
			}
		}
	} else if(v2) {
		for(var i = 0; i < a1.length; i++) {
			for(var j = 0; j < a2.length; j++) {
				var key = i+'-'+j+'-0';
				var obj = typeof stocks[key] == 'undefined' ? '' : stocks[key];
				htm += '<tr bgcolor="#FFFFFF" align="center" class="c_p" onclick="stock_select(\''+key+'\');">';
				htm += '<td><img src="'+(obj && obj.thumb ? obj.thumb : '<?php echo DT_STATIC;?>image/nopic50.png')+'" width="50" height="50" id="thumb-'+key+'" title="'+(obj ? obj.title : '')+'"/><input type="hidden" name="post[stock]['+key+']" id="stockid-'+key+'" value="'+(obj ? obj.itemid : '')+'"/></td>';
				htm += '<td>'+a1[i]+'</td>';
				htm += '<td>'+a2[j]+'</td>';
				htm += '<td class="f_red" id="price-'+key+'">'+(obj ? obj.price : '')+'</td>';
				htm += '<td id="amount-'+key+'">'+(obj ? obj.amount : '')+'</td>';
				htm += '<td id="skuid-'+key+'">'+(obj ? obj.skuid : '')+'</td>';
				htm += '<td><img src="<?php echo DT_STATIC;?>image/ico-sort.png" width="11" height="11" title="从库存中选择商品"/></td>';
				htm += '</tr>';
			}
		}
	} else {
		for(var i = 0; i < a1.length; i++) {
			var key = i+'-0-0';
			var obj = typeof stocks[key] == 'undefined' ? '' : stocks[key];
			htm += '<tr bgcolor="#FFFFFF" align="center" class="c_p" onclick="stock_select(\''+key+'\');">';
			htm += '<td><img src="'+(obj && obj.thumb ? obj.thumb : '<?php echo DT_STATIC;?>image/nopic50.png')+'" width="50" height="50" id="thumb-'+key+'" title="'+(obj ? obj.title : '')+'"/><input type="hidden" name="post[stock]['+key+']" id="stockid-'+key+'" value="'+(obj ? obj.itemid : '')+'"/></td>';
			htm += '<td>'+a1[i]+'</td>';
			htm += '<td class="f_red" id="price-'+key+'">'+(obj ? obj.price : '')+'</td>';
			htm += '<td id="amount-'+key+'">'+(obj ? obj.amount : '')+'</td>';
			htm += '<td id="skuid-'+key+'">'+(obj ? obj.skuid : '')+'</td>';
			htm += '<td><img src="<?php echo DT_STATIC;?>image/ico-sort.png" width="11" height="11" title="从库存中选择商品"/></td>';
			htm += '</tr>';
		}
	}
	htm += '</table>';
	Dd('tb_muti').innerHTML = htm;
}
function Nlist(id) {
	var p = $('#lp') ? 1 : 0;
	if($('#l'+id).css('display') == 'none') {
		var h = hp = '';
		var v = $('#v'+id).val();
		var x = 6;
		if(v) {
			var a = v.split('|');
			var ap = $('#prices').val().split('|');
			if(a) {
				for(var i = 0; i < a.length; i++) {
					h += '<div><input type="text" size="20" value="'+a[i]+'"/></div>';
					if(id == 1 && p) hp += '<div><input type="text" size="20" value="'+(typeof ap[i] == 'undefined' ? '' : ap[i])+'"/></div>';
				}
				x = 3;
			}
		}
		for(var i = 0; i < x; i++) {
			h += '<div><input type="text" size="20" value=""/></div>';
			if(id == 1 && p) hp += '<div><input type="text" size="20" value=""/></div>';
		}
		$('#v'+id).attr('readonly', 'readonly');
		$('#l'+id).html(h);
		$('#l'+id).slideDown();
		$('#egv').hide();
		if(id == 1 && p) $('#egp').hide();
		$('#l'+id).on('keyup blur paste', 'input', function(e) {
			var v = '';
			var j = 0;
			$('#l'+id+' input').each(function(i) {
				var s = $.trim($(this).val()).replace(/\|/g, '');
				if(s) {
					v += s+'|';
				} else {
					j++;
				}
			});
			$('#v'+id).val(v ? v.substr(0, v.length - 1) : '');
			if(j == 0) $('#l'+id).append('<div><input type="text" size="20" value=""/></div>');
			if(id == 1 && p && j == 0) $('#lp').append('<div><input type="text" size="20" value=""/></div>');
		});
		if(id == 1 && p) {
			$('#prices').attr('readonly', 'readonly');
			$('#lp').html(hp);
			$('#lp').slideDown();
			$('#lp').on('keyup blur paste', 'input', function(e) {
				var vp = '';
				$('#lp input').each(function(i) {
					var sp = $.trim($(this).val()).replace(/\|/g, '');
					if(sp) vp += sp+'|';
				});
				$('#prices').val(vp ? vp.substr(0, vp.length - 1) : '');
			});
		}
	} else {
		$('#v'+id).removeAttr('readonly');
		$('#l'+id).slideUp();
		if(id == 1 && p) {
			$('#prices').removeAttr('readonly');
			$('#lp').slideUp();
		}
		if(!$('#v1').attr('readonly') && !$('#v2').attr('readonly') && !$('#v3').attr('readonly')) {
			$('#egv').show();
			if(id == 1 && p) $('#egp').show();
		}
	}
}
function stock_select(k) {
	if(Dd('username').value != '<?php echo $_username;?>') {
		alert('该商品非帐号 <?php echo $_username;?> 发布，无法进行此操作');
		return;
	}
	Dwidget(AJPath+'?action=choose&mid=<?php echo $moduleid;?>&job=stock&key='+k, '选择库存商品');
}
function stock_skuid() {
	if(Dd('username').value != '<?php echo $_username;?>') {
		alert('该商品非帐号 <?php echo $_username;?> 发布，无法进行此操作');
		return;
	}
	Dwidget(AJPath+'?action=choose&mid={$moduleid}&job=stock&key=skuid&skuid='+Dd('skuid').value, '选择商品条码');
}
function stock_content(itemid) {
	if(EditorLen() < 5) {
		$.post(AJPath, 'action=choose&mid={$moduleid}&job=stock&key=content&itemid='+itemid, function(data) {
			if(data) EditorAPI('content', 'set', data);
		});
	}
}
$(function(){
	Dmode(<?php echo $mode;?>);
	if(Dd('v1').value) {Dmuti();}
	if(Dd('mode_2').checked) {Dstep();}
	$('#tr_step input').on('input blur',function(e){
		if(Dd('mode_2').checked) {
			Dstep();
		}
	});
	$('#tr_nv input').on('input blur',function(e){
		if(Dd('mode_3').checked) {
			Dmuti();
		}
	});
	<?php if($mode == 1 && $prices) { ?>Nlist(1);<?php } ?>
});
</script>
<script type="text/javascript">Menuon(<?php echo $menuid;?>);</script>
<?php include tpl('footer');?>