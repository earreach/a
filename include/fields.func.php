<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function fields_update($post_fields, $table, $itemid, $keyname = 'itemid', $fd = array()) {
	global $FD;
	if(!$table || !$itemid) return '';
	if($fd) $FD = $fd;
	$sql = '';
	foreach($FD as $k=>$v) {
		if(isset($post_fields[$v['name']]) || $v['html'] == 'checkbox') {
			$mk = $v['name'];
			$mv = $post_fields[$v['name']];
			if($v['html'] == 'checkbox' && is_array($post_fields[$v['name']]) && $post_fields[$v['name']]) $mv = implode(',', $post_fields[$v['name']]).',';			
			$mv = $v['html'] == 'editor' ? addslashes(dsafe(save_remote(save_local(stripslashes($mv))))) : dhtmlspecialchars(trim($mv));
			$sql .= ",$mk='$mv'";
		}
	}
	$sql = substr($sql, 1);
	if($sql) DB::query("UPDATE {$table} SET $sql WHERE `$keyname`=$itemid");
}

function fields_check($post_fields, $fd = array()) {
	global $FD, $session;
	include load('include.lang');
	if($fd) $FD = $fd;
	if(!is_object($session)) $session = new dsession();
	$uploads = isset($_SESSION['uploads']) ? $_SESSION['uploads'] : array();
	foreach($FD as $k=>$v) {
		$value = isset($post_fields[$v['name']]) ? $post_fields[$v['name']] : '';
		if(in_array($v['html'], array('thumb', 'file', 'editor')) && $uploads) {
			foreach($uploads as $sk=>$sv) {
				if($v['html'] == 'editor') {
					if(strpos($value, $sv) !== false) unset($_SESSION['uploads'][$sk]);
				} else {
					if($sv == $value) unset($_SESSION['uploads'][$sk]);
				}
			}
		}
		if(!$v['input_limit']) continue;
		if(!defined('DT_ADMIN') && !$v['front']) continue;
		if($v['input_limit'] == 'is_date') {
			if(!is_date($value)) fields_message(lang($L['fields_input'], array($v['title'])));
		} else if($v['input_limit'] == 'is_time') {
			if(!is_time($value)) fields_message(lang($L['fields_input'], array($v['title'])));
		} else if($v['input_limit'] == 'is_email') {
			if(!is_email($value)) fields_message(lang($L['fields_valid'], array($v['title'])));
		} else if(is_numeric($v['input_limit'])) {
			$length = $value ? ($v['html'] == 'checkbox' ? count($value) : word_count($value)) : 0;
			if($length < $v['input_limit']) fields_message(lang($L['fields_less'], array($v['title'], $v['input_limit'])));
		} else if(preg_match("/^([0-9]{1,})\-([0-9]{1,})$/", $v['input_limit'], $m)) {			
			$length = $value ? ($v['html'] == 'checkbox' ? count($value) : word_count($value)) : 0;
			if($m[1] && $length < $m[1]) fields_message(lang($L['fields_less'], array($v['title'], $m[1])));
			if($m[2] && $length > $m[2]) fields_message(lang($L['fields_more'], array($v['title'], $m[2])));
		} else {
			if(!preg_match("/^".$v['input_limit']."$/", $value)) fields_message(lang($L['fields_match'], array($v['title'])));
		}
	}
}

function fields_js($fd = array()) {
	global $FD;
	if($fd) $FD = $fd;
	$js = '';
	include load('include.lang');
	foreach($FD as $k=>$v) {
		if(!$v['input_limit']) continue;
		if(!defined('DT_ADMIN') && !$v['front']) continue;
		if($v['input_limit'] == 'is_date') {
			$js .= 'f = "post_fields'.$v['name'].'";l = Dd(f).value.length;';
			$js .= 'if(l != 10) {Dmsg("'.lang($L['fields_input'], array($v['title'])).'", f, 1);return false;}';
		} else if($v['input_limit'] == 'is_time') {
			$js .= 'f = "post_fields'.$v['name'].'";l = Dd(f).value.length;';
			$js .= 'if(l > 19 || l > 16) {Dmsg("'.lang($L['fields_input'], array($v['title'])).'", f, 1);return false;}';
		} else if($v['input_limit'] == 'is_email') {
			$js .= 'f = "'.$v['name'].'";l = Dd(f).value.length;';
			$js .= 'if(l < 8) {Dmsg("'.lang($L['fields_input'], array($v['title'])).'", f);return false;}';
		} else if(is_numeric($v['input_limit'])) {
			if($v['html'] == 'area') {
				$js .= 'f = "'.$v['name'].'";l = Dd("areaid_1").value;';
				$js .= 'if(l == 0) {Dmsg("'.lang($L['fields_area']).'", f, 1);return false;}';
			} else if($v['html'] == 'checkbox') {
				$js .= 'f = "'.$v['name'].'";l = checked_count(f);';
				$js .= 'if(l < '.$v['input_limit'].') {Dmsg("'.lang($L['fields_less'], array($v['title'], $v['input_limit'])).'", f, 1);return false;}';
			} else {
				$js .= 'f = "'.$v['name'].'";l = Dd(f).value.length;';
				$js .= 'if(l < '.$v['input_limit'].') {Dmsg("'.lang($L['fields_less'], array($v['title'], $v['input_limit'])).'", f);return false;}';
			}
		} else if(preg_match("/^([0-9]{1,})\-([0-9]{1,})$/", $v['input_limit'], $m)) {
			if($v['html'] == 'checkbox') {
				$js .= 'f = "'.$v['name'].'";l = checked_count(f);';
				if($m[1]) $js .= 'if(l < '.$m[1].') {Dmsg("'.lang($L['fields_less'], array($v['title'], $m[1])).'", f, 1);return false;}';
				if($m[2]) $js .= 'if(l > '.$m[2].') {Dmsg("'.lang($L['fields_more'], array($v['title'], $m[2])).'", f, 1);return false;}';
			} else {
				$js .= 'f = "'.$v['name'].'";l = Dd(f).value.length;';
				if($m[1]) $js .= 'if(l < '.$m[1].') {Dmsg("'.lang($L['fields_less'], array($v['title'], $m[1])).'", f);return false;}';
				if($m[2]) $js .= 'if(l > '.$m[2].') {Dmsg("'.lang($L['fields_more'], array($v['title'], $m[2])).'", f);return false;}';
			}
		} else {
			$js .= 'f = "'.$v['name'].'";l = Dd(f).value;';
			$js .= 'if(l.match(/^'.$v['input_limit'].'$/) == null) {Dmsg("'.lang($L['fields_match'], array($v['title'])).'", f);return false;}';
		}
	}
	return $js;
}

function fields_html($left = '<td class="tl">', $right = '<td>', $values = array(), $fd = array()) {
	extract($GLOBALS, EXTR_SKIP);
	if($fd) $FD = $fd;
	$html = '';
	foreach($FD as $k=>$v) {
		if(!$v['display']) continue;
		if(!defined('DT_ADMIN') && !$v['front']) continue;
		$html .= fields_show($k, $left, $right, $values, $fd);
	}
	return $html;
}

function fields_show($itemid, $left = '<td class="tl">', $right = '<td>', $values = array(), $fd = array()) {
	extract($GLOBALS, EXTR_SKIP);
	if($fd) $FD = $fd;
	if(!$values) {
		if(isset($item)) $values = $item;
		if(isset($user)) $values = $user;
	}
	$html = '';
	$v = $FD[$itemid];
	$value = $v['default_value'];
	$did = 'd'.$v['name'];
	if(isset($values[$v['name']]) && strlen($values[$v['name']]) > 0) {
		$value = $values[$v['name']];
	} else if($v['default_value'] && substr($v['default_value'], 0, 1) == '$') {
		eval('$value = "'.$v['default_value'].'";');
	}
	if($v['html'] == 'hidden') {
		$html .= '<input type="hidden" name="post_fields['.$v['name'].']" id="'.$v['name'].'" value="'.$value.'" '.$v['addition'].'/>';
	} else {
		if($DT_PC) {
			$html .= '<tr>'.$left;
			if($v['input_limit']) {
				$html .= '<span class="f_red">*</span> ';
			} else {
				$html .= defined('DT_ADMIN') ? '<span class="f_hid">*</span> ' : '';
			}
			$html .= $v['title'];
			$html .= '</td>';
			$html .= $right;
		} else {
			$html .= '<p>'.$v['title'];
			if($v['input_limit']) $html .= '<em>*</em>';
			$html .= '<b id="'.$did.'"></b></p>';
			$html .=  $v['html'] == 'editor' ? '' : '<div>';
		}
		switch($v['html']) {
			case 'text':
				$html .= '<input type="text" name="post_fields['.$v['name'].']" id="'.$v['name'].'" value="'.$value.'" '.$v['addition'].'/>';
			break;
			case 'textarea':
				$html .= '<textarea name="post_fields['.$v['name'].']" id="'.$v['name'].'" '.$v['addition'].'>'.$value.'</textarea>';
			break;
			case 'select':
				if($v['option_value']) {
					$html .= '<select name="post_fields['.$v['name'].']" id="'.$v['name'].'" '.$v['addition'].'><option value="">'.$L['choose'].'</option>';
					$rows = explode("*", $v['option_value']);
					foreach($rows as $row) {
						if($row) {
							$cols = explode("|", trim($row));
							$html .= '<option value="'.$cols[0].'"'.($cols[0] == $value ? ' selected' : '').'>'.$cols[1].'</option>';
						}
					}
					$html .= '</select>';
				}
			break;
			case 'radio':
				if($v['option_value']) {
					$html .= '<span id="'.$v['name'].'">';
					$rows = explode("*", $v['option_value']);
					foreach($rows as $rw => $row) {
						if($row) {
							$cols = explode("|", trim($row));
							$html .= '<label><input type="radio" name="post_fields['.$v['name'].']" value="'.$cols[0].'" id="'.$v['name'].'_'.$rw.'"'.($cols[0] == $value ? ' checked' : '').'> '.$cols[1].'</label>&nbsp;&nbsp;&nbsp;';
						}
					}
					$html .= '</span>';
				}
			break;
			case 'checkbox':
				if($v['option_value']) {
					$html .= '<span id="'.$v['name'].'">';
					$value = explode(',', $value);
					$rows = explode("*", $v['option_value']);
					foreach($rows as $rw => $row) {
						if($row) {
							$cols = explode("|", trim($row));
							$html .= '<label><input type="checkbox" name="post_fields['.$v['name'].'][]" value="'.$cols[0].'" id="'.$v['name'].'_'.$rw.'"'.(in_array($cols[0], $value) ? ' checked' : '').'> '.$cols[1].'</label>&nbsp;&nbsp;&nbsp;';
						}
					}
					$html .= '</span>';
				}
			break;
			case 'date':
				if($DT_PC) {
					$html .= dcalendar('post_fields['.$v['name'].']', $value);
					$did = 'post_dfields'.$v['name'];
				} else {
					$html .= '<input type="date" name="post_fields['.$v['name'].']" id="'.$v['name'].'" value="'.$value.'" '.$v['addition'].'/>';
				}
			break;
			case 'time':
				if($DT_PC) {
					$html .= dcalendar('post_fields['.$v['name'].']', $value, '-', 1);
					$did = 'post_dfields'.$v['name'];
				} else {
					$html .= '<input type="datetime-local" name="post_fields['.$v['name'].']" id="'.$v['name'].'" value="'.$value.'" '.$v['addition'].'/>';
				}
			break;
			case 'area':
				$html .= ajax_area_select('post_fields['.$v['name'].']', $GLOBALS['L']['choose'], $value);
			break;
			case 'thumb':
				if($DT_PC) {
					$html .= '<input name="post_fields['.$v['name'].']" type="text" size="70" id="'.$v['name'].'" value="'.$value.'" '.$v['addition'].'/> <span class="upl"><img src="'.DT_STATIC.'image/ico-upl.png" title="'.$L['upload'].'" onclick="Dthumb('.$moduleid.','.$v['width'].','.$v['height'].', Dd(\''.$v['name'].'\').value,\''.(defined('DT_ADMIN') ? '' : '1').'\',\''.$v['name'].'\');"/> <img src="'.DT_STATIC.'image/ico-view.png" title="'.$L['preview'].'" onclick="_preview(Dd(\''.$v['name'].'\').value);"/> <img src="'.DT_STATIC.'image/ico-del.png" title="'.$L['delete'].'" onclick="Dd(\''.$v['name'].'\').value=\'\';"/></span><span id="d'.$v['name'].'" class="f_red"></span>';
				} else {
					$html .= '<input type="url" name="post_fields['.$v['name'].']" id="'.$v['name'].'" value="'.$value.'" '.$v['addition'].'/>';
					$html .= '</div><div class="ui-form-file-upload"><div id="'.$v['name'].'-picker"></div></div>';
					$html .= '<script type="text/javascript">';
					$html .= 'var file_'.$v['name'].' = WebUploader.create({';
					$html .= '	auto: true,';
					$html .= '	server: UPPath+\'?moduleid='.$moduleid.'&action=webuploader&from=thumb&width='.$v['width'].'&height='.$v['height'].'\',';
					$html .= '	pick: \'#'.$v['name'].'-picker\',';
					$html .= '	accept: {';
					$html .= '		title: \'Images\',';
					$html .= '		extensions: \'jpg,jpeg,png,gif,bmp\',';
					$html .= '		mimeTypes: \'image/*\'';
					$html .= '	},';
					$html .= '	resize: false';
					$html .= '});';
					$html .= 'file_'.$v['name'].'.on(\'beforeFileQueued\', function(file) {';
					$html .= '	var exts = fileu.options.accept[0].extensions;';
					$html .= '	if((\',\'+exts).indexOf(\',\'+ext(file.name)) == -1) {';
					$html .= '		alert(L[\'upload_ext\']+ext(file.name)+\' \'+L[\'upload_allow\']+exts);';
					$html .= '		return false;';
					$html .= '	}';
					$html .= '});';
					$html .= 'file_'.$v['name'].'.on(\'fileQueued\', function(file) {';
					$html .= '	Dtoast(\''.$js_pageid.'\',L[\'uploading\'], \'\', 30);';
					$html .= '});';
					$html .= 'file_'.$v['name'].'.on(\'uploadProgress\', function(file, percentage) {';
					$html .= '	var p = parseInt(percentage * 100);';
					$html .= '	$(\'#toast-'.$js_pageid.'\').html(p > 99 ? L[\'processing\'] : L[\'uploading\']+p+\'%\');';
					$html .= '});';
					$html .= 'file_'.$v['name'].'.on(\'uploadSuccess\', function(file, data) {';
					$html .= '	if(data.error) {';
					$html .= '		Dtoast(\''.$js_pageid.'\',data.message, \'\', 5);';
					$html .= '	} else {';
					$html .= '		$(\'#'.$v['name'].'\').val(data.url);';
					$html .= '	}';
					$html .= '});';
					$html .= 'file_'.$v['name'].'.on(\'uploadError\', function(file, data) {';
					$html .= '	Dtoast(\''.$js_pageid.'\',data.message, \'\', 5);';
					$html .= '});';
					$html .= 'file_'.$v['name'].'.on(\'uploadComplete\', function(file) {';
					$html .= '	$(\'#toast-'.$js_pageid.'\').hide();';
					$html .= '});';
					$html .= '</script>';
					return $html;
				}
			break;
			case 'file':
				if($DT_PC) {
					$html .= '<input name="post_fields['.$v['name'].']" type="text" size="70" id="'.$v['name'].'" value="'.$value.'" '.$v['addition'].'/> <span class="upl"><img src="'.DT_STATIC.'image/ico-upl.png" title="'.$L['upload'].'" onclick="Dfile('.$moduleid.', Dd(\''.$v['name'].'\').value, \''.$v['name'].'\', \''.$DT['uploadtype'].'\');"/> <img src="'.DT_STATIC.'image/ico-view.png" title="'.$L['preview'].'" onclick="_preview(Dd(\''.$v['name'].'\').value);"/> <img src="'.DT_STATIC.'image/ico-del.png" title="'.$L['delete'].'" onclick="Dd(\''.$v['name'].'\').value=\'\';"/></span><span id="d'.$v['name'].'" class="f_red"></span>';
				} else {
					$html .= '<input type="url" name="post_fields['.$v['name'].']" id="'.$v['name'].'" value="'.$value.'" '.$v['addition'].'/>';
					$html .= '</div><div class="ui-form-file-upload"><div id="'.$v['name'].'-picker"></div></div>';
					$html .= '<script type="text/javascript">';
					$html .= 'var file_'.$v['name'].' = WebUploader.create({';
					$html .= '	auto: true,';
					$html .= '	server: UPPath+\'?moduleid='.$moduleid.'&action=webuploader&from=file\',';
					$html .= '	pick: \'#'.$v['name'].'-picker\',';
					$html .= '	accept: {';
					$html .= '		title: \'Files\',';
					$html .= '		extensions: \''.str_replace('|', ',', $DT['uploadtype']).'\',';
					$html .= '		mimeTypes: \'*/*\'';
					$html .= '	},';
					$html .= '	resize: false';
					$html .= '});';
					$html .= 'file_'.$v['name'].'.on(\'beforeFileQueued\', function(file) {';
					$html .= '	var exts = fileu.options.accept[0].extensions;';
					$html .= '	if((\',\'+exts).indexOf(\',\'+ext(file.name)) == -1) {';
					$html .= '		alert(L[\'upload_ext\']+ext(file.name)+\' \'+L[\'upload_allow\']+exts);';
					$html .= '		return false;';
					$html .= '	}';
					$html .= '});';
					$html .= 'file_'.$v['name'].'.on(\'fileQueued\', function(file) {';
					$html .= '	Dtoast(\''.$js_pageid.'\',L[\'uploading\'], \'\', 30);';
					$html .= '});';
					$html .= 'file_'.$v['name'].'.on(\'uploadProgress\', function(file, percentage) {';
					$html .= '	var p = parseInt(percentage * 100);';
					$html .= '	$(\'#toast-'.$js_pageid.'\').html(p > 99 ? L[\'processing\'] : L[\'uploading\']+p+\'%\');';
					$html .= '});';
					$html .= 'file_'.$v['name'].'.on(\'uploadSuccess\', function(file, data) {';
					$html .= '	if(data.error) {';
					$html .= '		Dtoast(\''.$js_pageid.'\',data.message, \'\', 5);';
					$html .= '	} else {';
					$html .= '		$(\'#'.$v['name'].'\').val(data.url);';
					$html .= '	}';
					$html .= '});';
					$html .= 'file_'.$v['name'].'.on(\'uploadError\', function(file, data) {';
					$html .= '	Dtoast(\''.$js_pageid.'\',data.message, \'\', 5);';
					$html .= '});';
					$html .= 'file_'.$v['name'].'.on(\'uploadComplete\', function(file) {';
					$html .= '	$(\'#toast-'.$js_pageid.'\').hide();';
					$html .= '});';
					$html .= '</script>';
					return $html;
				}
			break;
			case 'editor':
				if($DT_PC) {
					$toolbar = isset($group_editor) ? $group_editor : 'Destoon';
					$width = $v['width'];
					if($width < 101) $width = $width.'%';
					if(DT_EDITOR == 'fckeditor') {
						$html .= '<textarea name="post_fields['.$v['name'].']" id="'.$v['name'].'" style="display:none">'.$value.'</textarea><iframe id="'.$v['name'].'___Frame" src="'.$MODULE[2]['linkurl'].'editor/fckeditor/editor/fckeditor.html?InstanceName='.$v['name'].'&Toolbar='.$toolbar.'" width="'.$width.'" height="'.$v['height'].'" frameborder="no" scrolling="no"></iframe><br/>';
					} else {
						$html .= '<textarea name="post_fields['.$v['name'].']" id="'.$v['name'].'" style="display:none">'.$value.'</textarea>'. deditor($moduleid, $v['name'], $toolbar, $width, $v['height']);
					}
				} else {
					$html .= '</div>';
					$html .= '<input type="hidden" name="post_fields['.$v['name'].']" id="'.$v['name'].'" '.$v['addition'].'/>';
					$html .= '<ul class="ui-editor-toolbar">';
					$html .= '<li class="ui-editor-img"><div id="editor-'.$v['name'].'-picker"></div></li>';
					$html .= '<li class="ui-editor-bold"><input type="button" value=" " editor-action="bold"/>B</li>';
					$html .= '<li class="ui-editor-italic"><input type="button" value=" " editor-action="italic"/>I</li>';
					$html .= '<li class="ui-editor-underline"><input type="button" value=" " editor-action="underline"/>U</li>';
					$html .= '</ul>';
					$html .= '<div class="ui-editor-content" id="editor-'.$v['name'].'">'.$value.'</div>';
					$html .= '<script type="text/javascript">';
					$html .= '$(function(){';
					$html .= '	$(\'#editor-'.$v['name'].'\').DEditor({';
					$html .= '		editorid: \'editor-'.$v['name'].'\',';
					$html .= '		textareaid: \''.$v['name'].'\',';
					$html .= '		server: UPPath+\'?moduleid='.$moduleid.'&action=webuploader&from=editor\'';
					$html .= '	});';
					$html .= '});';
					$html .= '</script>';
					$html .= '<div class="ui-form">';
				}				
			break;
		}
		if($DT_PC) {
			$html .= ' <span class="f_red" id="'.$did.'"></span>';
			$html .= '</td></tr>';
			if($v['note']) $html .= '<tr>'.$left.'</td><td class="ts">'.$v['note'].'</td></tr>';
		} else {
			$html .= in_array($v['html'], array('thumb', 'file', 'editor')) ? '' : '</div>';
			if($v['note']) $html .= '<s>'.$v['note'].'</s>';
		}
	}
	return $html;
}

function fields_search_htm($v) {
	global $L;
	$html = '';
	$value = '';//$v['default_value'];
	switch($v['html']) {
		case 'select':
			if($v['option_value']) {
				$html .= '<select name="'.$v['name'].'" id="'.$v['name'].'" '.$v['addition'].'><option value="">'.$L['choose'].'</option>';
				$rows = explode("*", $v['option_value']);
				foreach($rows as $row) {
					if($row) {
						$cols = explode("|", trim($row));
						$html .= '<option value="'.$cols[0].'"'.($cols[0] == $value ? ' selected' : '').'>'.$cols[1].'</option>';
					}
				}
				$html .= '</select>';
			}
		break;
		case 'radio':
			if($v['option_value']) {
				$html .= '<span id="'.$v['name'].'">';
				$rows = explode("*", $v['option_value']);
				foreach($rows as $rw => $row) {
					if($row) {
						$cols = explode("|", trim($row));
						$html .= '<label><input type="radio" name="'.$v['name'].'" value="'.$cols[0].'" id="'.$v['name'].'_'.$rw.'"'.($cols[0] == $value ? ' checked' : '').'> '.$cols[1].'</label>&nbsp;&nbsp;&nbsp;';
					}
				}
				$html .= '</span>';
			}
		break;
		case 'checkbox':
			if($v['option_value']) {
				$html .= '<span id="'.$v['name'].'">';
				$value = explode(',', $value);
				$rows = explode("*", $v['option_value']);
				foreach($rows as $rw => $row) {
					if($row) {
						$cols = explode("|", trim($row));
						$html .= '<label><input type="checkbox" name="'.$v['name'].'[]" value="'.$cols[0].'" id="'.$v['name'].'_'.$rw.'"'.(in_array($cols[0], $value) ? ' checked' : '').'> '.$cols[1].'</label>&nbsp;&nbsp;&nbsp;';
					}
				}
				$html .= '</span>';
			}
		break;
	}
	return $html;
}

function fields_search_arr($v) {
	$arr = array();
	$rows = explode("*", $v['option_value']);
	foreach($rows as $row) {
		if($row) $arr[] = explode("|", trim($row));
	}		
	return $arr;
}

function fields_search_sql() {
	global $FD, $_GET;
	$condition = '';
	foreach($FD as $k=>$v) {
		if($v['search'] && isset($_GET[$v['name']])) {
			$vv = $_GET[$v['name']];
			if($v['html'] == 'checkbox') {
				if(is_array($vv)) {
					$sql = '';
					foreach($vv as $s) {
						if($s && strpos($v['option_value'], $s.'|') !== false) $sql .= " OR ".$v['name']." LIKE '%".$s.",%'";
					}
					if($sql) $condition .= substr_count($sql, ' OR ') == 1 ? " AND ".substr($sql, 4) : " AND (".substr($sql, 4).")";
				} else {
					if($vv && strpos($v['option_value'], $vv.'|') !== false) $condition .= " AND ".$v['name']." LIKE '%".$vv.",%'";
				}
			} else {
				if($vv && strpos($v['option_value'], $vv.'|') !== false) $condition .= " AND ".$v['name']."='".$vv."'";
			}
		}
	}
	return $condition;
}

function fields_message($msg) {
	defined('DT_ADMIN') ? msg($msg) : dalert($msg);
}
?>