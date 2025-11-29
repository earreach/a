<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
if($_cid) dheader('child.php');
switch($action) {
	case 'logout':
		if($admin_user) set_cookie('admin_user', '');
		dmsg($L['index_msg_logout'], '?reload='.$DT_TIME);
	break;
	case 'note':
		if(word_count($note) > 5000) message($L['index_msg_note_limit']);
		$note = '<?php exit;?>'.dhtmlspecialchars(stripslashes($note));
		file_put(DT_ROOT.'/file/user/'.dalloc($_userid).'/'.$_userid.'/note.php', $note);
		dmsg($L['op_edit_success'], '?reload='.$DT_TIME);
	break;
	default:
		$mynote = DT_ROOT.'/file/user/'.dalloc($_userid).'/'.$_userid.'/note.php';
		$mynote = file_get($mynote);
		if($mynote) {
			$mynote = substr($mynote, 13);
		} else {
			$mynote = $MOD['usernote'];
		}
		$line = substr_count($mynote, "\n");
		$line = $line > 3 ? $line*24 : 0;
		if($DT_PC) {
			$user = userinfo($_username);
			extract($user);
			$expired = $totime && $totime < $DT_TIME ? true : false;
			$days = $expired ? 0 : ceil(($totime - $DT_TIME)/86400);
			$t = explode('.', $_money);
			$my_money = number_format($t[0]).'<span>.'.$t[1].'</span>';
			$head_title = '';
		} else {
			$head_title = $head_name = $MOD['name'];
			$foot = 'my';
		}
		include template('index', $module);
	break;
}
?>