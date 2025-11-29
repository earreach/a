<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');



show_menu($menus);
?>
<?php if($action == 'add') { ?>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="mid" value="<?php echo $mid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 上级分类</td>
<td><?php echo category_select('category[parentid]', '请选择', $parentid, $mid);?><?php tips('如果不选择，则为顶级分类');?></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 分类名称</td>
<td><textarea name="category[catname]"  id="catname" style="width:200px;height:100px;overflow:visible;" onblur="get_letter(this.value);"></textarea><?php tips('允许批量添加，一行一个，点回车换行');?><br/><span id="dcatname" class="f_red"></span></td>
</tr>
<!--<tr>-->
<!--    <td class="tl"><span class="f_red"></span> 分类目录（可以不填写）</td>-->
<!--<td><textarea name="category[catdir]"  id="catdir" style="width:200px;height:100px;overflow:visible;" ></textarea><?php tips('允许批量添加，一行一个，点回车换行');?><br/><span id="" class="f_red"></span></td>-->
<!--</tr>-->

<tr>
<td class="tl"><span class="f_hid">*</span> 分类目录</td>
<td><input name="category[catdir]" type="text" id="catdir" style="width:200px;" /> &nbsp; <a href="javascript:;"onclick="ckDir();" class="t">校验</a> <?php tips('限[a-z]、[A-z]、[0-9]、_、- 、/<br/>该分类相关的html文件将保存在此目录<br/>如果需要生成多级目录，请用 / 分隔目录<br/>如果不填写则自动将分类id作为目录');?> <span id="dcatdir" class="f_red"></span></td>
</tr>


<!--<tr>-->
<!--<td class="tl"><span class="f_hid">*</span> 分类目录</td>-->
<!--<td><input name="category[catdir]" type="text" id="catdir" style="width:200px;" /> &nbsp; <a href="javascript:;"onclick="ckDir();" class="t">校验</a> <?php tips('限[a-z]、[A-z]、[0-9]、_、- 、/<br/>该分类相关的html文件将保存在此目录<br/>如果需要生成多级目录，请用 / 分隔目录<br/>如果不填写则自动将分类id作为目录');?> <span id="dcatdir" class="f_red"></span></td>-->
<!--</tr>-->


<tr>
<td class="tl"><span class="f_hid">*</span> 分类图片</td>
<td>
<input name="category[thumb]" type="text" id="thumb" size="70" ondblclick="Dthumb(1,128,128, Dd('thumb').value, 0, 'thumb');"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dthumb(1,128,128, Dd('thumb').value, 0, 'thumb');"/>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('thumb').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-del.png" title="删除" onclick="Dd('thumb').value='';"/>
</span>
<?php tips('如果在模板里需要以图片形式展示分类，可以调用此参数(thumb)，建议128x128');?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 分类图标</td>
<td>
<input name="category[icon]" type="text" id="icon" size="70" ondblclick="Dthumb(1,48,48, Dd('icon').value, 0, 'icon');"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dthumb(1,48,48, Dd('icon').value, 0, 'icon');"/>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('icon').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-del.png" title="删除" onclick="Dd('icon').value='';"/>
</span>
<?php tips('如果在模板里需要以图标形式展示分类，可以调用此参数(icon)，建议48x48透明PNG格式');?>
</td>
</tr>
    <tr>
        <td class="tl">设备颜色</td>
        <td class="tr">
            <input name="category[color]" type="text" size="40"
                   value="<?php echo isset($category['color']) ? $category['color'] : ''; ?>"/>
            <span class="f_gray">
      多个颜色用逗号隔开，例如：黑色,白色,红色
    </span>
        </td>
    </tr>

<tr>
  <td class="tl">设备颜色</td>
  <td class="tr">
    <input name="category[color]" type="text" size="40"
           value="<?php echo $color;?>"/>
    <span class="f_gray">
      多个颜色用逗号隔开，例如：黑色,白色,红色
    </span>
  </td>
</tr>

<tr>
<td class="tl"><span class="f_hid">*</span> 字母索引</td>
<td><input name="category[letter]" type="text" id="letter" size="2" /><?php tips('填写分类名称后系统会自动获取 如果没有获取成功请填写<br/>例如 分类名称为 嘉客 则填写 j');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 级别</td>
<td><input name="category[level]" type="text" size="2" value="1"/><?php tips('0 - 不在首页显示 1 - 正常显示 2 - 首页和上级分类并列显示');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 分类模板</td>
<td><?php echo tpl_select('list', $MODULE[$mid]['module'], 'category[template]', '默认模板');?></td>
</tr>
<tr style="display:<?php echo $MODULE[$mid]['module'] == 'club' ? 'none' : '';?>;">
<td class="tl"><span class="f_hid">*</span> 内容模板</td>
<td><?php echo tpl_select('show', $MODULE[$mid]['module'], 'category[show_template]', '默认模板');?></td>
</tr>
    <tr>
        <td class="tl"><span class="f_hid">*</span> Title(SEO标题)</td>
        <td><input name="category[seo_title]" type="text" size="61"></td>
    </tr>

<tr>
<td class="tl"><span class="f_hid">*</span> Title(SEO标题)</td>
<td><input name="category[seo_title]" type="text" size="61"></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> Meta Keywords<br/>&nbsp; (网页关键词)</td>
<td><textarea name="category[seo_keywords]" cols="60" rows="3" id="seo_keywords"></textarea></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> Meta Description<br/>&nbsp; (网页描述)</td>
<td><textarea name="category[seo_description]" cols="60" rows="3" id="seo_description"></textarea></td>
</tr>

<tr>
    <td class="tl"><span class="f_red">*</span> 分类介绍内容</td>
    <td><textarea name="post[content]" id="content" class="dsn"></textarea>
        <?php echo deditor($mid, 'content', $MOD['editor'], '98%', 350);?><br/><span id="dcontent" class="f_red"></span>
    </td>
    </tr>
<tr>
        <td class="tl" height="30"><span class="f_hid">*</span> 111111111内容选1111项</td>
        <td>
        <a href="javascript:pagebreak();Ds('subtitle');"><img src="<?php echo DT_STATIC;?>admin/pagebreak.png" align="absmiddle"/> 插入分页符</a>&nbsp; &nbsp;
        <input type="checkbox" name="post[save_remotepic]" value="1"<?php if($MOD['save_remotepic']) echo 'checked';?>/> 下载远程图片&nbsp; &nbsp;
        <input type="checkbox" name="post[clear_link]" value="1"<?php if($MOD['clear_link']) echo 'checked';?>/> 清除链接&nbsp; &nbsp;
        截取内容 <input name="post[introduce_length]" type="text" size="2" value="<?php echo $MOD['introduce_length']?>"/> 字符至简介&nbsp; &nbsp;
        设置内容第 <input name="post[thumb_no]" type="text" size="2" value="1"/> 张图片为标题图&nbsp; &nbsp;
        插入投票 <input name="post[voteid]" type="text" size="10" value="<?php echo $voteid;?>"/> <a href="javascript:Dwidget('?moduleid=3&file=vote', '投票列表');" class="t">[查看]</a> <?php tips('请填写投票ID，多个ID请用空格隔开');?>
        </td>
</tr>

<tr>
<td class="tl"><span class="f_hid">*</span> 权限设置</td>
<td class="f_blue">如果没有特殊需要，以下选项不需要设置，全选或全不选均代表拥有对应权限</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 允许浏览分类</td>
<td><?php echo group_checkbox('category[group_list][]');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 允许浏览分类信息内容</td>
<td><?php echo group_checkbox('category[group_show][]');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 允许发布信息</td>
<td><?php echo group_checkbox('category[group_add][]');?></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="确 定" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="取 消" class="btn" onclick="Go('?mid=<?php echo $mid;?>&file=<?php echo $file;?>');"/></div>
</form>
<script type="text/javascript">
function ckDir() {
	if(Dd('catdir').value == '') {
		Dtip('请填写分类目录');
		Dd('catdir').focus();
		return false;
	}
	var url = '?file=category&action=ckdir&mid=<?php echo $mid;?>&catdir='+Dd('catdir').value;
	Diframe(url, 0, 0, 1);
}
function check() {
	if(Dd('catname').value == '') {
		Dmsg('请填写分类名称', 'catname');
		return false;
	}
	return true;
}
function get_letter(catname) {
	$.get('?file=<?php echo $file;?>&mid=<?php echo $mid;?>&action=letter&catname='+catname, function(data) {
		if(Dd('catdir').value == '') Dd('catdir').value = data;
		if(Dd('letter').value == '') Dd('letter').value = data.substr(0, 1);
	});
}
</script>
<script type="text/javascript">Menuon(0);</script>
<?php } else { ?>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="mid" value="<?php echo $mid;?>"/>
<input type="hidden" name="catid" value="<?php echo $catid;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_hid">*</span> 上级分类</td>
<td><?php echo category_select('category[parentid]', '请选择', $parentid, $mid);?><?php tips('如果不选择，则为顶级分类');?></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 分类名称</td>
<td><input name="category[catname]" type="text" id="catname" size="20" value="<?php echo $catname;?>"/> <?php echo dstyle('category[style]', $style);?> <span id="dcatname" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 分类目录</td>
<td><input name="category[catdir]" type="text" id="catdir" size="20" value="<?php echo $catdir;?>"/><?php tips('限英文、数字、中划线、下划线、斜线，该分类相关的html文件将保存在此目录');?> <span id="dcatdir" class="f_red"></span></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 分类图片</td>
<td>
<input name="category[thumb]" type="text" value="<?php echo $thumb;?>" id="thumb" size="70" ondblclick="Dthumb(1,128,128, Dd('thumb').value, 0, 'thumb');"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dthumb(1,128,128, Dd('thumb').value, 0, 'thumb');"/>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('thumb').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-del.png" title="删除" onclick="Dd('thumb').value='';"/>
</span>
<?php tips('如果在模板里需要以图片形式展示分类，可以调用此参数(thumb)，建议128x128');?>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 分类图标</td>
<td>
<input name="category[icon]" type="text" value="<?php echo $icon;?>" id="icon" size="70" ondblclick="Dthumb(1,48,48, Dd('icon').value, 0, 'icon');"/>
<span class="upl">
<img src="<?php echo DT_STATIC;?>image/ico-upl.png" title="上传" onclick="Dthumb(1,48,48, Dd('icon').value, 0, 'icon');"/>
<img src="<?php echo DT_STATIC;?>image/ico-view.png" title="预览" onclick="_preview(Dd('icon').value);"/>
<img src="<?php echo DT_STATIC;?>image/ico-del.png" title="删除" onclick="Dd('icon').value='';"/>
</span>
<?php tips('如果在模板里需要以图标形式展示分类，可以调用此参数(icon)，建议48x48透明PNG格式');?>
</td>
</tr>
<tr>
    <tr>
        <td class="tl">设备颜色</td>
        <td class="tr">
            <input name="category[color]" type="text" size="40"
                   value="<?php echo $color;?>"/>
            <span class="f_gray">
      多个颜色用逗号隔开，例如：黑色,白色,红色
    </span>
        </td>
    </tr>

    <td class="tl"><span class="f_hid">*</span> 字母索引</td>
<td><input name="category[letter]" type="text" id="letter" size="2" value="<?php echo $letter;?>"/></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 级别</td>
<td><input name="category[level]" type="text" size="2" value="<?php echo $level;?>"/><?php tips('0 - 不在首页显示 1 - 正常显示 2 - 首页和上级分类并列显示');?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 分类模板</td>
<td><?php echo tpl_select('list', $MODULE[$mid]['module'], 'category[template]', '默认模板', $template);?></td>
</tr>
<tr style="display:<?php echo $MODULE[$mid]['module'] == 'club' ? 'none' : '';?>;">
<td class="tl"><span class="f_hid">*</span> 内容模板</td>
<td><?php echo tpl_select('show', $MODULE[$mid]['module'], 'category[show_template]', '默认模板', $show_template);?></td>
</tr>




<?// var_dump($fua);die(); ?>







    <tr>
        <td class="tl"><span class="f_hid">*</span> 附加信息</td>
        <td>
            <div>
                <input type="text" name="category[fua][0][num]" value="<?php echo $fua[0]['num'];?>"/>
                <input type="text" name="category[fua][0][ming]" value="<?php echo $fua[0]['ming'];?>"/>
                <input type="text" name="category[fua][0][bi]"  value="<?php echo $fua[0]['bi'];?>"/>
                <br>
                <input type="text" name="category[fua][1][num]"  value="<?php echo $fua[1]['num'];?>"/>
                <input type="text" name="category[fua][1][ming]" value="<?php echo $fua[1]['ming'];?>"/>
                <input type="text" name="category[fua][1][bi]"  value="<?php echo $fua[1]['bi'];?>"/>
                <br>
                <input type="text" name="category[fua][2][num]" value="<?php echo $fua[2]['num'];?>"/>
                <input type="text" name="category[fua][2][ming]"  value="<?php echo $fua[2]['ming'];?>"/>
                <input type="text" name="category[fua][2][bi]"  value="<?php echo $fua[2]['bi'];?>"/>
                <br>
                <input type="text" name="category[fua][3][num]"  value="<?php echo $fua[3]['num'];?>"/>
                <input type="text" name="category[fua][3][ming]"  value="<?php echo $fua[3]['ming'];?>"/>
                <input type="text" name="category[fua][3][bi]"  value="<?php echo $fua[3]['bi'];?>" />
                <br>
                <input type="text" name="category[fua][4][num]"   value="<?php echo $fua[4]['num'];?>"/>
                <input type="text" name="category[fua][4][ming]"  value="<?php echo $fua[4]['ming'];?>"/>
                <input type="text" name="category[fua][4][bi]"  value="<?php echo $fua[4]['bi'];?>"/>
                <br>
                <input type="text" name="category[fua][5][num]"  value="<?php echo $fua[5]['num'];?>"/>
                <input type="text" name="category[fua][5][ming]"  value="<?php echo $fua[5]['ming'];?>"/>
                <input type="text" name="category[fua][5][bi]"  value="<?php echo $fua[5]['bi'];?>"/>
                <br>
                <input type="text" name="category[fua][6][num]"  value="<?php echo $fua[6]['num'];?>"/>
                <input type="text" name="category[fua][6][ming]"  value="<?php echo $fua[6]['ming'];?>"/>
                <input type="text" name="category[fua][6][bi]"  value="<?php echo $fua[6]['bi'];?>"/>
                <br>
                <input type="text" name="category[fua][7][num]"  value="<?php echo $fua[7]['num'];?>"/>
                <input type="text" name="category[fua][7][ming]"  value="<?php echo $fua[7]['ming'];?>"/>
                <input type="text" name="category[fua][7][bi]"  value="<?php echo $fua[7]['bi'];?>"/>
                <br>
                <input type="text" name="category[fua][8][num]"  value="<?php echo $fua[8]['num'];?>"/>
                <input type="text" name="category[fua][8][ming]"  value="<?php echo $fua[8]['ming'];?>"/>
                <input type="text" name="category[fua][8][bi]"  value="<?php echo $fua[8]['bi'];?>"/>
                <br>
                <input type="text" name="category[fua][9][num]"  value="<?php echo $fua[9]['num'];?>"/>
                <input type="text" name="category[fua][9][ming]"  value="<?php echo $fua[9]['ming'];?>"/>
                <input type="text" name="category[fua][9][bi]"  value="<?php echo $fua[9]['bi'];?>"/>
                <br>
                <input type="text" name="category[fua][10][num]"  value="<?php echo $fua[10]['num'];?>"/>
                <input type="text" name="category[fua][10][ming]"  value="<?php echo $fua[10]['ming'];?>"/>
                <input type="text" name="category[fua][10][bi]"  value="<?php echo $fua[10]['bi'];?>"/>
                <br>
                <input type="text" name="category[fua][11][num]"  value="<?php echo $fua[11]['num'];?>"/>
                <input type="text" name="category[fua][11][ming]"  value="<?php echo $fua[11]['ming'];?>"/>
                <input type="text" name="category[fua][11][bi]"  value="<?php echo $fua[11]['bi'];?>"/>
                <br>
                <input type="text" name="category[fua][12][num]"  value="<?php echo $fua[12]['num'];?>"/>
                <input type="text" name="category[fua][12][ming]"  value="<?php echo $fua[12]['ming'];?>"/>
                <input type="text" name="category[fua][12][bi]"  value="<?php echo $fua[12]['bi'];?>"/>
                <br>
            </div>
        </td>
    </tr>




<tr>
<td class="tl"><span class="f_hid">*</span> Title(SEO标题)</td>
<td><input name="category[seo_title]" type="text" id="seo_title" value="<?php echo $seo_title;?>" size="61"></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> Meta Keywords<br/>&nbsp; (网页关键词)</td>
<td><textarea name="category[seo_keywords]" cols="60" rows="3" id="seo_keywords"><?php echo $seo_keywords;?></textarea></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> Meta Description<br/>&nbsp; (网页描述)</td>
<td><textarea name="category[seo_description]" cols="60" rows="3" id="seo_description"><?php echo $seo_description;?></textarea></td>
</tr>

<tr>
        <td class="tl"><span class="f_red">*</span> <?php echo $MOD['name'];?>内容bbb11</td>
        <td><textarea name="category[content]" id="content" class="dsn"><?php echo $content;?></textarea>
            <?php echo deditor($moduleid=1, 'content', $MOD['editor'], '98%', 350);?><br/><span id="dcontent" class="f_red"></span>
        </td>
</tr>
<tr>
        <td class="tl" height="30"><span class="f_hid">*</span> 内容选项</td>
        <td>
            <a href="javascript:pagebreak();Ds('subtitle');"><img src="<?php echo DT_STATIC;?>admin/pagebreak.png" align="absmiddle"/> 插入分页符</a>&nbsp; &nbsp;
            <input type="checkbox" name="post[save_remotepic]" value="1"<?php if($MOD['save_remotepic']) echo 'checked';?>/> 下载远程图片&nbsp; &nbsp;
            <input type="checkbox" name="post[clear_link]" value="1"<?php if($MOD['clear_link']) echo 'checked';?>/> 清除链接&nbsp; &nbsp;
            截取内容 <input name="post[introduce_length]" type="text" size="2" value="<?php echo $MOD['introduce_length']?>"/> 字符至简介&nbsp; &nbsp;
            设置内容第 <input name="post[thumb_no]" type="text" size="2" value="1"/> 张图片为标题11图&nbsp;
            插入投票 <input name="post[voteid]" type="text" size="10" value="<?php echo $voteid;?>"/> <a href="javascript:Dwidget('?moduleid=3&file=vote', '投票列表');" class="t">[查看]</a> <?php tips('请填写投票ID，多个ID请用空格隔开');?>

        </td>
</tr>



<tr>
<td class="tl"><span class="f_hid">*</span> 权限设置</td>
<td class="f_blue">如果没有特殊需要，以下选项不需要设置，全选或全不选均代表拥有对应权限</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 允许浏览分类</td>
<td><?php echo group_checkbox('category[group_list][]', $group_list);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 允许浏览分类信息内容</td>
<td><?php echo group_checkbox('category[group_show][]', $group_show);?></td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 允许发布信息</td>
<td><?php echo group_checkbox('category[group_add][]', $group_add);?></td>
</tr>
</table>
<div class="sbt"><input type="submit" name="submit" value="修 改" class="btn-g"/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="返 回" class="btn" onclick="Go('?mid=<?php echo $mid;?>&file=<?php echo $file;?>');"/></div>
</form>
<script type="text/javascript">
function ckDir() {
	if(Dd('catdir').value == '') {
		Dtip('请填写分类目录');
		Dd('catdir').focus();
		return false;
	}
	var url = '?file=category&action=ckdir&mid=<?php echo $mid;?>&catdir='+Dd('catdir').value;
	Diframe(url, 0, 0, 1);
}
function check() {
	if(Dd('catname').value == '') {
		Dmsg('请填写分类名称', 'catname');
		return false;
	}
	if(Dd('catdir').value == '') {
		Dmsg('请填写分类目录', 'catdir');
		return false;
	}
	return true;
}




$(document).ready(function() {
    $('#addButton').click(function() {
        $('#dataTable tbody').append(`
                    <tr>
                        <td><input type="text" placeholder="输入名称" /></td>
                        <td><input type="number" placeholder="输入数量" /></td>
                        <td><button class="removeButton">删除</button></td>
                    </tr>
                `);
    });

    // 动态删除行
    $(document).on('click', '.removeButton', function() {
        $(this).closest('tr').remove();
    });
});





</script>
<script type="text/javascript">Menuon(1);</script>
<?php } ?>
<?php include tpl('footer');?>