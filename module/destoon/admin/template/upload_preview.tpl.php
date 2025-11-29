<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
if($P) {
	load('jquery.slide.js');
	load('jquery.slide.css');
}
?>
<style>
.dirs {overflow:hidden;padding:8px;clear:both;}
.dirs div {width:128px;height:128px;float:left;border-radius:12px;}
.dirs div:hover {background:#F6F6F6;}
.dirs div p {margin:0;padding:16px 0 0 0;text-align:center;}
.dirs div h5 {margin:0;padding:0;text-align:center;font-weight:normal;line-height:24px;color:#333333;}
.files {overflow:hidden;padding:8px;clear:both;}
.files div {width:168px;height:168px;float:left;border-radius:12px;}
.files div:hover {background:#F6F6F6;}
.files div:hover h5 {display:none;}
.files div:hover h6 {display:block;}
.files div p {margin:0;padding:16px 0 0 0;height:96px;line-height:96px;overflow:hidden;text-align:center;}
.files div img {max-width:96px;vertical-align:middle;}
.files div h5 {margin:0;padding:0;text-align:center;font-weight:normal;height:32px;line-height:16px;padding:8px;overflow:hidden;color:#333333;}
.files div h6 {margin:0;padding:0;text-align:center;font-weight:normal;height:48px;background:url('<?php echo DT_SKIN;?>ico-delete.png') no-repeat center center;overflow:hidden;cursor:pointer;display:none;}
.files div h6:hover {background:url('<?php echo DT_SKIN;?>ico-delete-on.png') no-repeat center center;}
.none {padding:128px 0;text-align:center;color:#999999;border-bottom:#E7E7EB 1px solid;}
.jqv {z-index:1;position:absolute;width:100%;min-width:1220px;height:10000px;background:#000000;display:none;}
.jqm {width:1220px;margin:0 auto;}
#quit {display:none;width:24px;}
#quit div {z-index:2;position:absolute;margin:-6px 0 0 -6px;width:32px;height:32px;background:#CCCCCC url('<?php echo DT_STATIC;?>admin/dialog-close.png') no-repeat center center;border-radius:50%;cursor:pointer;}
#quit div:hover {background:#F45454 url('<?php echo DT_STATIC;?>admin/dialog-close-on.png') no-repeat center center;}
</style>
<form action="?" id="search">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<input type="hidden" name="dir" value="<?php echo $dir;?>"/>
<div class="sbox">
<?php if($files) { ?>
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入文件名或后缀" title="请输入文件名或后缀"/>&nbsp;
<?php } ?>
<?php echo dcalendar('fromdate', $fromdate);?>&nbsp;
<select name="datetype">
<option value="ymd"<?php if($datetype == 'ymd') echo ' selected';?>>年月日</option>
<option value="ym"<?php if($datetype == 'ym') echo ' selected';?>>年月</option>
<option value="y"<?php if($datetype == 'y') echo ' selected';?>>年</option>
</select>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?file=<?php echo $file;?>&action=<?php echo $action;?>');"/>

<input type="button" value="清理空目录" class="btn-r f_r" onclick="if(confirm('确定要清理所有空目录吗？'))Go('?file=<?php echo $file;?>&action=clear');"/>
</div>
</form>
<table cellspacing="0" class="tb">
<tr>
<td class="f_fd" style="border-right:none;">&nbsp;<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>" title="<?php echo $root;?>"><img src="file/ext/folder.gif" alt="" align="absmiddle"/></a> / <?php echo dir_nav($dir);?></td>
<td align="right" width="320" id="link">
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&fromdate=<?php echo timetodate($DT_TIME, 'Y-m-d');?>&datetype=ymd" class="t">今日</a> &nbsp; 
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&fromdate=<?php echo timetodate($DT_TIME - 86400, 'Y-m-d');?>&datetype=ymd" class="t">昨日</a> &nbsp; 
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&fromdate=<?php echo timetodate($DT_TIME - 86400*2, 'Y-m-d');?>&datetype=ymd" class="t">前日</a> &nbsp; 
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&fromdate=<?php echo timetodate($DT_TIME, 'Y-m-d');?>&datetype=ym" class="t">本月</a> &nbsp; 
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&fromdate=<?php echo timetodate(datetotime(timetodate($DT_TIME, 'Y-m').'-01') - 86400, 'Y-m');?>-<?php echo timetodate($DT_TIME, 'd');?>&datetype=ym" class="t">上月</a> &nbsp; 
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&fromdate=<?php echo timetodate($DT_TIME, 'Y-m-d');?>&datetype=y" class="t">今年</a> &nbsp; 
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&fromdate=<?php echo timetodate($DT_TIME, 'Y')-1;?>-<?php echo timetodate($DT_TIME, 'm-d');?>&datetype=y" class="t">去年</a> &nbsp; 
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>" class="t">全部</a> &nbsp; 
<td id="quit"><div onclick="PhotoLast();" title="关闭预览"></div></td>
</td>
</tr>
</table>

<?php if($P) { ?>
<div class="jqv">
<div class="jqm">
	<div class="jqslide">
		<div class="pic">
			<div class="big">
				<img id="photo" src="<?php echo DT_SKIN;?>spacer.gif"/>
				<div id="pload"></div>
			</div>
			<div class="prev"><a href="javascript:void(0);" hidefocus="true" id="prevbtn" class="prevbtn" title="上一张 支持键盘←方向键"></a></div>
			<div class="next"><a href="javascript:void(0);" hidefocus="true" id="nextbtn" class="nextbtn" title="下一张 支持键盘→方向键"></a></div>
		</div>
		<div id="pintro" class="photo_intro"></div>
		<div class="plist">
			<div class="scd">
				<ul id="photolist">			
				</ul>
			</div>
			<div class="scb" id="scb"></div>
			<a href="javascript:void(0);" class="scl" id="scprev" hidefocus="true"></a>
			<a href="javascript:void(0);" class="scr" id="scnext" hidefocus="true"></a>
		</div>
		<ul id="photoinfo" style="display:none;">
		<?php foreach($P as $k=>$v) {?>
		<li>
		<p></p>
		<i title="bimg"><?php echo $v['big'];?></i>
		<i title="simg"><span><?php echo $items-$k;?>/<?php echo $items;?></span><a href="javascript:void(0)" title="<?php echo $v['big'];?>" hidefocus="true"><img src="<?php echo $v['middle'];?>" width="100" height="75" alt=""/></a></i>
		</li>
		<?php } ?>
		</ul>
	</div>
</div>
</div>
<script type="text/javascript">
	var load_page = 1;
	var load_item = <?php echo $items;?>;
	function PhotoLast() {
		$('.jqv').fadeOut(300);
		$('#quit').hide();
		$('#link').show();
	}
	function PhotoShow(url) {
		$('#photo').attr('src', url);
		$('#photolist a').each(function(i) {
			if($(this).attr('title') == url) {
				Gslide.nowli = i;
				$(this).find('img').eq(0).css({border:'1px solid #FF6600',background:'#FF6600'});
			} else {
				$(this).find('img').eq(0).css({border:'1px solid #BFBFBF',background:'#FFFFFF'});
			}
		});
		$('.jqv').fadeIn(300);
		$('#link').hide();
		$('#quit').show();
	}
	$(function(){	
		$('.jqv').click(function(e) {
			e.stopPropagation();
			if($(e.target).attr('class') == 'jqv') PhotoLast();
		});
	});
</script>
<?php } ?>

<?php if($dirs) { ?>
<div class="dirs">
<?php foreach($dirs as $v) {?>
<a href="?file=<?php echo $file;?>&action=<?php echo $action;?>&dir=<?php echo $dir ? $dir.$v['dirname'] : $v['dirname'];?>">
<div>
<p><img src="file/ext/icon-folder.png"/></p>
<h5><?php echo $v['dirname'];?></h5>
</div>
</a>
<?php } ?>
</div>
<?php } ?>

<?php if($files) { ?>
<div class="files">
<?php foreach($files as $v) {?>
<?php if(!in_array($v['id'], $thumbs)) { ?>
<div title="文件名称：<?php echo $v['filename'];?>&#10;文件大小：<?php echo $v['filesize'];?>Kb&#10;上传时间：<?php echo $v['mtime'];?>&#10;">
<a href="<?php echo $v['url'];?>"><p><img src="<?php echo $v['src'];?>"/></p></a>
<h5><?php echo cutstr($v['filename'], '', '.thumb.');?></h5>
<h6 onclick="_del('<?php echo $v['filename'];?>');"></h6>
</div>
<?php } ?>
<?php } ?>
</div>
<?php } ?>

<?php if(!$dirs && !$files) { ?>
<div class="none">当前目录未找到任何内容，请换个目录再试</div>
<?php } ?>

<script type="text/javascript">
function _del(name) {
	if(confirm('确定要删除文件吗？此操作将不可撤销')) Go('?file=<?php echo $file;?>&action=<?php echo $action;?>&job=delete&dir=<?php echo $dir;?>&name='+name);
}
Menuon(2);
</script>
<?php include tpl('footer');?>