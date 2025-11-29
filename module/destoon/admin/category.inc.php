<?php
/*
	DESTOON Copyright (C)2008-2099 www.destoon.com
	This is NOT a freeware,Use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$mid or $mid = 4;
$CATEGORY = cache_read('category-'.$mid.'.php');
$MOD = cache_read('module-'.$mid.'.php');
$NUM = count($CATEGORY);
$catid = isset($catid) ? intval($catid) : 0;
$do = new category($mid, $catid);
$parentid = isset($parentid) ? intval($parentid) : 0;
$table = $DT_PRE.'category';
$menus = array (
    array('添加分类', '?file='.$file.'&action=add&mid='.$mid.'&parentid='.$parentid),
    array('管理分类', '?file='.$file.'&mid='.$mid),
    array('分类复制', '?file='.$file.'&action=copy&mid='.$mid),
    array('更新缓存', '?file='.$file.'&action=caches&mid='.$mid),
);
if(strpos($forward, 'category') === false) $forward = '?file='.$file.'&mid='.$mid.'&parentid='.$parentid.'&kw='.urlencode($kw);
switch($action) {
	case 'add':
		if($submit) {
			if(!$category['catname']) msg('分类名不能为空');
			$category['catname'] = trim($category['catname']);

            if(isset($category['color'])) $category['color'] = trim($category['color']); // ← 新增

            //(var_dump($category['catdir']));
			$childs = '';
			$catids = array();
			//添加单个分类。
			if(strpos($category['catname'], "\n") === false) {
				$category['catdir'] = $do->get_catdir($category['catdir']);
				// 先把得到的数据添加进去
				$do->add($category);
				$childs .= ','.$do->catid;
				$catids[] = $do->catid;
//							print_r($category);die();
			} else {
				$catnames = explode("\n", $category['catname']);
				foreach($catnames as $catname) {
					$catname = trim($catname);
					if(!$catname) continue;
					$category['catname'] = $catname;
					$category['catdir'] = '';
					$category['letter'] = '';
					$category['seo_title'] = '';
					$category['seo_keywords'] = '';
					$category['seo_description'] = '';
					$do->add($category);
					$childs .= ','.$do->catid;
					$catids[] = $do->catid;
				}
			}



			//如果分类有上级
			if($category['parentid']) {
				$parents = array();
				$cid = $category['parentid'];
				$parents[] = $cid;
				$i = 0;
				while($i++ < 100) {
					if($CATEGORY[$cid]['parentid']) {
						$parents[] = $cid = $CATEGORY[$cid]['parentid'];
					} else {
						break;
					}
				}
				foreach($parents as $catid) {
					$arrchildid = $CATEGORY[$catid]['child'] ? $CATEGORY[$catid]['arrchildid'].$childs : $catid.$childs;
					$db->query("UPDATE {$table} SET child=1,arrchildid='$arrchildid' WHERE catid=$catid");
				}
			}
			foreach($catids as $catid) {
				$CATEGORY[$catid] = $db->get_one("SELECT * FROM {$table} WHERE catid=$catid");
				update_category($CATEGORY[$catid]);
			}
			$NUM > 500 ? $do->cache() : $do->repair();
			dmsg('添加成功', '?file='.$file.'&mid='.$mid.'&parentid='.$category['parentid']);
		} else {
			include tpl('category_edit');
		}
	break;
	case 'edit':
		$catid or msg();
		if($submit) {
           // print_r($category['fua']);  die();
            foreach ($category['fua'] as $k=>$v){
                $category['fua'][$k]['ming'] = urlencode($category['fua'][$k]['ming']);
            }
            $category['fua'] =  json_encode($category['fua'],true);
//            $category['fua'] = json_encode(urlencode($category['fua']),true);


           // echo "<pre>";
           // print_r($category['fua']);
           // die();
            if(isset($category['color'])) $category['color'] = trim($category['color']); // ← 新增
			if(!$category['catname']) msg('分类名不能为空');
			if($category['parentid'] == $catid) msg('上级分类不能与当前分类相同');
//            print_r(111);die();
//            如果无编辑内容可以提交吗？
			$do->edit($category);
			$category['catid'] = $catid;
			update_category($category);
			$NUM > 500 ? $do->cache() : $do->repair();
			dmsg('修改成功', '?file='.$file.'&mid='.$mid.'&parentid='.$category['parentid']);
		} else {
//            获取内容
            $arr = $db->get_one("SELECT content FROM ".$DT_PRE."category_content WHERE catid=$catid");
            $content=$arr['content'];
			extract($db->get_one("SELECT * FROM {$table} WHERE catid=$catid"));
//            提取附加信息
            $fua = json_decode($fua,true);
//            var_dump($fua);die();
            foreach ($fua as $k=>$v){
                $fua[$k]['ming'] = urldecode($fua[$k]['ming']);
            }
//            die();
//            echo "<pre>";
//            var_dump($fua);die();

//            $fua = json_decode($fua,true);
//            echo "<pre>";
//            var_dump($fua);die();
//            var_dump(tpl('category_edit'));die();
			include tpl('category_edit');
		}
	break;
	case 'copy':
		if($submit) {
			if(!$fromid) msg('源模块ID不能为空');
			if(!$save) $db->query("DELETE FROM {$table} WHERE moduleid=$mid");
			$result = $db->query("SELECT * FROM {$table} WHERE moduleid=$fromid ORDER BY catid");
			$O = $R = array();
			while($r = $db->fetch_array($result)) {
				$O[$r['catid']] = $r['catname'];
				$catid = $r['catid'];
				unset($r['catid']);
				$r['cid'] = $catid;
				$r['moduleid'] = $mid;
				$r['item'] = $r['property'] = 0;
				$r = daddslashes($r);
				$db->query("INSERT INTO {$table} ".arr2sql($r, 0));
				$R[$catid] = $db->insert_id();
			}
			$result = $db->query("SELECT * FROM {$table} WHERE moduleid=$mid ORDER BY catid");
			while($r = $db->fetch_array($result)) {
				$catid = $r['catid'];
				$v = $r['parentid'];
				$parentid = isset($R[$v]) ? $R[$v] : $v;
				$arrparentid = explode(',', $r['arrparentid']);
				foreach($arrparentid as $k=>$v) {
					if(isset($R[$v])) $arrparentid[$k] = $R[$v];
				}
				$arrparentid = implode(',', $arrparentid);
				$arrchildid = explode(',', $r['arrchildid']);
				foreach($arrchildid as $k=>$v) {
					if(isset($R[$v])) $arrchildid[$k] = $R[$v];
				}
				$arrchildid = implode(',', $arrchildid);
				$db->query("UPDATE {$table} SET parentid='$parentid',arrparentid='$arrparentid',arrchildid='$arrchildid' WHERE catid=$catid");
			}
			$do->repair();
			msg('分类复制成功', '?file='.$file.'&action=url&&mid='.$mid.'&forward='.urlencode('?file='.$file.'&mid='.$mid));
		} else {
			include tpl('category_copy');
		}
	break;
	case 'caches':
		msg('开始更新统计', "?file=$file&mid=$mid&action=count");
	break;
	case 'count':
		require DT_ROOT.'/include/module.func.php';
		$tb = get_table($mid);
		if($MODULE[$mid]['module'] == 'club') $tb = $DT_PRE.'club_group_'.$mid;
		if(!isset($num)) {
			$num = 50;
		}
		if(!isset($fid)) {
			$r = $db->get_one("SELECT MIN(catid) AS fid FROM {$table} WHERE moduleid=$mid");
			$fid = $r['fid'] ? $r['fid'] : 0;
		}
		isset($sid) or $sid = $fid;
		if(!isset($tid)) {
			$r = $db->get_one("SELECT MAX(catid) AS tid FROM {$table} WHERE moduleid=$mid");
			$tid = $r['tid'] ? $r['tid'] : 0;
		}
		if($fid <= $tid) {
			$result = $db->query("SELECT catid FROM {$table} WHERE moduleid=$mid AND catid>=$fid ORDER BY catid LIMIT 0,$num");
			if($db->affected_rows($result)) {
				while($r = $db->fetch_array($result)) {
					$catid = $r['catid'];					
					if($mid == 4) {
						$condition = "groupid IN (".get_gids().") AND catids like '%,".$catid.",%'";
					} else {
						$condition = 'status=3';
						$condition .= $CATEGORY[$catid]['child'] ? " AND catid IN (".$CATEGORY[$catid]['arrchildid'].")" : " AND catid=$catid";
					}
					$item = $db->count($tb, $condition);
					$db->query("UPDATE {$table} SET item=$item WHERE catid=$catid");
				}
				$catid += 1;
			} else {
				$catid = $fid + $num;
			}
		} else {
			msg('统计更新成功', "?file=$file&mid=$mid&action=url");
		}
		msg('ID从'.$fid.'至'.($catid-1).'更新成功'.progress($sid, $fid, $tid), "?file=$file&mid=$mid&action=$action&sid=$sid&fid=$catid&tid=$tid&num=$num");
	break;
	case 'url':	
		foreach($CATEGORY as $c) {
			update_category($c);
		}
		msg('地址更新成功', "?file=$file&mid=$mid&action=letters");
	break;
	case 'letters':
		$update = false;
		foreach($CATEGORY as $k=>$v) {
			if(strlen($v['letter']) != 1) {
				$letter = $do->get_letter($v['catname'], false);
				if($letter) {
					$update = true;
					$letter = substr($letter, 0, 1);
					$db->query("UPDATE {$table} SET letter='$letter' WHERE catid='$v[catid]'");
				}
			}
		}
		msg('索引修复成功', "?file=$file&mid=$mid&action=cache");
	break;
	case 'cache':
		$do->repair();
		dmsg('缓存更新成功', '?file='.$file.'&mid='.$mid);
	break;
	case 'delete':
		if($catid) $catids = $catid;
		$catids or msg('请选择分类');
		$do->delete($catids);
		$NUM > 500 ? $do->cache() : $do->repair();
		dmsg('删除成功', $forward);
	break;
	case 'update':
		if(!$category || !is_array($category)) msg();
		$do->update($category);
		foreach($category as $catid=>$v) {
			$CATEGORY[$catid] = $db->get_one("SELECT * FROM {$table} WHERE catid=$catid");
			update_category($CATEGORY[$catid]);
		}		
		$NUM > 500 ? $do->cache() : $do->repair();
		dmsg('更新成功', '?file='.$file.'&mid='.$mid.'&parentid='.$parentid);
	break;
	case 'letter':
		isset($catname) or $catname = '';
		if(!$catname || strpos($catname, "\n") !== false) exit('');
		exit($do->get_letter($catname, false));
	break;
	case 'ckdir':
		if($do->get_catdir($catdir)) {
			dialog('目录名可以使用');
		} else {
			dialog('目录名不合法或者已经被使用');
		}
	break;
	default:
		$total = 0;
		$DTCAT = array();
		$condition = "moduleid=$mid";
		$condition .= $keyword ? " AND catname LIKE '%$keyword%'" : " AND parentid=$parentid";
		$result = $db->query("SELECT * FROM {$table} WHERE {$condition} ORDER BY listorder,catid");
		while($r = $db->fetch_array($result)) {
			$r['childs'] = substr_count($r['arrchildid'], ',');
			$total += $r['item'];
			$DTCAT[$r['catid']] = $r;
		}
		if(!$DTCAT && !$parentid && !$keyword) msg('暂无分类,请先添加',  '?file='.$file.'&mid='.$mid.'&action=add&parentid='.$parentid);
		include tpl('category');
	break;
}

class category {
	var $moduleid;
	var $catid;
	var $category = array();
	var $table;	

	function __construct($moduleid = 1, $catid = 0) {
		global $CATEGORY;
		$this->moduleid = $moduleid;
		$this->catid = $catid;
		if(!isset($CATEGORY)) $CATEGORY = cache_read('category-'.$this->moduleid.'.php');
		$this->category = $CATEGORY;
		$this->table = DT_PRE.'category';
	}

	function category($moduleid = 1, $catid = 0) {
		$this->__construct($moduleid, $catid);
	}

	function add($post)	{
//        print_r($post);die();
//  $post是个数组  Array ( [parentid] => 23 [catname] => ddd [catdir] => ddd [thumb] => [icon] => [letter] => d [level] => 1 [template] => [show_template] => [seo_title] => dd [seo_keywords] => dddddddd [seo_description] => dddddd [content] =>ddd
		$post['moduleid'] = $this->moduleid;
		$post['letter'] = preg_match("/^[a-z]{1}+$/i", $post['letter']) ? strtolower($post['letter']) : '';
		foreach(array('group_list',  'group_show',  'group_add') as $v) {
			$post[$v] = isset($post[$v]) ? implode(',', $post[$v]) : '';
		}
		is_url($post['thumb']) or $post['thumb'] = '';
		is_url($post['icon']) or $post['icon'] = '';
		DB::query("INSERT INTO {$this->table} ".arr2sql($post, 0));
		$this->catid = DB::insert_id();
        //增加内容
        $sql = "INSERT INTO ".DT_PRE."category_content (catid,content) VALUES (".$this->catid.", '".$_POST['post']['content']."')";
        DB::query($sql);
        $this->update($this->catid);
//        print_r($sql);
//        die();

		if($post['parentid']) {
			$post['catid'] = $this->catid;
			$this->category[$this->catid] = $post;
			$arrparentid = $this->get_arrparentid($this->catid);
		} else {
			$arrparentid = 0;
		}
		$catdir = $post['catdir'] ? $post['catdir'] : $this->catid;
		DB::query("UPDATE {$this->table} SET listorder=$this->catid,catdir='$catdir',arrparentid='$arrparentid' WHERE catid=$this->catid");
        //      删除此用户没有提交的临时图片
		clear_upload($post['thumb'].$post['icon'].$_POST['post']['content']);
//            die();
		return true;
	}

	function edit($post) {








//        print_r($post);die();
		$post['letter'] = preg_match("/^[a-z]{1}+$/i", $post['letter']) ? strtolower($post['letter']) : '';
		if($post['parentid']) {
			$post['catid'] = $this->catid;
			$this->category[$this->catid] = $post;
//                    print_r($this->category[$this->catid]);die();
			$post['arrparentid'] = $this->get_arrparentid($this->catid);
		} else {
			$post['arrparentid'] = 0;
		}





//        先获取内容，如果内容不存在就插入，如果存在就个更新。
        $sql = "SELECT content,itemid FROM ".DT_PRE."category_content WHERE catid=$this->catid";
        $r = DB::get_one($sql);
//        不能用这个，只能用上面的语句
//        $arr = DB::query("$sql");
       // var_dump($r);
       // die();

        if($r['itemid']==''){
            $sql = "INSERT INTO ".DT_PRE."category_content (catid,content) VALUES (".$this->catid.", '".$post['content']."')";
        }else{
            $sql = "update ".DT_PRE."category_content SET content='".$post['content']."' WHERE catid = ".$this->catid;
        }
//        更新内容表
//        print_r($sql);
//        die();
        DB::query($sql)	;





// 		然后把content 从category中去掉
        $tmpost = $post['content'];
        unset($post['content']);
//        检查图片的格式否则赋值为空
//        如果存在，并且是url 。否则就赋值为空
		is_url($post['thumb']) or $post['thumb'] = '';
		is_url($post['icon']) or $post['icon'] = '';


        //        删除不使用的图片
        if($this->catid) {
            $new = $tmpost;
            if($post['thumb']) $new .= '<img src="'.$post['thumb'].'"/>';
//            $r = $this->get_one();
            $old = $r['content'];
            if($r['thumb']) $old .= '<img src="'.$r['thumb'].'"/>';
            delete_diff($new, $old, $this->catid);
        } else {
            $post['ip'] = DT_IP;
        }




//        更新三个点：允许浏览分类，允许浏览分类信息内容，允许发布信息，
		foreach(array('group_list',  'group_show',  'group_add') as $v) {
			$post[$v] = isset($post[$v]) ? implode(',', $post[$v]) : '';
		}
		$post['linkurl'] = '';

		DB::query("UPDATE {$this->table} SET ".arr2sql($post, 1)." WHERE catid=$this->catid");

//      清理此用户没有提交的临时图片
	    clear_upload($post['thumb'].$post['icon'].$tmpost,$this->catid);
//        print_r(88888888);
//        die();

		return true;
	}

	function delete($catids) {
		if(is_array($catids)) {
			foreach($catids as $catid) {
				if(isset($this->category[$catid])) $this->delete($catid);
			}
		} else {
			$catid = $catids;
			if(isset($this->category[$catid])) {
				DB::query("DELETE FROM {$this->table} WHERE catid=$catid");
				$arrchildid = $this->category[$catid]['arrchildid'] ? $this->category[$catid]['arrchildid'] : $catid;
				DB::query("DELETE FROM {$this->table} WHERE catid IN ($arrchildid)");			
				if($this->moduleid > 4) DB::query("UPDATE ".get_table($this->moduleid)." SET status=0 WHERE catid IN (".$arrchildid.")");
			}
		}
		return true;
	}

	function update($post) {
	    if(!is_array($post)) return false;
		foreach($post as $k=>$v) {
			if(!$v['catname']) continue;
			$v['parentid'] = intval($v['parentid']);
			if($k == $v['parentid']) continue;
			if($v['parentid'] > 0 && !isset($this->category[$v['parentid']])) continue;
			$v['listorder'] = intval($v['listorder']);
			$v['level'] = intval($v['level']);
			$v['letter'] = preg_match("/^[a-z0-9]{1}+$/i", $v['letter']) ? strtolower($v['letter']) : '';
			$v['catdir'] = $this->get_catdir($v['catdir'], $k);
			if(!$v['catdir']) $v['catdir'] = $k;
			DB::query("UPDATE {$this->table} SET catname='$v[catname]',parentid='$v[parentid]',listorder='$v[listorder]',style='$v[style]',level='$v[level]',letter='$v[letter]',fua='$v[fua]',catdir='$v[catdir]' WHERE catid=$k ");
		}
		return true;
	}

	function repair() {
		$query = DB::query("SELECT * FROM {$this->table} WHERE moduleid='$this->moduleid' ORDER BY listorder,catid");
		$CATEGORY = array();
		while($r = DB::fetch_array($query)) {
			$CATEGORY[$r['catid']] = $r;
		}
		$childs = array();
		foreach($CATEGORY as $catid => $category) {
			$CATEGORY[$catid]['arrparentid'] = $arrparentid = $this->get_arrparentid($catid);
			$CATEGORY[$catid]['catdir'] = $catdir = preg_match("/^[0-9a-z_\-\/]+$/i", $category['catdir']) ? $category['catdir'] : $catid;
			$sql = "catdir='$catdir',arrparentid='$arrparentid'";
			if(!$category['linkurl']) {
				$CATEGORY[$catid]['linkurl'] = listurl($category);
				$sql .= ",linkurl='$category[linkurl]'";
			}
			DB::query("UPDATE {$this->table} SET $sql WHERE catid=$catid");
			if($arrparentid) {
				$arr = explode(',', $arrparentid);
				foreach($arr as $a) {
					if($a == 0) continue;
					isset($childs[$a]) or $childs[$a] = '';
					$childs[$a] .= ','.$catid;
				}
			}
		}
		foreach($CATEGORY as $catid => $category) {
			if(isset($childs[$catid])) {
				$CATEGORY[$catid]['arrchildid'] = $arrchildid = $catid.$childs[$catid];
				$CATEGORY[$catid]['child'] = 1;
				DB::query("UPDATE {$this->table} SET arrchildid='$arrchildid',child=1 WHERE catid='$catid'");
			} else {
				$CATEGORY[$catid]['arrchildid'] = $catid;
				$CATEGORY[$catid]['child'] = 0;
				DB::query("UPDATE {$this->table} SET arrchildid='$catid',child=0 WHERE catid='$catid'");
			}
		}
		$this->cache($CATEGORY);
        return true;
	}

	function get_arrparentid($catid) {
		$CAT = get_cat($catid);
		if($CAT['parentid'] && $CAT['parentid'] != $catid) {
			$parents = array();
			$cid = $catid;
			$i = 1;
			while($i++ < 10) {
				$CAT = get_cat($cid);
				if($CAT['parentid']) {
					$parents[] = $cid = $CAT['parentid'];
				} else {
					break;
				}
			}
			$parents[] = 0;
			return implode(',', array_reverse($parents));
		} else {
			return '0';
		}
	}

	function get_catdir($catdir, $catid = 0) {
		if(preg_match("/^[0-9a-z_\-\/]+$/i", $catdir)) {
			$condition = "catdir='$catdir' AND moduleid='$this->moduleid'";
			if($catid) $condition .= " AND catid!=$catid";
			$r = DB::get_one("SELECT catid FROM {$this->table} WHERE {$condition}");
			if($r) {
				return '';
			} else {
				return $catdir;
			}
		} else {
			return '';
		}
	}

	function get_letter($catname, $letter = true) {
		return $letter ? strtolower(substr(gb2py($catname), 0, 1)) : str_replace(' ', '', gb2py($catname));
	}

	function cache($data = array()) {
		cache_category($this->moduleid, $data);
	}
}
?>