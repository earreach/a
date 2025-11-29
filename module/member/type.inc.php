<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
isset($item) or message();
$names = $L['type_names'];
isset($names[$item]) or message();
$could_child = in_array($item, array('friend', 'favorite', 'follow', 'fans')) ? 0 : 1;
if($mid > 4 && !in_array($MODULE[$mid]['module'], array('sell', 'mall'))) $mid = 0;
require DT_ROOT.'/include/type.class.php';
$do = new dtype;
$do->item = $item.($mid > 4 ? '-'.$mid : '').'-'.$_userid;
$TYPE = $do->get_list();
switch($action) {
	case 'update':
		if($MG['type_limit'] && $post[0]['typename'] && count($TYPE) > $MG['type_limit']) dalert(lang($L['type_msg_limit'], array($MG['type_limit'])), 'goback');
		$do->update($post);
		dmsg($L['op_update_success'], '?item='.$item.'&mid='.$mid);
	break;
	case 'delete':
		$itemid or msg($L['choose_type']);
		if(is_array($itemid)) {
			foreach($itemid as $typeid) {
				$do->delete($typeid);
			}
		} else {
			$do->delete($itemid);
		}
		dmsg($L['op_del_success'], '?item='.$item.'&mid='.$mid);
	break;
	default:
		$head_title = lang($L['type_title'], array($names[$item]));
		$types = $TYPE;
		$parent_option = '<option value="0">'.$L['type_parent'].'</option>'.$do->parent_option($TYPE);
		$parent_select = '<select name="post[0][parentid]">'.$parent_option .'</select>';
		foreach($types as $k=>$v) {
			$types[$k]['style_select'] = dstyle('post['.$v['typeid'].'][style]', $v['style']);
			$types[$k]['parent_select'] = '<select name="post['.$v['typeid'].'][parentid]">'.str_replace('"'.$v['parentid'].'"', '"'.$v['parentid'].'" selected', $parent_option).'</select>';
		}
		$new_style = dstyle('post[0][style]');
		$lists = sort_type($types);
	break;
}

if($DT_PC) {
	//
} else {
	if((!$action || $action == 'index') && !$kw) $back_link = $MODULE[2]['mobile'].($_cid ? 'child.php' : '');
	$head_name = $head_title;
}
include template('type', $module);
?>