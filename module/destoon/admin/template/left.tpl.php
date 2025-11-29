<?php
defined('DT_ADMIN') or exit('Access Denied');
?>
<!doctype html>
<html lang="<?php echo DT_LANG;?>">
<head>
<meta charset="<?php echo DT_CHARSET;?>"/>
<title>管理中心 - <?php echo $DT['sitename']; ?> - Powered By DESTOON V<?php echo DT_VERSION; ?> R<?php echo DT_RELEASE;?></title>
<meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,width=device-width"/>
<meta name="robots" content="noindex,nofollow"/>
<meta name="generator" content="DESTOON - www.destoon.com"/>
<meta http-equiv="x-ua-compatible" content="IE=8"/>
<link rel="stylesheet" type="text/css" href="<?php echo DT_STATIC;?>admin/style.css?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"/>
<link rel="stylesheet" type="text/css"  href="<?php echo DT_STATIC;?>admin/side.css?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"/>
<link rel="stylesheet" type="text/css" href="<?php echo DT_PATH;?>file/style/admin.reset.css?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"/>
<script type="text/javascript" src="<?php echo DT_PATH;?>lang/<?php echo DT_LANG;?>/lang.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<script type="text/javascript" src="<?php echo DT_PATH;?>file/script/config.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<?php if(strpos($DT_MBS, 'IE') === false) { ?>
<script type="text/javascript" src="<?php echo DT_STATIC;?>script/jquery-3.6.4.min.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<?php } else { ?>
<script type="text/javascript" src="<?php echo DT_STATIC;?>script/jquery-1.12.4.min.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo DT_STATIC;?>script/common.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<script type="text/javascript" src="<?php echo DT_STATIC;?>script/panel.js?v=<?php echo DT_DEBUG ? DT_TIME : DT_REFRESH;?>"></script>
<base target="main"/>
</head>
<body>
<?php
include DT_ROOT.'/module/destoon/admin/menu.inc.php';
if($_admin == 2) {
?>
<table cellpadding="0" cellspacing="0" width="<?php echo $_admin == 2 ? $DT['admin_left'] - 48 : $DT['admin_left'];?>" height="100%">
<tr>
<td valign="top" class="barmain" id="menu">
	<div id="m_1">
	<dl>
	<dt onclick="s(this)"><i class="ico-user">我的面板</i></dt>
	<dd onclick="c(this);" class="dd-on"><a href="?action=main">后台首页</a></dd>
	<dd onclick="c(this);"><a href="?file=panel">定义面板</a></dd>
	<span id="panel">
	<?php foreach($panel as $v) { ?>
	<dd onclick="c(this);"><a href="<?php echo substr($v['url'], 0, 1) == '?' ? $v['url'] : gourl($v['url']).'" target="_blank';?>"><?php echo set_style($v['title'], $v['style']);?></a></dd>
	<?php } ?>
	</span>
	<dd onclick="c(this);"><a href="?action=logout" target="_top" onclick="return confirm('确定要退出管理后台吗');">安全退出</a></dd>
	</dl>
	<?php if($DT['admin_hit']) { ?>
	<dl>
	<dt onclick="s(this)" class="dt-d"><i class="ico-list">常用菜单</i></dt>
	<span id="menus">
	<?php foreach($menus as $v) { ?>
	<dd onclick="c(this);" style="display:none;"><a href="<?php echo substr($v['url'], 0, 1) == '?' ? $v['url'] : gourl($v['url']).'" target="_blank';?>"><?php echo set_style($v['title'], $v['style']);?></a></dd>
	<?php } ?>
	</span>
	<dd onclick="c(this);" style="display:none;"><a href="?file=panel&action=menu">更多记录</a></dd>
	</dl>
	<?php } ?>
	</div>
</td>
</tr>
</table>
<?php } else { ?>
<table cellpadding="0" cellspacing="0" width="<?php echo $DT['admin_left'];?>" height="100%">
<tr>
<td id="bar" class="bar" valign="top">
<div class="barfix">
<div onclick="sideshow(1);" id="b_1" class="barfix-on" ondblclick="window.open('?action=new');"><img src="<?php echo DT_STATIC;?>admin/bar1.png" width="16" height="16"/><span>我的面板</span></div>
<div onclick="sideshow(2);" id="b_2"><img src="<?php echo DT_STATIC;?>admin/bar2.png" width="16" height="16"/><span>系统维护</span></div>
<div onclick="sideshow(3);" id="b_3"><img src="<?php echo DT_STATIC;?>admin/bar3.png" width="16" height="16"/><span>功能模块</span></div>
<div onclick="sideshow(4);" id="b_4"><img src="<?php echo DT_STATIC;?>admin/bar4.png" width="16" height="16"/><span>会员管理</span></div>
<div onclick="sideshow(5);" id="b_5"><img src="<?php echo DT_STATIC;?>admin/bar5.png" width="16" height="16"/><span>扩展功能</span></div>
<div onclick="sideshow(6);" id="b_6"><img src="<?php echo DT_STATIC;?>admin/bar6.png" width="16" height="16"/><span>后台搜索</span></div>
<div onclick="top.document.getElementById('destoon-panel').cols = '0,8,*';" id="b_7"><img src="<?php echo DT_STATIC;?>admin/bar7.png" width="16" height="16"/><span>隐藏侧栏</span></div>
</div>
</td>
<td valign="top" class="barmain" id="menu">
	<div id="m_1">
	<dl>
	<dt onclick="s(this)"><i class="ico-user">我的面板</i></dt>
	<dd onclick="c(this);" class="dd-on"><a href="?action=main">后台首页</a></dd>
	<dd onclick="c(this);"><a href="?file=panel">定义面板</a></dd>
	<span id="panel">
	<?php foreach($panel as $v) { ?>
	<dd onclick="c(this);"><a href="<?php echo substr($v['url'], 0, 1) == '?' ? $v['url'] : gourl($v['url']).'" target="_blank';?>"><?php echo set_style($v['title'], $v['style']);?></a></dd>
	<?php } ?>
	</span>
	<dd onclick="c(this);"><a href="?action=logout" target="_top" onclick="return confirm('确定要退出管理后台吗');">安全退出</a></dd>
	</dl>	
	<?php if($DT['admin_hit']) { ?>
	<dl>
	<dt onclick="s(this)"><i class="ico-list">常用菜单</i></dt>
	<span id="menus">
	<?php foreach($menus as $v) { ?>
	<dd onclick="c(this);"><a href="<?php echo substr($v['url'], 0, 1) == '?' ? $v['url'] : gourl($v['url']).'" target="_blank';?>"><?php echo set_style($v['title'], $v['style']);?></a></dd>
	<?php } ?>
	</span>
	<dd onclick="c(this);"><a href="?file=panel&action=menu">更多记录</a></dd>
	</dl>	
	<?php } ?>
	<dl>
	<dt onclick="s(this)" class="dt-d"><i class="ico-help">使用帮助</i></dt>
	<?php
		foreach($menu_help as $m) {
			echo '<dd onclick="c(this);" style="display:none;"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
	</div>
	<div id="m_2" style="display:none;">
	<?php if($_founder) { ?>
	<dl> 
	<dt onclick="s(this)"><i class="ico-list">系统维护</i></dt> 
	<?php
		foreach($menu_system as $m) {
			echo '<dd onclick="c(this);"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
	<?php } ?>
	<dl> 
	<dt onclick="s(this)"><i class="ico-tool">系统工具</i></dt>
	<?php
		foreach($menu as $m) {
			echo '<dd onclick="c(this);"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
	</div>
	<div id="m_3" style="display:none;">
	<?php
		$k = 0;
		foreach($MODULE as $v) {
			if($v['moduleid'] > 3 && !$v['islink']) {
				$menuinc = DT_ROOT.'/module/'.$v['module'].'/admin/menu.inc.php';
				if(is_file($menuinc)) {
					extract($v);
					include $menuinc;
					echo '<dl id="dl_'.$moduleid.'">';
					echo '<dt id="dt_'.$moduleid.'" onclick="m('.$moduleid.');" class="dt-d">'.$name.(strlen($name) < 7 ? '管理' : '').'</dt>';
					foreach($menu as $m) {
						echo '<dd onclick="c(this);" style="display:none;"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
					}
					echo '</dl>';
					$k++;
				}
			}
		}
	?>
	</div>
	<div id="m_4" style="display:none;">
	<?php
		$menuinc = DT_ROOT.'/module/'.$MODULE[2]['module'].'/admin/menu.inc.php';
		if(is_file($menuinc)) {
			extract($MODULE[2]);
			include $menuinc;
			echo '<dl id="dl_'.$moduleid.'">';
			echo '<dt id="dt_'.$moduleid.'" onclick="s(this);"><i class="ico-user">'.$name.(strlen($name) < 7 ? '管理' : '').'</i></dt>';
			foreach($menu as $m) {
				echo '<dd onclick="c(this);"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
			}
			echo '</dl>';
		}
	?>
	<?php
		$menuinc = DT_ROOT.'/module/'.$MODULE[4]['module'].'/admin/menu.inc.php';
		if(is_file($menuinc)) {
			extract($MODULE[4]);
			include $menuinc;
			echo '<dl id="dl_'.$moduleid.'">';
			echo '<dt id="dt_'.$moduleid.'" class="dt-d" onclick="s(this);"><i class="ico-list">'.$name.(strlen($name) < 7 ? '管理' : '').'</i></dt>';
			foreach($menu as $m) {
				echo '<dd onclick="c(this);" style="display:none;"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
			}
			echo '</dl>';
		}
	?>
	<dl id="dl_pay"> 
	<dt id="dt_pay" onclick="s(this);"><i class="ico-coin">财务管理</i></dt>
	<?php
		foreach($menu_finance as $m) {
			echo '<dd onclick="c(this);"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
	<dl id="dl_oth"> 
	<dt id="dt_oth" onclick="s(this);"><i class="ico-user">会员相关</i></dt> 
	<?php
		foreach($menu_relate as $m) {
			echo '<dd onclick="c(this);"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
		}
	?>
	</dl>
	</div>
	<div id="m_5" style="display:none;">
	<?php
		$menuinc = DT_ROOT.'/module/'.$MODULE[3]['module'].'/admin/menu.inc.php';
		if(is_file($menuinc)) {
			extract($MODULE[3]);
			include $menuinc;
			echo '<dl id="dl_'.$moduleid.'">';
			echo '<dt onclick="s(this);"><i class="ico-list">扩展功能</i></dt>';
			foreach($menu as $m) {
				echo '<dd onclick="c(this);"><a href="'.$m[1].'">'.$m[0].'</a></dd>';
			}
			echo '</dl>';
		}
	?>
	</div>
</td>
</tr>
</table>
<?php } ?>
<script type="text/javascript">
function sidelink(url, id) {
	sideshow(id);
	var d = $("#m_"+id+" [href='"+url+"']");
	if(d) {
		if(d.parent().css('display') == 'none') {
			s(d.parent().parent().find('dt')[0]);
		}
		c(d.parent()[0]);
		$('html, body').animate({scrollTop:d.parent().offset().top-96}, 200);
	}
}
function sideshow(id) {
	if(id == 6) {
		top.document.getElementsByName('main')[0].contentWindow.Dwidget('?file=search', '后台搜索');
		return;
	}
	for(i=1;i<6;i++) {
		if(i==id) {
			$('#m_'+i).show();
			$('#b_'+i).addClass('barfix-on');
		} else {
			$('#m_'+i).hide();
			$('#b_'+i).removeClass();
		}
	}
}
function c(o) {
	var dds = Dd('menu').getElementsByTagName('dd');
	for(var i=0;i<dds.length;i++) {
		dds[i].className = dds[i] == o ? 'dd-on' : '';
		if(dds[i] == o) o.firstChild.blur();
	}
	<?php if($DT['admin_hit']) { ?>
	$.post('?', 'file=panel&action=log&title='+encodeURIComponent($(o).text())+'&url='+encodeURIComponent($(o).find('a').attr('href')), function(data) {});
	<?php } ?>
}
function s(o) {
	o.className = o.className == 'dt-d' ? '' : 'dt-d';
	var dds = o.parentNode.getElementsByTagName('dd');
	for(var i=0;i<dds.length;i++) {
		dds[i].style.display = dds[i].style.display == 'none' ? '' : 'none';
	}
}
function h(o) {
	var dds = o.parentNode.getElementsByTagName('dd');
	for(var i=0;i<dds.length;i++) {
		dds[i].style.display = 'none';
	}
}
function m(ID) {
	var dls = Dd('m_3').getElementsByTagName('dl');
	for(var i=0;i<dls.length;i++) {
		var dds = Dd(dls[i].id).getElementsByTagName('dd');
		for(var j=0;j<dds.length;j++) {
			dds[j].style.display = dls[i].id == 'dl_'+ID ? dds[j].style.display == 'none' ? '' : 'none' : 'none';
		}
		var mid = dls[i].id.replace('dl_', '');
		if(dds[0].style.display == 'none') {
			Dd('dt_'+mid).className = 'dt-d';
		} else {
			Dd('dt_'+mid).className = mid == ID ? '' : 'dt-d';
		}
	}
}
function n() {
	$.post('?', 'action=menu', function(data) {
		var t = data.split('<br>');
		if(t[0]) $('#panel').html(t[0]);
		<?php if($DT['admin_hit']) { ?>
		if(t[1]) $('#menus').html(t[1]);
		<?php } ?>
	});
}
</script>
</body>
</html>