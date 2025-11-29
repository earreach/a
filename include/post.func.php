<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function deditor($moduleid = 1, $textareaid = 'content', $toolbarset = 'Default', $width = 500, $height = 400) {
	global $DT, $MODULE, $_userid, $destoon_editor_id;
	$editor = '';
	if(!$destoon_editor_id) {
		$destoon_editor_id = 1;
	} else {
		$destoon_editor_id++;
	}
	include DT_ROOT.'/'.$MODULE[2]['moduledir'].'/editor/'.DT_EDITOR.'/init.inc.php';	
	return $editor;
}

function dstyle($name, $value = '', $oid = '') {
	global $destoon_style_id;
	$style = $color = '';
	if(preg_match("/^#[0-9a-zA-Z]{6}$/", $value)) $color = $value;
	if(!$destoon_style_id) {
		$destoon_style_id = 1;
		$style .= '<script type="text/javascript" src="'.DT_STATIC.'script/color.js?v='.DT_REFRESH.'"></script>';
	} else {
		$destoon_style_id++;
	}
	$style .= '<input type="hidden" name="'.$name.'" id="color_input_'.$destoon_style_id.'" value="'.$color.'"/><img src="'.DT_PATH.'static/image/color.gif" width="30" height="30" align="absmiddle" id="color_img_'.$destoon_style_id.'" style="cursor:pointer;background:'.$color.'" onclick="color_show('.$destoon_style_id.', \''.$oid.'\');"/>';
	return $style;
}

function dcalendar($name, $value = '', $sep = '-', $time = 0) {
	global $destoon_calendar_id;
	$calendar = '';
	$id = str_replace(array('[', ']'), array('', ''), $name);
	if(!$destoon_calendar_id) {
		$destoon_calendar_id = 1;
		$calendar .= '<script type="text/javascript" src="'.DT_STATIC.'script/calendar.js?v='.DT_REFRESH.'"></script>';
	}
	$calendar .= '<input type="text" name="'.$name.'" id="'.$id.'" value="'.$value.'" style="width:'.($time ? 156 : 100).'px;" autocomplete="off" onfocus="ca_show(\''.$id.'\', \''.$sep.'\', '.$time.');" readonly ondblclick="this.value=\'\';"/><img src="'.DT_PATH.'static/image/calendar.gif" style="z-index:1;position:relative;top:3px;left:-8px;margin:0 0 0 -16px;cursor:pointer;" onclick="ca_show(\''.$id.'\', \''.$sep.'\', '.$time.');"/>';
	return $calendar;
}

function dselect($sarray, $name, $title = '', $selected = 0, $extend = '', $key = 1, $ov = '', $abs = 0) {
	$select = '<select name="'.$name.'" '.$extend.'>';
	if($title) $select .= '<option value="'.$ov.'">'.$title.'</option>';
	foreach($sarray as $k=>$v) {
		if(!$v) continue;
		$val = $key ? $k : $v;
		$_selected = ($abs ? $val === $selected : $val == $selected) ? ' selected="selected"' : '';
		if(strpos($select, ' selected="selected"') !== false) $_selected = '';
		$select .= '<option value="'.$val.'"'.$_selected.'>'.strip_tags($v).'</option>';
	}	
	$select .= '</select>';
	return $select;
}

function dcheckbox($sarray, $name, $checked = '', $extend = '', $key = 1, $except = '', $abs = 0) {
	$checked = $checked ? explode(',', $checked) : array();
	$except = $except ? explode(',', $except) : array();
	$checkbox = $sp = '';
	foreach($sarray as $k=>$v) {
		if(in_array($key ? $k : $v, $except)) continue;
		$sp = in_array($key ? $k : $v, $checked) ? ' checked ' : '';
		$checkbox .= '<label><input type="checkbox" name="'.$name.'" value="'.($key ? $k : $v).'"'.$sp.$extend.'> '.$v.'</label>&nbsp;';
	}
	return $checkbox;
}

function type_select($item, $cache = 0, $name = 'typeid', $title = '', $typeid = 0, $extend = '', $all = '') {
	$TYPE = is_array($item) ? $item : get_type($item, $cache);
	$select = '<select name="'.$name.'" '.$extend.'>';
	if($all) $select .= '<option value="-1"'.($typeid == -1 ? ' selected="selected"' : '').'>'.$all.'</option>';
	if($title) $select .= '<option value="0"'.($typeid == 0 ? ' selected="selected"' : '').'>'.$title.'</option>';
	$p = $c = array();
	foreach($TYPE as $v) {
		if($v['parentid']) {
			$c[$v['parentid']][] = $v;
		} else {
			$p[] = $v;
		}
	}
	foreach($p as $v0) {
		$select .= '<option value="'.$v0['typeid'].'"'.($v0['typeid'] == $typeid ? ' selected' : '').'>'.$v0['typename'].'</option>';
		if(isset($c[$v0['typeid']])) {
			foreach($c[$v0['typeid']] as $v1) {
				$select .= '<option value="'.$v1['typeid'].'"'.($v1['typeid'] == $typeid ? ' selected' : '').'>&nbsp;&#9500;'.$v1['typename'].'</option>';
			}
		}
	}
	$select .= '</select>';
	return $select;
}

function type_child($typeid, $TYPE) {
	if($typeid <= 0) return '0';
	$id = $typeid;
	foreach($TYPE as $v) {
		if($v['parentid'] == $typeid) $id .= ','.$v['typeid'];
	}
	return $id;
}

function url_select($name, $ext = 'htm', $type = 'list', $urlid = 0, $extend = '') {
	global $L;
	include DT_ROOT."/api/url.inc.php";
	$select = '<select name="'.$name.'" '.$extend.'>';
	$types = count($urls[$ext][$type]);
	for($i = 0; $i < $types; $i++) {
		$select .= ' <option value="'.$i.'"'.($i == $urlid ? ' selected' : '').'>'.$L['url_eg'].' '.$urls[$ext][$type][$i]['example'].'</option>';
	}
	$select .= '</select>';
	return $select;
}

function tpl_select($file = 'index', $module = '', $name = 'template', $title = '', $template = '', $extend = '') {
	include load('include.lang');
	global $CFG, $destoon_tpl_id;
	if(!$destoon_tpl_id) {
		$destoon_tpl_id = 1;
	} else {
		$destoon_tpl_id++;
	}
    $tpldir = $module ? DT_ROOT."/template/".$CFG['template']."/".$module : DT_ROOT."/template/".$CFG['template'];
	@include $tpldir."/these.name.php";
	$select = '<span id="destoon_template_'.$destoon_tpl_id.'"><select name="'.$name.'" '.$extend.'><option value="">'.$title.'</option>';
	$files = glob($tpldir."/*.htm");
	foreach($files as $tplfile)	{
		$tplfile = basename($tplfile);
		$tpl = str_replace('.htm', '', $tplfile);
		if(preg_match("/^".$file."-(.*)/i", $tpl) || !$file) {//$file == $tpl || 
			$selected = ($template && $tpl == $template) ? 'selected' : '';
            $templatename = (isset($names[$tpl]) && $names[$tpl]) ? $names[$tpl] : $tpl;
			$select .= '<option value="'.$tpl.'" '.$selected.'>'.$templatename.'</option>';
		}
	}
	$select .= '</select></span>';
	if(defined('DT_ADMIN')) $select .= '&nbsp;&nbsp;<a href="javascript:tpl_edit(\''.$file.'\', \''.$module.'\', '.$destoon_tpl_id.');" class="t"><img src="'.DT_STATIC.'image/ico-edit.png" title="'.$L['post_edit'].'"/></a> &nbsp;<a href="javascript:tpl_add(\''.$file.'\', \''.$module.'\');" class="t"><img src="'.DT_STATIC.'image/ico-new.png" title="'.$L['post_new'].'"/></a>';
	return $select;
}

function grade_select($name = 'gradeid', $title = '', $gradeid = '', $extend = '') {
	global $GRADE;
	$GRADE or $GRADE = cache_read('grade.php');
	$select = '<select name="'.$name.'" '.$extend.'><option value="0">'.$title.'</option>';
	foreach($GRADE as $k=>$v) {
		$select .= '<option value="'.$k.'"'.($k == $gradeid ? ' selected' : '').'>'.$v['name'].'</option>';
	}
	$select .= '</select>';
	return $select;
}

function group_select($name = 'groupid', $title = '', $groupid = '', $extend = '') {
	global $GROUP;
	$GROUP or $GROUP = cache_read('group.php');
	$select = '<select name="'.$name.'" '.$extend.'><option value="0">'.$title.'</option>';
	foreach($GROUP as $k=>$v) {
		$select .= '<option value="'.$k.'"'.($k == $groupid ? ' selected' : '').'>'.$v['groupname'].'</option>';
	}
	$select .= '</select>';
	return $select;
}

function group_checkbox($name = 'groupid', $checked = '', $except = '1,2,4') {
	global $GROUP, $L;
	$GROUP or $GROUP = cache_read('group.php');
	$checked = $checked ? explode(',', $checked) : array();
	$except = $except ? explode(',', $except) : array();
	$str = $sp = '';
	$id = str_replace(array('[', ']'), array('', ''), $name);
	foreach($GROUP as $k=>$v) {
		if(in_array($k, $except)) continue;
		$sp = in_array($k, $checked) ? ' checked' : '';
		$str .= '<label><input type="checkbox" name="'.$name.'" value="'.$k.'"'.$sp.' id="'.$id.$k.'"/> '.$v['groupname'].'&nbsp; </label>';
	}
	return '<span id="group_'.$id.'">'.$str.'</span>&nbsp;<a href="javascript:check_box(\'group_'.$id.'\', true);" class="t">'.$L['select_all'].'</a> / <a href="javascript:check_box(\'group_'.$id.'\', false);" class="t">'.$L['clear_all'].'</a>';
}

function module_checkbox($name = 'moduleid', $checked = '', $except = '1,2,3,4') {
	global $MODULE;
	$checked = $checked ? explode(',', $checked) : array();
	$except = $except ? explode(',', $except) : array();
	$str = $sp = '';
	$id = str_replace(array('[', ']'), array('', ''), $name);
	foreach($MODULE as $k=>$v) {
		if(in_array($k, $except) || $v['islink']) continue;
		$sp = in_array($k, $checked) ? ' checked' : '';
		$str .= '<li><label><input type="checkbox" name="'.$name.'" value="'.$k.'"'.$sp.' id="'.$id.$k.'"/> '.$v['name'].'&nbsp; </label></li>';
	}
	return '<ul class="mods">'.$str.'</ul>';
}

function module_select($name = 'moduleid', $title = '', $moduleid = '', $extend = '', $except = '1,2,3') {
	global $MODULE, $L;
	$except = $except ? explode(',', $except) : array();
	$title or $title = $L['choose'];
	$select = '<select name="'.$name.'" '.$extend.'><option value="0">'.$title.'</option>';
	foreach($MODULE as $k=>$v) {
		if(in_array($k, $except) || $v['islink']) continue;
		$select .= '<option value="'.$k.'"'.($k == $moduleid ? ' selected' : '').'>'.$v['name'].'</option>';
	}
	$select .= '</select>';
	return $select;
}

function homepage_select($name, $title = '', $groupid = 0, $itemid = 0, $extend = '') {
	global $L;
	$title or $title = $L['choose'];
	$select = '<select name="'.$name.'" '.$extend.'><option value="0">'.$title.'</option>';
	$result = DB::query("SELECT * FROM ".DT_PRE."style ORDER BY listorder DESC,itemid DESC");
	while($r = DB::fetch_array($result)) {
		$select .= '<option value="'.$r['itemid'].'"'.($r['itemid'] == $itemid ? ' selected' : '').'>'.$r['title'].'</option>';
	}
	$select .= '</select>';
	return $select;
}

function product_select($name = 'pid', $title = '', $pid = 0, $extend = '') {
	global $PRODUCT;
	$PRODUCT or $PRODUCT = cache_read('product.php');
	$select = '<select name="'.$name.'" '.$extend.'>';
	if($title) $select .= '<option value="0">'.$title.'</option>';
	foreach($PRODUCT as $k=>$v) {
		$select .= '<option value="'.$k.'"'.($k == $pid ? ' selected' : '').'>'.$v['title'].'</option>';
	}
	$select .= '</select>';
	return $select;
}

function category_select($name = 'catid', $title = '', $catid = 0, $moduleid = 1, $extend = '') {
	$option = cache_read('catetree-'.$moduleid.'.php', '', true);
	if($option) {
		if($catid) $option = str_replace('value="'.$catid.'"', 'value="'.$catid.'" selected', $option);
		$select = '<select name="'.$name.'" '.$extend.' id="catid_1">';
		if($title) $select .= '<option value="0">'.$title.'</option>';
		$select .= $option ? $option : '</select>';
		return $select;
	} else {
		return ajax_category_select($name, $title, $catid, $moduleid, $extend);
	}
}

function get_category_select($title = '', $catid = 0, $moduleid = 1, $extend = '', $deep = 0, $cat_id = 1) {
	global $_catid;
	$_catid or $_catid = array();
	$parents = array();
	if($catid) {
		$r = DB::get_one("SELECT child,arrparentid FROM ".DT_PRE."category WHERE catid=$catid");
		$parents = explode(',', $r['arrparentid']);
		if($r['child']) $parents[] = $catid;
	} else {
		$parents[] = 0;
	}
	$select = '';
	foreach($parents as $k=>$v) {
		if($deep && $deep <= $k) break;
		$select .= '<select onchange="load_category(this.value, '.$cat_id.');" '.$extend.'>';
		if($title) $select .= '<option value="'.$v.'">'.$title.'</option>';
		$condition = $v ? "parentid=$v" : "moduleid=$moduleid AND parentid=0";
		$result = DB::query("SELECT catid,catname FROM ".DT_PRE."category WHERE {$condition} ORDER BY listorder,catid ASC");
		while($c = DB::fetch_array($result)) {
			$selectid = isset($parents[$k+1]) ? $parents[$k+1] : $catid;
			$selected = $c['catid'] == $selectid ? ' selected' : '';
			if($_catid && is_array($_catid) && !in_array($c['catid'], $_catid)) continue;
			$select .= '<option value="'.$c['catid'].'"'.$selected.'>'.$c['catname'].'</option>';
		}
		$select .= '</select> ';
	}
	return $select;
}

function ajax_category_select($name = 'catid', $title = '', $catid = 0, $moduleid = 1, $extend = '', $deep = 0) {
	global $cat_id;
	if($cat_id) {
		$cat_id++;
	} else {
		$cat_id = 1;
	}
	$catid = intval($catid);
	$deep = intval($deep);
	$select = '';
	$select .= '<input name="'.$name.'" id="catid_'.$cat_id.'" type="hidden" value="'.$catid.'"/>';
	$select .= '<span id="load_category_'.$cat_id.'">'.get_category_select($title, $catid, $moduleid, $extend, $deep, $cat_id).'</span>';
	$select .= '<script type="text/javascript">';
	if($cat_id == 1) $select .= 'var category_moduleid = new Array;';
	$select .= 'category_moduleid['.$cat_id.']="'.$moduleid.'";';
	if($cat_id == 1) $select .= 'var category_title = new Array;';
	$select .= 'category_title['.$cat_id.']=\''.$title.'\';';
	if($cat_id == 1) $select .= 'var category_extend = new Array;';
	$select .= 'category_extend['.$cat_id.']=\''.encrypt($extend, DT_KEY.'CAT').'\';';
	if($cat_id == 1) $select .= 'var category_catid = new Array;';
	$select .= 'category_catid['.$cat_id.']=\''.$catid.'\';';
	if($cat_id == 1) $select .= 'var category_deep = new Array;';
	$select .= 'category_deep['.$cat_id.']=\''.$deep.'\';';
	$select .= '</script>';
	if($cat_id == 1) $select .= '<script type="text/javascript" src="'.DT_STATIC.'script/category.js?v='.DT_REFRESH.'"></script>';
	return $select;
}

function ajax_area_select($name = 'areaid', $title = '', $areaid = 0, $extend = '', $deep = 0) {
	global $area_id;
	if($area_id) {
		$area_id++;
	} else {
		$area_id = 1;
	}
	$areaid = intval($areaid);
	$deep = intval($deep);
	$select = '';
	$select .= '<input name="'.$name.'" id="areaid_'.$area_id.'" type="hidden000000000000" value="'.$areaid.'"/>';
	$select .= '<span id="load_area_'.$area_id.'">'.get_area_select($title, $areaid, $extend, $deep, $area_id).'</span>';
	$select .= '<script type="text/javascript">';
	if($area_id == 1) $select .= 'var area_title = new Array;';
	$select .= 'area_title['.$area_id.']=\''.$title.'\';';
	if($area_id == 1) $select .= 'var area_extend = new Array;';
	$select .= 'area_extend['.$area_id.']=\''.encrypt($extend, DT_KEY.'ARE').'\';';
	if($area_id == 1) $select .= 'var area_areaid = new Array;';
	$select .= 'area_areaid['.$area_id.']=\''.$areaid.'\';';
	if($area_id == 1) $select .= 'var area_deep = new Array;';
	$select .= 'area_deep['.$area_id.']=\''.$deep.'\';';
	$select .= '</script>';
	if($area_id == 1) $select .= '<script type="text/javascript" src="'.DT_STATIC.'script/area.js?v='.DT_REFRESH.'"></script>';
	return $select;
}

function ajax_shuxing_select($name = 'shuxingid', $title = '', $shuxingid = 0, $extend = '', $deep = 0) {
//    自己加
    global $_shuxingid,$module,$moduleid;


    global $shuxing_id;
    if($shuxing_id) {
        $shuxing_id++;
    } else {
        $shuxing_id = 1;
    }


    var_dump($moduleid);
    echo 77777777777;
//    die();
//    echo 333;
    $shuxingid = intval($shuxingid);
    $deep = intval($deep);
    $select = '';
    $select .= '<input name="'.$name.'" id="shuxingid_'.$shuxing_id.'" type="hidden1111111111111" value="'.$shuxingid.'"/>';
    //在get_shuxing_select上一步已经通过id="load_shuxing'.$shuxing_id.获取最新的$shuxing_id，应该通过下方js获取到的
    $select .= '<span id="load_shuxing_'.$shuxing_id.'">'.get_shuxing_select($title, $shuxingid,$extend, $deep, $shuxing_id).'</span>';
    $select .= '<script type="text/javascript">';


//    if($shuxing_id == 1) $select .= 'var shuxing_moduleid = 21;';
//    $select .= 'shuxing_moduleid['.$shuxing_id.']="'.$moduleid.'";';
//    $select.='alert(shuxing_moduleid['.$shuxing_id.'])' ;

//
//    应该不需要这个属性，而且加了也没用
    /*
    if($shuxing_id == 1) $select .= 'var shuxing_moduleid = new Array;';
    $select .= 'shuxing_moduleid['.$shuxing_id.']="'.$moduleid.'";';
*/


    if($shuxing_id == 1) $select .= 'var shuxing_title = new Array;';
    $select .= 'shuxing_title['.$shuxing_id.']=\''.$title.'\';';
    if($shuxing_id == 1) $select .= 'var shuxing_extend = new Array;';
    $select .= 'shuxing_extend['.$shuxing_id.']=\''.encrypt($extend, DT_KEY.'ARE').'\';';
    if($shuxing_id == 1) $select .= 'var shuxing_shuxingid = new Array;';
    $select .= 'shuxing_shuxingid['.$shuxing_id.']=\''.$shuxingid.'\';';
    if($shuxing_id == 1) $select .= 'var shuxing_deep = new Array;';
    $select .= 'shuxing_deep['.$shuxing_id.']=\''.$deep.'\';';
    $select .= '</script>';
//    引用shuxingjs
    if($shuxing_id == 1) $select .= '<script type="text/javascript" src="'.DT_STATIC.'script/shuxing.js?v='.DT_REFRESH.'"></script>';
    return $select;
}
function level_select($name, $title = '', $level = 0, $extend = '') {
	global $MOD, $L;
	$names = isset($MOD['level']) && $MOD['level'] ? $MOD['level'] : '';
	$names = $names ? explode('|', trim($names)) : array();
	$select = '<select name="'.$name.'" '.$extend.'>';
	if($title) $select .= '<option value="0">'.$title.'</option>';
	for($i = 1; $i < 10; $i++) {
		$n = isset($names[$i-1]) ? ' '.$names[$i-1] : '';
		$select .= '<option value="'.$i.'"'.($i == $level ? ' selected' : '').'>'.$i.' '.$L['level'].$n.'</option>';
	}
	if($extend == 'all') $select .= '<option value="10"'.(10 == $level ? ' selected' : '').'>'.$L['level_all'].'</option>';
	$select .= '</select>';
	return $select;
}

function get_area_select($title = '', $areaid = 0, $extend = '', $deep = 0, $id = 1) {
    var_dump($title,$areaid,$extend,$deep,$id);
//    die();
    global $_areaid;
    $_areaid or $_areaid = array();
    $parents = array();
    if($areaid) {
        $sql1="SELECT child,arrparentid FROM ".DT_PRE."area WHERE areaid=$areaid";
        $r = DB::get_one($sql1);
        var_dump($sql1);
        var_dump($r);
//        die();
        $parents = explode(',', $r['arrparentid']);
        if($r['child']) $parents[] = $areaid;
    } else {
        $parents[] = 0;
    }
    $select = '';
    foreach($parents as $k=>$v) {
        if($deep && $deep <= $k) break;
        $v = intval($v);
        $select .= '<select onchange="load_area(this.value, '.$id.');" '.$extend.'>';
        if($title) $select .= '<option value="'.$v.'">'.$title.'</option>';
        $result = DB::query("SELECT areaid,areaname FROM ".DT_PRE."area WHERE parentid=$v ORDER BY listorder,areaid ASC");
        while($a = DB::fetch_array($result)) {
            $selectid = isset($parents[$k+1]) ? $parents[$k+1] : $areaid;
            $selected = $a['areaid'] == $selectid ? ' selected' : '';
            if($_areaid && !in_array($a['areaid'], $_areaid)) continue;
            $select .= '<option value="'.$a['areaid'].'"'.$selected.'>'.$a['areaname'].'</option>';
        }
        $select .= '</select> ';
    }
    return $select;
}

function get_shuxing_select($title = '', $shuxingid = 0, $extend = '', $deep = 0, $id = 1) {
    var_dump($title,$shuxingid,$extend,$deep,$id);
//    die();
//    $moduleid = $_GET['$moduleid'];
//    var_dump(33);
//    die();


//    echo 88888888;die();
    global $_shuxingid,$module,$moduleid;
//    var_dump($module); die();
//    var_dump($shuxingid);
    echo (5555555);
    var_dump($moduleid);
    $_shuxingid or $_shuxingid = array();
    $parents = array();
    if($shuxingid) {
            var_dump($shuxingid);
    echo (5555555);
    var_dump($moduleid);
//    die();
        $sql1="SELECT child,arrparentid FROM ".DT_PRE.$module."_shuxing_".$moduleid." WHERE shuxingid=$shuxingid";
        $r = DB::get_one($sql1);
        var_dump($sql1);;
//        echo(2222222222222);;
        var_dump($r);;
//        die();
        $parents = explode(',', $r['arrparentid']);
        if($r['child']) $parents[] = $shuxingid;
    } else {
        $parents[] = 0;
    }
    $select = '5555555555';
    foreach($parents as $k=>$v) {
        if($deep && $deep <= $k) break;
        $v = intval($v);
//        $select .= '<select onchange="alert(11)" '.$extend.'>';
        $select .= '<select onchange="load_shuxing(this.value, '.$id.');" '.$extend.'>';
        echo 3333333333388;
        if($title) $select .= '<option value="'.$v.'">'.$title.'</option>';
        $sql1="SELECT shuxingid,shuxingname FROM ".DT_PRE.$module."_shuxing_".$moduleid." WHERE parentid=$v ORDER BY listorder,shuxingid ASC";
        $result = DB::query($sql1);
        var_dump($sql1);
        var_dump($result);
        echo 8888888888888888888;
//        die();
        while($a = DB::fetch_array($result)) {
            $selectid = isset($parents[$k+1]) ? $parents[$k+1] : $shuxingid;
            $selected = $a['shuxingid'] == $selectid ? ' selected' : '';
            if($_shuxingid && !in_array($a['shuxingid'], $_shuxingid)) continue;
            $select .= '<option value="'.$a['shuxingid'].'"'.$selected.'>'.$a['shuxingname'].'</option>';
        }
        $select .= '</select> ';
    }
    return $select;
}















function is_filepath($filepath) {
	return strlen($filepath) > 6 && in_array(file_ext($filepath), array('htm', 'html','shtm', 'shtml'));
}

function is_email($email) {
	if(!preg_match("/^[a-z0-9_\-\.]{1,}@[a-z0-9\-]{1,}\.[a-z0-9\-\.]{1,}$/", $email)) return false;
	list($user, $domain) = explode('@', $email);
	if(strpos($domain, '..') !== false) return false;
	$str = substr($user, 0, 1).substr($user, -1, 1).substr($domain, 0, 1).substr($domain, -1, 1).substr(cutstr($email, '@', '.'), -1, 1);
	if(preg_match("/[_\-\.]/", $str)) return false;
	return true;
}

function is_tel($tel) {
	return preg_match("/^[0-9\-\+\(\)\s]{7,}$/", $tel);
}

function is_qq($qq) {
	return preg_match("/^[1-9]{1}[0-9]{4,12}$/", $qq);
}

function is_wx($wx) {
	return check_name($wx);
}

function is_gzh($gzh) {
	return check_name($gzh);
}

function is_skuid($skuid) {
	return preg_match("/^[0-9a-zA-Z\-_]{2,}$/", $skuid);
}

function is_gbk($string) {
	return preg_match("/^([\s\S]*?)([\x81-\xfe][\x40-\xfe])([\s\S]*?)/", $string);
}

function is_date($date, $sep = '-') {
	if(preg_match("/^[0-9]{8}$/", $date)) $date = substr($date, 0, 4).'-'.substr($date, 4, 2).'-'.substr($date, 6, 2);
	if(strlen($date) > 10 || strlen($date) < 8)  return false;
	list($year, $month, $day) = explode($sep, $date);
	return checkdate($month, $day, $year);
}

function is_time($time, $sep = '-') {
	$time = str_replace('T', ' ', $time);
	if(substr_count($time, ' ') != 1) return false;
	if(substr_count($time, ':') < 1) return false;
	list($date, $time) = explode(' ', $time);
	if(strlen($time) == 5) $time .= ':00';
	if(strlen($time) != 8) return false;
	if(!preg_match("/^[0-2]{1}[0-9]{1}\:[0-5]{1}[0-9]{1}\:[0-5]{1}[0-9]{1}$/", $time)) return false;
	if(intval(substr($time, 0, 2)) > 23) return false;
	if(preg_match("/^[0-9]{8}$/", $date)) $date = substr($date, 0, 4).'-'.substr($date, 4, 2).'-'.substr($date, 6, 2);
	if(strlen($date) > 10 || strlen($date) < 8)  return false;
	list($year, $month, $day) = explode($sep, $date);
	return checkdate($month, $day, $year);
}

function is_image($file) {
	return preg_match("/^(jpg|jpeg|gif|png|bmp)$/i", file_ext($file));
}

function is_user($username) {
	$r = DB::get_one("SELECT username FROM ".DT_PRE."member WHERE username='$username'");
	return $r ? true : false;
}

function is_password($username, $password) {
	if(strlen($password) < 6) return false;
	$r = DB::get_one("SELECT password,passsalt FROM ".DT_PRE."member WHERE username='$username'");
	if(!$r) return false;
	return $r['password'] == dpassword($password, $r['passsalt']);
}

function is_payword($username, $payword) {
	if(strlen($payword) < 6) return false;
	$r = DB::get_one("SELECT payword,paysalt FROM ".DT_PRE."member WHERE username='$username'");
	if(!$r) return false;
	return $r['payword'] == dpassword($payword, $r['paysalt']);
}

function is_crsf($url) {
	if(strpos($url, '://') === false) {
		//
	} else {
		if(!is_uri($url)) return false;
	}
	return preg_match("/(\?|\&|\.php)/i", $url);
}

function dpassword($password, $salt) {
	return md5((is_md5($password) ? md5($password) : md5(md5($password))).$salt);
}

function gb2py($text, $exp = '') {
	if(!$text) return '';
	$text = convert($text, DT_CHARSET, 'GBK');
	$data = array();
	$tmp = @file(DT_ROOT.'/file/table/gb-pinyin.table');
	if(!$tmp) return '';
	$tmps = count($tmp);
	for($i = 0; $i < $tmps; $i++) {
		$tmp1 = explode("	", $tmp[$i]);
		$data[$i]=array($tmp1[0], $tmp1[1]);
	}
	$r = array();
	$k = 0;
	$textlen = strlen($text);
	for($i = 0; $i < $textlen; $i++) {
		$p = ord(substr($text, $i, 1));		
		if($p > 160) {
			$q = ord(substr($text, ++$i, 1));
			$p = $p*256+$q-65536;
		}
        if($p > 0 && $p < 160) {
            $r[$k] = chr($p);
        } elseif($p < -20319 || $p > -10247) {
            $r[$k] = '';
        } else {
            for($j = $tmps - 1; $j >= 0; $j--) {
                if($data[$j][1]<=$p) break;
            }
            $r[$k] = $data[$j][0];
        }
		$k++;
	}
	return implode($exp, $r);
}

function match_userid($file) {
	$name = explode('.', basename($file));
	return intval(substr($name[0], strpos($name[0], '-') === false ? 8 : 12));
}

function clear_img($content) {
	return preg_replace("/<img[^>]*>/i", "", $content);
}

function clear_link($content) {
	return preg_replace_callback("/<a[^>]*>(.*?)<\/a>/is", "_clear_link", $content);
}

function _clear_link($matches) {
	return is_uri($matches[0]) ? $matches[0] : $matches[1];
}

function save_remote($content, $ext = 'jpg|jpeg|gif|png|bmp', $self = 0) {
	global $DT, $_userid, $_username, $moduleid, $itemid, $session;
	if(!$_userid || !$content) return $content;
	if(!preg_match_all("/src=([\"|']?)([^ \"'>]+\.($ext))\\1/i", $content, $matches)) return $content;
	require_once DT_ROOT.'/include/image.class.php';
	$dftp = false;
	if($DT['ftp_remote'] && $DT['remote_url']) {
		require_once DT_ROOT.'/include/ftp.class.php';
		$ftp = new dftp($DT['ftp_host'], $DT['ftp_user'], $DT['ftp_pass'], $DT['ftp_port'], $DT['ftp_path'], $DT['ftp_pasv'], $DT['ftp_ssl']);
		$dftp = $ftp->connected;
	}
	if($DT['uploadlog'] && !is_object($session)) $session = new dsession();
	$urls = $oldpath = $newpath = array();
	$DT['uploaddir'] or $DT['uploaddir'] = 'Ym/d';
	foreach($matches[2] as $k=>$url) {
		if(in_array($url, $urls)) continue;
		$urls[$url] = $url;		
		if(strpos($url, '://') === false) continue;
		if(!$self) {
			if(is_uri($url)) continue;
			if($DT['remote_url'] && strpos($url, $DT['remote_url']) !== false) continue;
		}
		$filedir = 'file/upload/'.timetodate(DT_TIME, $DT['uploaddir']).'/';
		$filepath = DT_PATH.$filedir;
		$fileroot = DT_ROOT.'/'.$filedir;
		$file_ext = file_ext($url);
		$filename = timetodate(DT_TIME, 'His').mt_rand(10, 99).$_userid.'.'.$file_ext;
		$newfile = $fileroot.$filename;
		if(file_copy($url, $newfile)) {
			$file_size = filesize($newfile);
			if($file_size < 1) {
				file_del($newfile);
				continue;
			}
			$img = '';
			if(is_image($newfile)) {
				$img = @getimagesize($newfile);
				if(!$img) {
					file_del($newfile);
					continue;
				}
				if($DT['water_type']) {
					$image = new image($newfile);
					if($DT['water_type'] == 2) {
						$image->waterimage();
					} else if($DT['water_type'] == 1) {
						$image->watertext();
					}
				}
			}
			$oldpath[] = $url;
			$newurl = linkurl($filepath.$filename);
			if($dftp) {
				$exp = explode("file/upload/", $newurl);
				if($ftp->dftp_put($filedir.$filename, $exp[1])) {
					$newurl = $DT['remote_url'].$exp[1];
					$DT['ftp_save'] or file_del($newfile);
				}
			}
			if($DT['uploadlog']) {
				$upload_table = DT_PRE.'upload_'.($_userid%10);
				$_SESSION['uploads'][] = $newurl;
				$img_w = $img ? $img[0] : 0;
				$img_h = $img ? $img[1] : 0;
				DB::query("INSERT INTO {$upload_table} (item,fileurl,filesize,fileext,upfrom,width,height,moduleid,username,ip,addtime,itemid) VALUES ('".md5($newurl)."','$newurl','$file_size','$file_ext','editor','$img_w','$img_h','$moduleid','$_username','".DT_IP."','".DT_TIME."','$itemid')");
			}
			$newpath[] = $newurl;
		}
	}
	unset($matches);
	return str_replace($oldpath, $newpath, $content);
}

function save_local($content) {
	global $DT, $_userid, $_username, $moduleid, $itemid, $session;
	if($content == '<br type="_moz" />') return '';//FireFox
	if($content == '&nbsp;') return '';//Chrome
	if(DT_EDITOR == 'kindeditor') $content = str_replace('" /></embed />', '"></embed>', $content);
	$content = preg_replace("/allowScriptAccess=\"always\"/i", "", $content);
	$content = preg_replace("/allowScriptAccess/i", "allowscr-iptaccess", $content);
	if(!preg_match_all("/src=([\"|']?)([^ \"'>]+)\\1/i", $content, $matches)) return $content;
	foreach($matches[2] as $k=>$url) {
		if(is_crsf($url) && substr($url, 0, 10) != 'data:image') $content = str_replace($url, DT_STATIC.'image/nopic320.png', $content);
	}
	if(strpos($content, 'data:image') === false) return $content;
	require_once DT_ROOT.'/include/image.class.php';
	$dftp = false;
	if($DT['ftp_remote'] && $DT['remote_url']) {
		require_once DT_ROOT.'/include/ftp.class.php';
		$ftp = new dftp($DT['ftp_host'], $DT['ftp_user'], $DT['ftp_pass'], $DT['ftp_port'], $DT['ftp_path'], $DT['ftp_pasv'], $DT['ftp_ssl']);
		$dftp = $ftp->connected;
	}
	if($DT['uploadlog'] && !is_object($session)) $session = new dsession();
	$urls = $oldpath = $newpath = array();
	$DT['uploaddir'] or $DT['uploaddir'] = 'Ym/d';
	foreach($matches[2] as $k=>$url) {
		if(in_array($url, $urls)) continue;
		$urls[$url] = $url;
		if(strpos($url, 'data:image') === false) continue;
		if(strpos($url, ';base64,') === false) continue;
		$t1 = explode(';base64,', $url);
		$t2 = explode('/', $t1[0]);
		$file_ext = $t2[1];
		in_array($file_ext, array('jpg', 'jpeg', 'gif', 'png')) or $file_ext = 'jpg';
		$filedir = 'file/upload/'.timetodate(DT_TIME, $DT['uploaddir']).'/';
		$filepath = DT_PATH.$filedir;
		$fileroot = DT_ROOT.'/'.$filedir;
		$filename = timetodate(DT_TIME, 'His').mt_rand(10, 99).$_userid.'.'.$file_ext;
		$newfile = $fileroot.$filename;
		if(!is_image($newfile)) continue;
		if(file_put($newfile, base64_decode(strip_sql($t1[1], 0)))) {
			$file_size = filesize($newfile);
			if($file_size < 1) {
				file_del($newfile);
				continue;
			}
			$img = @getimagesize($newfile);
			if(!$img) {
				file_del($newfile);
				continue;
			}
			if($DT['water_type']) {
				$image = new image($newfile);
				if($DT['water_type'] == 2) {
					$image->waterimage();
				} else if($DT['water_type'] == 1) {
					$image->watertext();
				}
			}
			$oldpath[] = $url;
			$newurl = linkurl($filepath.$filename);
			if($dftp) {
				$exp = explode("file/upload/", $newurl);
				if($ftp->dftp_put($filedir.$filename, $exp[1])) {
					$newurl = $DT['remote_url'].$exp[1];
					$DT['ftp_save'] or file_del($newfile);
				}
			}
			if($DT['uploadlog']) {
				$upload_table = DT_PRE.'upload_'.($_userid%10);
				$_SESSION['uploads'][] = $newurl;
				$img_w = $img ? $img[0] : 0;
				$img_h = $img ? $img[1] : 0;
				DB::query("INSERT INTO {$upload_table} (item,fileurl,filesize,fileext,upfrom,width,height,moduleid,username,ip,addtime,itemid) VALUES ('".md5($newurl)."','$newurl','$file_size','$file_ext','editor','$img_w','$img_h','$moduleid','$_username','".DT_IP."','".DT_TIME."','$itemid')");
			}
			$newpath[] = $newurl;
		}
	}
	unset($matches);
	return str_replace($oldpath, $newpath, $content);
}

function save_thumb($content, $no, $width = 120, $height = 90) {
	global $DT, $_userid;
	if(!$_userid || !$content) return '';
	$ext = 'jpg|jpeg|gif|png|bmp';
	if(!preg_match_all("/src=([\"|']?)([^ \"'>]+\.($ext))\\1/i", $content, $matches)) return '';
	require_once DT_ROOT.'/include/image.class.php';
	$dftp = false;
	if($DT['ftp_remote'] && $DT['remote_url']) {
		require_once DT_ROOT.'/include/ftp.class.php';
		$ftp = new dftp($DT['ftp_host'], $DT['ftp_user'], $DT['ftp_pass'], $DT['ftp_port'], $DT['ftp_path'], $DT['ftp_pasv'], $DT['ftp_ssl']);
		$dftp = $ftp->connected;
	}
	$urls = $oldpath = $newpath = array();
	$DT['uploaddir'] or $DT['uploaddir'] = 'Ym/d';
	foreach($matches[2] as $k=>$url) {
		if($k == $no - 1) {
			$filedir = 'file/upload/'.timetodate(DT_TIME, $DT['uploaddir']).'/';
			$filepath = DT_PATH.$filedir;
			$fileroot = DT_ROOT.'/'.$filedir;
			$file_ext = file_ext($url);
			$filename = timetodate(DT_TIME, 'His').mt_rand(10, 99).$_userid.'.'.$file_ext;
			$newfile = $fileroot.$filename;
			if(file_copy($url, $newfile)) {
				if(is_image($newfile)) {					
					if(!@getimagesize($newfile)) {
						file_del($newfile);
						return '';
					}
					$image = new image($newfile);
					$image->thumb($width, $height);
				}
				$newurl = linkurl($filepath.$filename);
				if($dftp) {
					$exp = explode("file/upload/", $newurl);
					if($ftp->dftp_put($filedir.$filename, $exp[1])) {
						$newurl = $DT['remote_url'].$exp[1];
						$DT['ftp_save'] or file_del($newfile);
					}
				}
				return $newurl;
			}
		}
	}
	unset($matches);
	return '';
}

function save_poster($video, $poster) {
	global $DT;
	if(is_uri($video) && file_ext($video) == 'mp4' && is_uri($poster) && is_image($poster)) {
		$poster = cutstr($poster, '', '.thumb.');
		if(strpos($poster, DT_PATH.'file/upload/') !== false) {
			$pfile = str_replace(DT_PATH.'file/upload/', '', $poster);
		} else if($DT['remote_url'] && strpos($poster, $DT['remote_url']) !== false) {
			$pfile = str_replace($DT['remote_url'], '', $poster);
		} else {
			return;
		}
		if(strpos($video, DT_PATH.'file/upload/') !== false) {
			$vfile = str_replace(DT_PATH.'file/upload/', '', $video);
		} else if($DT['remote_url'] && strpos($video, $DT['remote_url']) !== false) {
			$vfile = str_replace($DT['remote_url'], '', $video);
		} else {
			return;
		}
		$tfile = str_replace('.mp4', '.jpg', $vfile);
		if(is_file(DT_ROOT.'/file/upload/'.$pfile)) {
			file_copy(DT_ROOT.'/file/upload/'.$pfile, DT_ROOT.'/file/upload/'.$tfile);
		} else {
			file_copy($poster, DT_ROOT.'/file/upload/'.$tfile);
		}
		if($DT['ftp_remote'] && $DT['remote_url'] && strpos($video, $DT['remote_url']) !== false && is_file(DT_ROOT.'/file/upload/'.$tfile)) {
			require_once DT_ROOT.'/include/ftp.class.php';
			$ftp = new dftp($DT['ftp_host'], $DT['ftp_user'], $DT['ftp_pass'], $DT['ftp_port'], $DT['ftp_path'], $DT['ftp_pasv'], $DT['ftp_ssl']);
			$ftp->dftp_put(DT_ROOT.'/file/upload/'.$tfile, $tfile, 0);
		}
	}
}

function delete_local($content, $userid, $itemid = 0, $ext = 'jpg|jpeg|gif|png|bmp|mp4|swf') {
	if(preg_match_all("/src=([\"|']?)([^ \"'>]+\.($ext))\\1/i", $content, $matches)) {
		foreach($matches[2] as $url) {
			delete_upload($url, $userid, $itemid);
		}
	}
}

function delete_diff($new, $old, $itemid = 0, $ext = 'jpg|jpeg|gif|png|bmp|mp4|swf') {
	global $_userid;
	$new = stripslashes($new);
	$diff_urls = $new_urls = $old_urls = array();
	if(preg_match_all("/src=([\"|']?)([^ \"'>]+\.($ext))\\1/i", $old, $matches)) {
		foreach($matches[2] as $url) {
			$old_urls[] = $url;
		}
	} else {
		return;
	}
	if(preg_match_all("/src=([\"|']?)([^ \"'>]+\.($ext))\\1/i", $new, $matches)) {
		foreach($matches[2] as $url) {
			$new_urls[] = $url;
		}
	}
	foreach($old_urls as $url) {
		in_array($url, $new_urls) or $diff_urls[] = $url;
	}
	if(!$diff_urls) return;
	foreach($diff_urls as $url) {
		delete_upload($url, $_userid, $itemid);
	}
}

function delete_upload($file, $userid, $itemid = 0) {
	global $CFG, $DT, $ftp;
	if(!defined('DT_ADMIN') && (!$userid || $userid != match_userid($file))) return false;
	if(!$file) return false;
	if($DT['uploadlog'] && $itemid) {
		$t = DB::get_one("SELECT itemid FROM ".DT_PRE."upload_".($userid%10)." WHERE item='".md5($file)."'");
		if($t && $t['itemid'] && $t['itemid'] != $itemid) return false;
	}
	$thumb = strpos($file, '.thumb.') !== false ? 1 : 0;
	$ext = file_ext($file);
	$len = strlen($ext);
	$fileurl = $file;
	if(strpos($file, 'file/upload') === false) {
		if($DT['ftp_remote'] && $DT['remote_url']) {
			if(strpos($file, $DT['remote_url']) !== false) {
				if(!is_object($ftp)) {
					require_once DT_ROOT.'/include/ftp.class.php';
					$ftp = new dftp($DT['ftp_host'], $DT['ftp_user'], $DT['ftp_pass'], $DT['ftp_port'], $DT['ftp_path'], $DT['ftp_pasv'], $DT['ftp_ssl']);
				}
				$file = str_replace($DT['remote_url'], '', $file);
				$ftp->dftp_delete($file);
				file_del(DT_ROOT.'file/upload/'.$file);
				if($thumb) {
					$ftp->dftp_delete(str_replace('.thumb.'.$ext, '', $file));
					if($DT['ftp_save']) file_del(DT_ROOT.'file/upload/'.str_replace('.thumb.'.$ext, '', $file));
					$ftp->dftp_delete(str_replace('.thumb.', '.middle.', $file));
					if($DT['ftp_save']) file_del(DT_ROOT.'file/upload/'.str_replace('.thumb.', '.middle.', $file));
				} else {
					$ftp->dftp_delete($file.'.thumb.'.$ext);
					if($DT['ftp_save']) file_del(DT_ROOT.'file/upload/'.$file.'.thumb.'.$ext);
					$ftp->dftp_delete($file.'.middle.'.$ext);
					if($DT['ftp_save']) file_del(DT_ROOT.'file/upload/'.$file.'.middle.'.$ext);
				}
				if($ext == 'mp4') {
					file_del(DT_ROOT.'file/upload/'.str_replace('.mp4', '.jpg', $file));
					$ftp->dftp_delete(str_replace('.mp4', '.jpg', $file));
				}
				if($DT['convert']) {
					include DT_ROOT.'/file/config/convert.inc.php';
					$name = substr($file, 0, -$len);
					foreach($CV as $k=>$v) {
						if($v == $ext) {
							$cext = $v;
						} else if($k == $ext) {
							$cext = $k;
						} else {
							continue;
						}
						$ftp->dftp_delete($name.$cext);
						if($DT['ftp_save']) file_del(DT_ROOT.'file/upload/'.$name.$cext);
						$curl = $DT['remote_url'].$name.$cext;
						DB::query("DELETE FROM ".DT_PRE."upload_convert WHERE fileurl='".$curl."'");
					}
				}
			}
		}
	} else {
		$name = cutstr($file, '/file/upload/', '');
		$file = DT_ROOT.'/file/upload/'.$name;
		if(is_file($file) && strpos($name, '..') === false) {
			file_del($file);
			if($thumb) {
				file_del(str_replace('.thumb.'.$ext, '', $file));
				file_del(str_replace('.thumb.', '.middle.', $file));
			} else {
				file_del($file.'.thumb.'.$ext);
				file_del($file.'.middle.'.$ext);
			}
			if($ext == 'mp4') file_del(str_replace('.mp4', '.jpg', $file));
		}
		if($DT['convert']) {
			include DT_ROOT.'/file/config/convert.inc.php';
			$name = substr($file, 0, -$len);
			foreach($CV as $k=>$v) {
				if($v == $ext) {
					$cext = $v;
				} else if($k == $ext) {
					$cext = $k;
				} else {
					continue;
				}
				if(is_file($name.$cext)) {
					file_del($name.$cext);
					$curl = DT_PATH.'file/upload/'.cutstr($name, '/file/upload/', '').$cext;
					DB::query("DELETE FROM ".DT_PRE."upload_convert WHERE fileurl='".$curl."'");
				}
			}
		}
	}
	if($DT['uploadlog']) {
		$picurl = $thumb ? str_replace('.thumb.'.$ext, '', $fileurl) : $fileurl.'.thumb.'.$ext;
		DB::query("DELETE FROM ".DT_PRE."upload_".($userid%10)." WHERE item='".md5($fileurl)."'");
		DB::query("DELETE FROM ".DT_PRE."upload_".($userid%10)." WHERE item='".md5($picurl)."'");
	}
}
//删除此用户没有提交的临时图片
function clear_upload($content = '', $itemid = 0, $tb = '') {
//    print_r($content);
//    print_r($content,$itemid);
//    var_dump($itemid);
//    print_r(0);
//    die();
	global $DT, $session, $_userid;
	if(!is_object($session)) $session = new dsession();
//    print_r($_SESSION['uploads']);
//    die();

//    if(!isset($_SESSION['uploads']) || !$_SESSION['uploads'] || !$content) {
//        var_dump($_SESSION['uploads']);
//        var_dump(!isset($_SESSION['uploads']));
//        var_dump(!$_SESSION['uploads']);
//        var_dump(!$content);
//        var_dump(!isset($_SESSION['uploads']) || !$_SESSION['uploads'] || !$content);
//        die();
//    };
	if(!isset($_SESSION['uploads']) || !$_SESSION['uploads'] || !$content) return;

	$update = array();
	foreach($_SESSION['uploads'] as $file) {
//        print_r($_SESSION['uploads']);die();
		$ext = file_ext($file);
		$pic = strpos($file, '.thumb.') !== false ? str_replace('.thumb.'.$ext, '', $file) : $file.'.thumb.'.$ext;
		if(strpos($content, $file) === false && strpos($content, $pic) === false) {
			delete_upload($file, $_userid);
		} else {
			if($DT['uploadlog'] && $itemid) { $update[] = "'".md5($file)."'"; $update[] = "'".md5($pic)."'"; }
		}
	}
	$tb = $tb ? str_replace(DT_PRE, '', $tb) : '';
	if($update) DB::query("UPDATE ".DT_PRE."upload_".($_userid%10)." SET itemid=$itemid".($tb ? ",tb='$tb'" : "")." WHERE item IN (".implode(',', $update).")");
	$_SESSION['uploads'] = array();
}

function check_period($period) {
	if($period) {
		if(strpos($period, ',') === false) {
			$period = explode('|', $period);
			foreach($period as $p) {
				$p = str_replace(':', '.', $p);
				$p = explode('-', $p);
				$f = $p[0];
				$t = $p[1];
				$n = date('G.i', DT_TIME);
				if(($f > $t && ($n > $f || $n < $t)) || ($f < $t && $n > $f && $n < $t)) return true;
			}
			return false;
		} else {
			return strpos(','.$period.',', ','.date('w', DT_TIME).',') === false ? false : true;
		}
	} else {
		return false;
	}
}

function get_status($status, $check) {
	global $DT;
	if(!$check && $DT['check_week'] && check_period($DT['check_week'])) $check = true;
	if(!$check && $DT['check_hour'] && check_period($DT['check_hour'])) $check = true;
	if($status == 0) {
		return 0;
	} else if($status == 1) {
		return 2;
	} else if($status == 2) {
		return 2;
	} else if($status == 3) {
		return $check ? 2 : 3;
	} else if($status == 4) {
		return $check ? 2 : 3;
	} else {
		return 2;
	}
}

function hide_str($str) {
	$len = strlen($str);
	if($len < 3 || is_gbk($str)) return $str;
	if(is_mobile($str)) {
		return substr($str, 0, 3).'****'.substr($str, -4);
	} else if(is_email($str)) {
		list($name, $domain) = explode('@', $str);
		return hide_str($name).'@'.$domain;
	} else {
		$num = ceil($len/4);
		$tmp = $str;
		$str = '';
		for($i = 0; $i < $len; $i++) {
			$str .= ($i > $num - 1 && $i < $len - $num) ? '*' : $tmp[$i];
		}
	}
	return $str;
}

function tips($tips) {
	$tips = str_replace(array('<br/>', '<br />', '<br>'), array('&#10;', '&#10;', '&#10;'), $tips);
	echo ' <img src="'.DT_STATIC.'image/tips.png" width="16" height="16" title="'.strip_tags($tips).'" alt="tips" class="c_p" onclick="Dconfirm(\''.str_replace('&#10;', '<br/>', $tips).'\', \'\', 450);"/>';
}

function reload_captcha() {
	return 'try{parent.reloadcaptcha();}catch(e){}';
}

function reload_question() {
	return 'try{parent.reloadquestion();}catch(e){}';
}

function sync_weibo($site, $moduleid, $itemid) {
	return 'document.write(\'<img src="'.DT_PATH.'api/oauth/'.$site.'/post'.DT_EXT.'?auth='.encrypt($moduleid.'-'.$itemid, DT_KEY.'SYNC').'" width="1" height="1"/>\');';
}

function history($moduleid, $itemid, $action = '', $item = array()) {
	$file = DT_ROOT.'/file/history/'.$moduleid.'/'.$itemid.'.php';
	if($action == 'set') {
		file_put($file, '<?php exit;?>'.serialize($item));
	} else if($action == 'del') {
		file_del($file);
	} else {
		return is_file($file);
	}
}
?>