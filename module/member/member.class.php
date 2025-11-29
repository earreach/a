<?php 
defined('IN_DESTOON') or exit('Access Denied');
class member {
	var $userid;
	var $username;
	var $table_member;
	var $table_member_misc;
	var $table_member_check;
	var $table_company;
	var $table_company_data;
	var $errmsg = errmsg;

    function __construct() {
		$this->table_member = DT_PRE.'member';
		$this->table_member_misc = DT_PRE.'member_misc';
		$this->table_member_check = DT_PRE.'member_check';
		$this->table_company = DT_PRE.'company';
		$this->table_company_data = DT_PRE.'company_data';
    }

    function member() {
		$this->__construct();
    }

	function is_username($username, $auto = 0) {
		global $MOD, $L;
		if(!check_name($username)) return $this->_($L['member_username_match']);
		$MOD['minusername'] or $MOD['minusername'] = 4;
		$MOD['maxusername'] or $MOD['maxusername'] = 20;
		if(strlen($username) < $MOD['minusername'] || strlen($username) > $MOD['maxusername']) return $this->_(lang($L['member_username_len'], array($MOD['minusername'], $MOD['maxusername'])));
		if($MOD['banusername'] && !$this->userid) {
			$tmp = explode('|', $MOD['banusername']);
			foreach($tmp as $v) {
				if($MOD['banmodeu']) {
					if($username == $v) return $this->_($L['member_username_ban']);
				} else {
					if(strpos($username, $v) !== false) return $this->_($L['member_username_ban']);
				}
			}
		}
		if(!$auto && substr($username, 0, 4) == 'uid-') return $this->_($L['member_username_ban']);
		if($this->username_exists($username)) return $this->_($L['member_username_reg']);
		return true;
	}

	function is_company($company) {
		global $MOD, $L;
		$company = trim($company);		
		if(strlen($company) < 6) return $this->_($L['member_company_null']);
		if(preg_match("/[0-9]+/", $company) || !is_clean($company)) return $this->_($L['member_company_bad']);
		if($MOD['bancompany'] && !$this->userid) {
			$tmp = explode('|', $MOD['bancompany']);
			foreach($tmp as $v) {
				if($MOD['banmodec']) {
					if($company == $v) return $this->_($L['member_company_ban']);
				} else {
					if(strpos($company, $v) !== false) return $this->_($L['member_company_ban']);
				}
			}
		}
		return true;
	}

	function is_email($email) {
		global $MOD, $L;
		$email = trim($email);
		if(!is_email($email)) return $this->_($L['member_email_null']);
		if($MOD['banemail']) {
			$domain = substr(strstr($email, '@'), 1);
			$tmp = explode('|', $MOD['banemail']);
			foreach($tmp as $v) {
				if($domain == $v) return $this->_($L['member_email_ban']);
			}
		}
		return true;
	}

	function is_passport($passport, $auto = 0) {
		global $MOD, $L;
		$MOD['minusername'] or $MOD['minusername'] = 2;
		$MOD['maxusername'] or $MOD['maxusername'] = 30;
		if(!is_clean($passport)) $this->_($L['member_passport_char']);
		if(word_count($passport) < $MOD['minusername'] || word_count($passport) > $MOD['maxusername']) return $this->_(lang($L['member_passport_len'], array($MOD['minusername'], $MOD['maxusername'])));
		if($MOD['banusername'] && !$this->userid) {
			$tmp = explode('|', $MOD['banusername']);
			foreach($tmp as $v) {
				if($MOD['banmodeu']) {
					if($passport == $v) return $this->_($L['member_passport_ban']);
				} else {
					if(strpos($passport, $v) !== false) return $this->_($L['member_passport_ban']);
				}
			}
		}
		if(!$auto && substr($passport, 0, 4) == 'uid-') return $this->_($L['member_passport_ban']);
		if($this->passport_exists($passport)) return $this->_($L['member_passport_reg']);
		return true;
	}

	function is_password($password, $cpassword = '') {
		global $MOD, $L;
		if(!$password) return $this->_($L['member_password_null']);
		if($cpassword && $password != $cpassword) return $this->_($L['member_password_match']);
		if(is_md5($password)) return true;
		if(!$MOD['minpassword'] || $MOD['minpassword'] < 6) $MOD['minpassword'] = 6;
		if(!$MOD['maxpassword'] || $MOD['maxpassword'] > 30) $MOD['maxpassword'] = 30;
		if($MOD['minpassword'] > $MOD['maxpassword'] || $MOD['maxpassword'] - $MOD['minpassword'] < 6) $MOD['maxpassword'] = $MOD['minpassword'] + 6;
		if(strlen($password) < $MOD['minpassword'] || strlen($password) > $MOD['maxpassword']) return $this->_(lang($L['member_password_len'], array($MOD['minpassword'], $MOD['maxpassword'])));
		if(strpos(','.$MOD['mixpassword'].',', ',1,') !== false && !preg_match("/[0-9]/", $password)) return $this->_($L['member_password_1']);
		if(strpos(','.$MOD['mixpassword'].',', ',2,') !== false && !preg_match("/[a-z]/", $password)) return $this->_($L['member_password_2']);
		if(strpos(','.$MOD['mixpassword'].',', ',3,') !== false && !preg_match("/[A-Z]/", $password)) return $this->_($L['member_password_3']);
		if(strpos(','.$MOD['mixpassword'].',', ',4,') !== false && !preg_match("/[[:punct:]]/", $password)) return $this->_($L['member_password_4']);
		return true;
	}

	function is_payword($password, $cpassword = '') {
		global $MOD, $L;
		if(!$password) return $this->_($L['member_payword_null']);
		if($cpassword && $password != $cpassword) return $this->_($L['member_payword_match']);
		if(is_md5($password)) return true;
		if(!$MOD['minpassword'] || $MOD['minpassword'] < 6) $MOD['minpassword'] = 6;
		if(!$MOD['maxpassword'] || $MOD['maxpassword'] > 30) $MOD['maxpassword'] = 30;
		if($MOD['minpassword'] > $MOD['maxpassword'] || $MOD['maxpassword'] - $MOD['minpassword'] < 6) $MOD['maxpassword'] = $MOD['minpassword'] + 6;
		if(strlen($password) < $MOD['minpassword'] || strlen($password) > $MOD['maxpassword']) return $this->_(lang($L['member_payword_len'], array($MOD['minpassword'], $MOD['maxpassword'])));
		if(strpos(','.$MOD['mixpassword'].',', ',1,') !== false && !preg_match("/[0-9]/", $password)) return $this->_($L['member_password_1']);
		if(strpos(','.$MOD['mixpassword'].',', ',2,') !== false && !preg_match("/[a-z]/", $password)) return $this->_($L['member_password_2']);
		if(strpos(','.$MOD['mixpassword'].',', ',3,') !== false && !preg_match("/[A-Z]/", $password)) return $this->_($L['member_password_3']);
		if(strpos(','.$MOD['mixpassword'].',', ',4,') !== false && !preg_match("/[[:punct:]]/", $password)) return $this->_($L['member_password_4']);
		return true;
	}

	function email_exists($email) {
		$condition = "email='$email'";
		if($this->userid) $condition .= " AND userid!=$this->userid";
		return DB::get_one("SELECT userid FROM {$this->table_member} WHERE {$condition}");
	}

	function mobile_exists($mobile) {
		$condition = "mobile='$mobile'";
		if($this->userid) $condition .= " AND userid!=$this->userid";
		return DB::get_one("SELECT userid FROM {$this->table_member} WHERE {$condition}");
	}

	function username_exists($username) {
		if(is_mobile($username)) return true;
		$t = DB::get_one("SELECT userid FROM {$this->table_member} WHERE username='$username'");
		if($t) return true;
		return DB::get_one("SELECT userid FROM {$this->table_member} WHERE passport='$username'");
	}

	function company_exists($company) {
		$condition = "company='$company'";
		if($this->userid) $condition .= " AND userid!=$this->userid";
		return DB::get_one("SELECT userid FROM {$this->table_company} WHERE {$condition}");
	}

	function shop_exists($shop) {
		$condition = "shop='$shop'";
		if($this->userid) $condition .= " AND userid!=$this->userid";
		return DB::get_one("SELECT userid FROM {$this->table_member} WHERE {$condition}");
	}

	function passport_exists($passport) {
		$condition = "passport='$passport'";
		if($this->userid) $condition .= " AND userid!=$this->userid";
		$t = DB::get_one("SELECT userid FROM {$this->table_member} WHERE {$condition}");
		if($t) return true;
		if(check_name($passport)) {
			$condition = "username='$passport'";
			if($this->userid) $condition .= " AND userid!=$this->userid";
			return DB::get_one("SELECT userid FROM {$this->table_member} WHERE {$condition}");
		}
		return false;
	}

	function pass($member, $auto = 0) {
		global $L, $AREA, $MOD;
		if(!is_array($member)) return false;
		if(!$member['groupid']) return $this->_($L['member_groupid_null']);
		if($member['email']) {
			if(!is_email($member['email'])) return $this->_($L['member_email_null']);
			if(!$this->is_email($member['email'])) return $this->_($this->errmsg);
			if($this->email_exists($member['email'])) return $this->_($L['member_email_reg']);
		} else {
			if($MOD['email_register'] == 2 && !is_email($member['email'])) return $this->_($L['member_email_null']);
		}
		if($member['mobile']) {
			if(!is_mobile($member['mobile'])) return $this->_($L['member_mobile_null']);
			if($this->mobile_exists($member['mobile'])) return $this->_($L['member_mobile_reg']);
		} else {
			if($MOD['email_register'] == 2 && !is_mobile($member['mobile'])) $this->_($L['member_mobile_null']);
		}
		if($MOD['truename_register'] == 2 && strlen($member['truename']) < 2) $this->_($L['member_truename_null']);
		if($MOD['qq_register'] == 2 && !is_qq($member['qq'])) $this->_($L['member_qq_null']);
		if($MOD['wx_register'] == 2 && !is_wx($member['wx'])) $this->_($L['member_wx_null']);
		$groupid = $this->userid ? $member['groupid'] : $member['regid'];
		$GROUP = cache_read('group.php');
		if($GROUP[$groupid]['type']) {
			if(!$this->is_company($member['company'])) return $this->_($this->errmsg);
			if($this->company_exists($member['company'])) return $this->_($L['member_company_reg']);
		}
		if($this->userid) {			
			if($member['shop'] && $this->shop_exists($member['shop'])) return $this->_($L['member_shop_reg']);
			if(strlen($member['truename']) < 2 || !is_clean($member['truename'])) return $this->_($L['member_truename_null']);
			$areaid = intval($member['areaid']);
			if(!$areaid || !DB::get_one("SELECT areaid FROM ".DT_PRE."area WHERE areaid=$areaid")) return $this->_($L['member_areaid_null']);
			if($member['password'] && !$this->is_password($member['password'], $member['cpassword'])) return false;
			if($member['payword'] && !$this->is_payword($member['payword'], $member['cpayword'])) return false;
			if($GROUP[$groupid]['type']) {
				if(strlen($member['type']) < 2) return $this->_($L['member_type_null']);
				if(!is_tel($member['telephone'])) return $this->_($L['member_telephone_null']);
				if(strlen($member['regyear']) != 4 || !is_numeric($member['regyear'])) return $this->_($L['member_regyear_null']);
				if(empty($member['address'])) return $this->_($L['member_address_null']);
				#if(word_count($member['content']) < 5) return $this->_($L['member_introduce_null']);
				if(!$member['business']) return $this->_($L['member_business_null']);
				if(strlen($member['catid']) < 2) return $this->_($L['member_catid_null']);
			}
		} else {
			if(!$this->is_username($member['username'], $auto)) return false;
			if(!$this->is_passport($member['passport'], $auto)) return false;
			if($GROUP[$groupid]['type'] && !$this->is_company($member['company'])) return false;
			if(!$this->is_password($member['password'], $member['cpassword'])) return false;
			if($MOD['checkuser'] == 1 && strlen($member['reason']) < 2) return $this->_($L['member_reason_reg']);
		}
		if(DT_MAX_LEN && strlen(clear_img($member['content'])) > DT_MAX_LEN) return $this->_(lang('message->pass_max'));
		return true;
	}

	function set($member) {
		global $MOD, $GROUP;
		$GROUP or $GROUP = cache_read('group.php');
		$member['enterprise'] = $GROUP[$member['groupid']]['type'] ? 1 : 0;
		$member['email'] = trim($member['email']);
		$member['mail'] = isset($member['mail']) ? trim($member['mail']) : '';
		is_email($member['mail']) or $member['mail'] = '';
		$member['telephone'] = isset($member['telephone']) ? trim($member['telephone']) : '';
		is_tel($member['telephone']) or $member['telephone'] = '';
		$member['qq'] = isset($member['qq']) ? trim($member['qq']) : '';
		is_qq($member['qq']) or $member['qq'] = '';
		$member['wx'] = isset($member['wx']) ? trim($member['wx']) : '';
		is_wx($member['wx']) or $member['wx'] = '';
		$member['gzh'] = isset($member['gzh']) ? trim($member['gzh']) : '';
		is_gzh($member['gzh']) or $member['gzh'] = '';
		$member['thumb'] = isset($member['thumb']) ? trim($member['thumb']) : '';
		is_url($member['thumb']) or $member['thumb'] = '';
		$member['cover'] = isset($member['cover']) ? trim($member['cover']) : '';
		is_url($member['cover']) or $member['cover'] = '';
		$member['wxqr'] = isset($member['wxqr']) ? trim($member['wxqr']) : '';
		is_url($member['wxqr']) or $member['wxqr'] = '';
		$member['gzhqr'] = isset($member['gzhqr']) ? trim($member['gzhqr']) : '';
		is_url($member['gzhqr']) or $member['gzhqr'] = '';	
		check_name($member['inviter']) or $member['inviter'] = '';
		($member['inviter'] != $member['username']) or $member['inviter'] = '';
		$member['postcode'] = isset($member['postcode']) ? trim($member['postcode']) : '';
		is_numeric($member['postcode']) or $member['postcode'] = '';
		$member['ali'] = isset($member['ali']) ? trim($member['ali']) : '';
		if(!is_clean($member['ali'])) $member['ali'] = '';
		$member['skype'] = isset($member['skype']) ? trim($member['skype']) : '';
		if(!is_clean($member['skype'])) $member['skype'] = '';
		$member['address'] = isset($member['address']) ? trim($member['address']) : '';
		if(!is_clean($member['address'])) $member['address'] = '';
		$member['mode'] = (isset($member['mode']) && is_array($member['mode']) && $member['mode']) ? implode(',', $member['mode']) : '';
		$member['invoice'] = (isset($member['invoice']) && is_array($member['invoice']) && $member['invoice']) ? implode(',', $member['invoice']) : '';
		$member['keyword'] = $member['company'];
		$member['homepage'] = isset($member['homepage']) ? fix_link($member['homepage']) : '';
		$member['capital'] = isset($member['capital']) ? dround($member['capital']) : '';
		$member['sound'] = isset($member['sound']) ? intval($member['sound']) : 0;
		$member['agent'] = (isset($member['agent']) && $member['agent']) ? 1 : 0;
		$member['checkorder'] = (isset($member['checkorder']) && $member['checkorder']) ? 1 : 0;
		$member['bill'] = (isset($member['bill']) && $member['bill']) ? 1 : 0;
		if($this->userid) {
			$member['keyword'] = $member['company'];
			if($member['business'] && strpos($member['keyword'], $member['business']) === false) $member['keyword'] .= ','.$member['business'];
			if($member['sell'] && strpos($member['keyword'], $member['sell']) === false) $member['keyword'] .= ','.$member['sell'];
			if($member['buy'] && strpos($member['keyword'], $member['buy']) === false) $member['keyword'] .= ','.$member['buy'];
			if($member['mode'] && strpos($member['keyword'], $member['mode']) === false) $member['keyword'] .= ','.$member['mode'];
			if($member['areaid']) $member['keyword'] .= ','.addslashes(strip_tags(area_pos($member['areaid'], ',')));
			$new = $member['content'];
			if($member['thumb']) $new .= '<img src="'.$member['thumb'].'">';
			if($member['cover']) $new .= '<img src="'.$member['cover'].'">';
			if($member['wxqr']) $new .= '<img src="'.$member['wxqr'].'">';
			if($member['gzhqr']) $new .= '<img src="'.$member['gzhqr'].'">';
			$content_table = content_table(4, $this->userid, is_file(DT_CACHE.'/4.part'), $this->table_company_data);
			$r = DB::get_one("SELECT content FROM {$content_table} WHERE userid=$this->userid");
			$old = $r['content'];
			$r = $this->get_one();
			if($r['thumb']) $old .= '<img src="'.$r['thumb'].'">';
			if($r['cover']) $old .= '<img src="'.$r['cover'].'">';
			if($r['wxqr']) $old .= '<img src="'.$r['wxqr'].'">';
			if($r['gzhqr']) $old .= '<img src="'.$r['gzhqr'].'">';
			delete_diff($new, $old, $this->userid);
		}
		$member['introduce'] = addslashes(get_intro($member['content'], $MOD['introduce_length']));
		if(!defined('DT_ADMIN')) {
			$content = $member['content'];
			unset($member['content']);
			$member = dhtmlspecialchars($member);
			$member['content'] = dsafe($content);
		}
		if($MOD['introduce_clear'] || $MOD['introduce_save']) {
			$member['content'] = stripslashes($member['content']);
			$member['content'] = save_local($member['content']);
			if($MOD['introduce_clear']) $member['content'] = clear_link($member['content']);
			if($MOD['introduce_save']) $member['content'] = save_remote($member['content']);
			$member['content'] = addslashes($member['content']);
		}
		if($member['catid']) {
			$catids = explode(',', substr($member['catid'], 1, -1));
			$cids = '';
			foreach($catids as $catid) {
				$C = get_cat($catid);
				if($C) {
					$catid = $C['parentid'] ? $C['arrparentid'].','.$catid : $catid;
					$cids .= $catid.',';
					if(strpos($member['keyword'], $C['catname']) === false) $member['keyword'] .= ','.$C['catname'];
				}
			}
			$cids = array_unique(explode(',', substr(str_replace(',0,', ',', ','.$cids), 1, -1)));
			$member['catids'] = ','.implode(',', $cids).',';
		}
		return $member;
	}

	function add($member) {
		global $DT, $MOD, $L;
		$member = $this->set($member);
		$member['linkurl'] = userurl($member['username']);
		$password = $member['password'];
		$member['passsalt'] = random(8);
		$member['paysalt'] = random(8);
		$member['password'] = dpassword($password, $member['passsalt']);
		$member['payword'] = dpassword($password, $member['paysalt']);
		$member['sound'] = 1;
		$member_fields = array('username','company','passport', 'password','payword','email','sound','gender','truename','mobile','qq','wx','wxqr','ali','skype','department','career','groupid','regid','areaid','edittime','inviter','passsalt', 'paysalt','enterprise','sign');
		$misc_fields = array('username','idtype','idno','bank','branch','account','cover','reply','reason','send');
		$company_fields = array('username','groupid','company','taxid','invoice','agent','checkorder','bill','type','areaid','catid','catids','business', 'mode','regyear','capital','regunit','size','address','postcode','telephone','fax','mail','gzh','gzhqr','homepage','sell','buy','introduce','thumb','keyword','linkurl');
		$member_sqlk = $member_sqlv = $misc_sqlk = $misc_sqlv = $company_sqlk = $company_sqlv = '';
		foreach($member as $k=>$v) {
			if(in_array($k, $member_fields)) {$member_sqlk .= ','.$k; $member_sqlv .= ",'$v'";}
			if(in_array($k, $company_fields)) {$company_sqlk .= ','.$k; $company_sqlv .= ",'$v'";}
			if(in_array($k, $misc_fields)) {$misc_sqlk .= ','.$k; $misc_sqlv .= ",'$v'";}
		}
        $member_sqlk = substr($member_sqlk, 1);
        $member_sqlv = substr($member_sqlv, 1);
        $misc_sqlk = substr($misc_sqlk, 1);
        $misc_sqlv = substr($misc_sqlv, 1);
        $company_sqlk = substr($company_sqlk, 1);
        $company_sqlv = substr($company_sqlv, 1);
		DB::query("INSERT INTO {$this->table_member} ($member_sqlk,regip,regtime,loginip,logintime)  VALUES ($member_sqlv,'".DT_IP."','".DT_TIME."','".DT_IP."','".DT_TIME."')");
		$this->userid = DB::insert_id();
		if(!$this->userid) return 0;
		$member['userid'] = $this->userid;
		$this->username = $member['username'];
	    DB::query("INSERT INTO {$this->table_member_misc} (userid, $misc_sqlk) VALUES ('$this->userid', $misc_sqlv)");
	    DB::query("INSERT INTO {$this->table_company} (userid, $company_sqlk) VALUES ('$this->userid', $company_sqlv)");
		$content_table = content_table(4, $this->userid, is_file(DT_CACHE.'/4.part'), $this->table_company_data);
	    DB::query("INSERT INTO {$content_table} (userid, content) VALUES ('$this->userid', '$member[content]')");
		if($MOD['credit_register'] > 0) {
			credit_add($this->username, $MOD['credit_register']);
			credit_record($this->username, $MOD['credit_register'], 'system', $L['member_record_reg'], DT_IP);
		}
		if($MOD['money_register'] > 0) {
			money_add($this->username, $MOD['money_register']);
			money_record($this->username, $MOD['money_register'], $L['in_site'], 'system', $L['member_record_reg'], DT_IP);
		}
		if($MOD['sms_register'] > 0) {
			sms_add($this->username, $MOD['sms_register']);
			sms_record($this->username, $MOD['sms_register'], 'system', $L['member_record_reg'], DT_IP);
		}
		if($member['inviter']) $this->agent($member);
		$this->bind($member);
		clear_upload($member['thumb'].$member['cover'].$member['wxqr'].$member['gzhqr'].$member['content'], $this->userid, 'company');
		return $this->userid;
	}

	function edit($member)	{
		$member = $this->set($member);
		$r = $this->get_one();
		$member_fields = array('company','shop','passport','sound','email','qq','wx','wxqr','ali','skype','gender','truename','mobile','department','career','groupid','areaid', 'edittime','vemail','vmobile','vbank','vtruename','vcompany','vshop','enterprise','support','inviter','sign');
		$misc_fields = array('username','idtype','idno','bank','branch','account','cover','reply','reason', 'send');
		$company_fields = array('company','taxid','invoice','agent','checkorder','bill','type','areaid', 'catid','catids','business','mode','regyear','capital','regunit','size','address','lng','lat','postcode','telephone','fax','mail','gzh','gzhqr','homepage','sell','buy','introduce','thumb','keyword','linkurl','groupid','domain','icp','wan','validated','validator','validtime');
		$member_sql = $misc_sql = $company_sql = '';
		foreach($member as $k=>$v) {
			if(in_array($k, $member_fields)) $member_sql .= ",$k='$v'";
			if(in_array($k, $misc_fields)) $misc_sql .= ",$k='$v'";
			if(in_array($k, $company_fields)) $company_sql .= ",$k='$v'";
		}
		if($member['password']) {
			$passsalt = random(8);
			$password = dpassword($member['password'], $passsalt);
			$member_sql .= ",password='$password',passsalt='$passsalt'";
		}
		if($member['payword']) {
			$paysalt = random(8);
			$payword = dpassword($member['payword'], $paysalt);
			$member_sql .= ",payword='$payword',paysalt='$paysalt'";
		}
        $member_sql = substr($member_sql, 1);
        $misc_sql = substr($misc_sql, 1);
        $company_sql = substr($company_sql, 1);
	    DB::query("UPDATE {$this->table_member} SET $member_sql WHERE userid=$this->userid");
	    DB::query("UPDATE {$this->table_member_misc} SET $misc_sql WHERE userid=$this->userid");
	    DB::query("UPDATE {$this->table_company} SET $company_sql WHERE userid=$this->userid");
		$content_table = content_table(4, $this->userid, is_file(DT_CACHE.'/4.part'), $this->table_company_data);
	    DB::query("UPDATE {$content_table} SET content='$member[content]' WHERE userid=$this->userid");
		$member['userid'] = $this->userid;
		$member['vip'] = $r['vip'];
		userclean($r['username']);
		clear_upload($member['thumb'].$member['cover'].$member['wxqr'].$member['gzhqr'].$member['content'], $this->userid, 'company');
		return true;
	}

	function get_one($username = '') {
		$condition = $username ? "username='$username'" : "userid=$this->userid";
		$r1 = DB::get_one("SELECT * FROM {$this->table_member} WHERE {$condition}");
		if(!$r1) return array();
		$r2 = DB::get_one("SELECT * FROM {$this->table_member_misc} WHERE {$condition}");
		$r3 = DB::get_one("SELECT * FROM {$this->table_company} WHERE {$condition}");
        return array_merge($r1, $r2, $r3);
	}

	function get_list($condition, $order = 'userid DESC') {
		global $pages, $page, $pagesize, $offset, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table_member} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table_member} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$r['logindate'] = timetodate($r['logintime'], 5);
			$r['regdate'] = timetodate($r['regtime'], 5);
			$lists[] = $r;
		}
		return $lists;
	}

	function get_list_misc($condition, $order = 'userid DESC') {
		global $pages, $page, $pagesize, $offset, $sum;
		if($page > 1 && $sum) {
			$items = $sum;
		} else {
			$r = DB::get_one("SELECT COUNT(*) AS num FROM {$this->table_member_misc} WHERE {$condition}");
			$items = $r['num'];
		}
		$pages = pages($items, $page, $pagesize);
		if($items < 1) return array();
		$lists = array();
		$result = DB::query("SELECT * FROM {$this->table_member_misc} WHERE {$condition} ORDER BY {$order} LIMIT {$offset},{$pagesize}");
		while($r = DB::fetch_array($result)) {
			$lists[] = $r;
		}
		return $lists;
	}

	function join($lists, $tb = 'member_misc', $fd = 'note,reason') {
		$arr = array();
		$ids = '';
		foreach($lists as $k=>$v) {
			$arr[$v['userid']] = $k;
			$ids .= ','.$v['userid'];
		}
		if($ids) {
			if($fd != '*' && strpos($fd, 'userid') === false) $fd = 'userid,'.$fd;
			$ids = substr($ids, 1);
			$result = DB::query("SELECT {$fd} FROM ".DT_PRE."{$tb} WHERE userid IN ($ids)");
			while($r = DB::fetch_array($result)) {
				$userid = $r['userid'];
				$lists[$arr[$userid]] += $r;
			}
		}
		return $lists;
	}

	function update($username, $member = array()) {		
		global $GROUP, $MODULE;
		$GROUP or $GROUP = cache_read('group.php');
		$member or $member = userinfo($username);
		if(!$member) return false;
		$userid = $member['userid'];
		$msql = $csql = '';
		$gradeid = $this->get_gradeid($member['credit']);
		if($gradeid != $member['gradeid']) $msql .= ",gradeid=$gradeid";
		$enterprise = $GROUP[$member['groupid']]['type'] ? 1 : 0;
		if($enterprise != $member['enterprise']) $msql .= ",enterprise=$enterprise";
		$validate = $member['validated'] ? ($enterprise ? 2 : 1) : 0;
		if($validate != $member['validate']) $msql .= ",validate=$validate";
		$moments = isset($MODULE[20]) ? DB::count(DT_PRE.'moment_20', "userid=$userid AND status=3") : 0;
		if($moments != $member['moments']) $msql .= ",moments=$moments";
		$fans = DB::count(DT_PRE.'follow', "fuserid=$userid");
		if($fans != $member['fans']) $msql .= ",fans=$fans";
		$follows = DB::count(DT_PRE.'follow', "userid=$userid");
		if($follows != $member['follows']) $msql .= ",follows=$follows";
		if($msql) DB::query("UPDATE {$this->table_member} SET ".substr($msql, 1)." WHERE userid=$userid");
		if($csql) DB::query("UPDATE {$this->table_company} SET ".substr($csql, 1)." WHERE userid=$userid");
		if($msql || $csql) userclean($username);
		return true;
	}

	function login($login_username, $login_password, $login_cookietime = 0, $reason = '', $child = 0) {
		global $DT, $MOD, $MODULE, $L;
		if(!check_name($login_username)) return $this->_($L['member_login_username_bad']);
		if(!$MOD || !isset($MOD['login_times'])) $MOD = cache_read('module-2.php');
		$login_lock = ($MOD['login_times'] && $MOD['lock_hour']) ? true : false;
		$LOCK = array();
		if($login_lock) {
			$LOCK = cache_read(DT_IP.'.php', 'ban');
			if($LOCK) {
				if(DT_TIME - $LOCK['time'] < $MOD['lock_hour']*3600) {
					if($LOCK['times'] >= $MOD['login_times']) return $this->_(lang($L['member_login_ban'], array($MOD['login_times'], $MOD['lock_hour'])));
				} else {
					$LOCK = array();
					cache_delete(DT_IP.'.php', 'ban');
				}
			}
		}
		$user = userinfo($login_username, 0);
		if(!$user) {
			$this->lock($login_lock, $LOCK);
			return $this->_($L['member_login_not_member']);
		}
		if($user['groupid'] == 2) return $this->_($L['member_login_member_ban']);
		if($reason) {
			if(defined('DT_ADMIN')) {
				$this->login_log($login_username, '', '', 1, '', $reason);
			} else {
				if($DT['login_log'] == 2) $this->login_log($login_username, '', '', 0, '', $reason);
			}
		} else {
			if($user['password'] != dpassword($login_password, $user['passsalt'])) {
				$this->lock($login_lock, $LOCK);
				return $this->_($L['member_login_password_bad']);
			}
		}
		$userid = $user['userid'];
		if($MOD['credit_login'] > 0 && timetodate(DT_TIME, 3) != timetodate($user['logintime'], 3) && !$reason) {
			credit_add($login_username, $MOD['credit_login']);
			credit_record($login_username, $MOD['credit_login'], 'system', $L['member_record_login'], DT_IP);
			$user['credit'] += $MOD['credit_login'];
		}
		$child = intval($child);		
		$cookietime = $login_cookietime ? DT_TIME + intval($login_cookietime) : 0;
		$auth = encrypt($user['userid'].'|'.$user['password'].'|'.$child, DT_KEY.'USER');
		set_cookie('auth', $auth, $cookietime);
		set_cookie('username', $user['username'], DT_TIME + 30*86400);
		DB::query("UPDATE {$this->table_member} SET loginip='".DT_IP."',logintime=".DT_TIME.",logintimes=logintimes+1 WHERE userid=$userid");
		if($child) DB::query("UPDATE {$this->table_member}_child SET loginip='".DT_IP."',logintime=".DT_TIME.",logintimes=logintimes+1 WHERE itemid=$child");
		$this->cart($userid);
		$this->bind($user);
		$this->update($login_username, $user);
		return $user;
	}

	function cart($userid) {//SYNC
		$r = DB::get_one("SELECT data FROM ".DT_PRE."cart WHERE userid=$userid");
		if($r && $r['data']) {
			$cart = unserialize($r['data']);
			set_cookie('cart', count($cart), DT_TIME + 30*86400);
		}
	}

	function bind($user) {
		if(!$user) return;
		$username = $user['username'];
		$userid = $user['userid'];
		$auth = get_cookie('bind');
		if($auth) {
			set_cookie('bind', '');
			$auth = decrypt($auth, DT_KEY.'BIND');
			if(strpos($auth, '|') !== false) {
				$t = explode('|', $auth);
				$itemid = intval($t[0]);
				$site = check_name($t[1]) ? $t[1] : '';
				if($itemid && $site) {
					$O = DB::get_one("SELECT * FROM ".DT_PRE."oauth WHERE itemid=$itemid");
					if($O && $O['site'] == $site && $O['username'] != $username) {						
						DB::query("UPDATE ".DT_PRE."oauth SET username='' WHERE username='$username' AND site='$site'");
						DB::query("UPDATE ".DT_PRE."oauth SET username='$username' WHERE itemid=$itemid");
					}
				}
			}
		}
		$weixin_openid = $wxmini_openid = $wxmini_phone = '';
		$auth = get_cookie('weixin_openid');
		if($auth) $weixin_openid = decrypt($auth, DT_KEY.'WXID');
		$auth = get_cookie('wxmini_openid');
		if($auth) $wxmini_openid = decrypt($auth, DT_KEY.'WXID');
		$auth = get_cookie('wxmini_phone');
		if($auth) $wxmini_phone = decrypt($auth, DT_KEY.'WXID');
		//CHIP weixin@oauth.class
		$W = array();
		if(is_openid($weixin_openid)) {
			$W = DB::get_one("SELECT * FROM ".DT_PRE."weixin_user WHERE openid='$weixin_openid'");
			if($W && $W['username'] != $username) {
				DB::query("UPDATE ".DT_PRE."weixin_user SET username='' WHERE username='$username'");
				DB::query("UPDATE ".DT_PRE."weixin_user SET username='$username' WHERE itemid=$W[itemid]");
				$W['username'] = $username;
			}
			if($W) {
				$O = DB::get_one("SELECT * FROM ".DT_PRE."oauth WHERE openid='$weixin_openid' AND site='weixin'");
				if(!$O) {
					DB::query("INSERT INTO ".DT_PRE."oauth (username,site,openid,addtime,logintime,loginip,logintimes) VALUES ('$username','weixin','$weixin_openid','".DT_TIME."','".DT_TIME."','".DT_IP."','1')");
					$itemid = DB::insert_id();
					$O = DB::get_one("SELECT * FROM ".DT_PRE."oauth WHERE itemid=$itemid");
				}
				if($O) {//SYNC
					$sql = "logintimes=".$W['logintimes'].",logintime=".$W['logintime'].",loginip='".$W['loginip']."'";
					if($O['addtime'] != $W['addtime']) $sql .= ",addtime='".$W['addtime']."'";
					if($O['username'] != $W['username']) $sql .= ",username='".$W['username']."'";
					if($O['nickname'] != $W['nickname']) $sql .= ",nickname='".addslashes($W['nickname'])."'";
					if($O['avatar'] != $W['headimgurl']) $sql .= ",avatar='".addslashes($W['headimgurl'])."'";
					DB::query("UPDATE ".DT_PRE."oauth SET {$sql} WHERE itemid=$O[itemid]");
				}
			}
		}
		if(is_openid($wxmini_openid)) {
			$M = DB::get_one("SELECT itemid,username,nickname,avatar,mobile FROM ".DT_PRE."oauth WHERE openid='$wxmini_openid' AND site='wxmini'");
			if($M) {
				$sql = "";
				if($M['username'] != $username) {
					DB::query("UPDATE ".DT_PRE."oauth SET username='' WHERE username='$username' AND site='wxmini'");							
					$sql .= ",username='$username'";
				}
				if($W && $W['nickname'] && $W['nickname'] != $M['nickname']) $sql .= ",nickname='".addslashes($W['nickname'])."'";
				if($W && $W['headimgurl'] && $W['headimgurl'] != $M['avatar']) $sql .= ",avatar='".addslashes($W['headimgurl'])."'";
				if((is_mobile($wxmini_phone) && $wxmini_phone != $M['mobile'])) $sql .= ",mobile='$wxmini_phone'";
				if($sql) DB::query("UPDATE ".DT_PRE."oauth SET ".substr($sql, 1)." WHERE itemid=$M[itemid]");
			}
		}
		if(is_mobile($wxmini_phone) && (!is_mobile($user['mobile']) || !$user['vmobile'])) {
			DB::query("UPDATE ".DT_PRE."member SET vmobile=0 WHERE mobile='$wxmini_phone'");
			DB::query("UPDATE ".DT_PRE."member SET vmobile=1,mobile='$wxmini_phone' WHERE userid=$userid");
			DB::query("INSERT INTO ".DT_PRE."validate (title,history,type,username,ip,addtime,status,editor,edittime) VALUES ('$wxmini_phone','$user[mobile]','mobile','$username','".DT_IP."','".DT_TIME."','3','wxmini','".DT_TIME."')");
			userclean($username);
		}
	}

	function lock($login_lock, $LOCK) {
		if($login_lock) {
			$LOCK['time'] = DT_TIME;
			$LOCK['times'] = isset($LOCK['times']) ? $LOCK['times'] + 1 : 1;
			cache_write(DT_IP.'.php', $LOCK, 'ban');
		}
	}

	function logout() {
		global $_userid;
		DB::query("DELETE FROM ".DT_PRE."online WHERE userid=$_userid");
		set_cookie('auth', '');
		set_cookie('userid', '');
		set_cookie('username', '');
		return true;
	}

	function delete($userid) {
		global $dc, $CFG, $MODULE, $L;
		if(!$userid) return false;
		if(is_array($userid)) {
			foreach($userid as $uid) {
				if($uid == 1 || is_founder($uid)) return $this->_($L['member_founder_del']);
			}
			$userids = implode(',', $userid);
		} else {
			if($userid == 1 || is_founder($userid)) return $this->_($L['member_founder_del']);
			$userids = intval($userid);
		}
		$result = DB::query("SELECT username,userid FROM {$this->table_member} WHERE userid IN ($userids)");
		while($r = DB::fetch_array($result)) {
			$userid = $r['userid'];
			$username = $r['username'];
			if(!$userid || !$username) continue;
			$content_table = content_table(4, $userid, is_file(DT_CACHE.'/4.part'), $this->table_company_data);
			$content_table = str_replace(DT_PRE, '', $content_table);
			foreach(array('address', 'admin_log', 'alert', 'ask', 'comment', 'follow', 'history', 'honor', 'gift_order', 'guestbook', 'link', 'login', 'mail_list', 'spread', 'member_upgrade', 'oauth', 'oauth_login', 'poll_record', 'upload_convert', 'vote_record', 'weixin_code', 'weixin_bind', 'weixin_user', 'member_check', 'member_blacklist') as $v) {
				$this->deluser($v, $username, true);
			}
			foreach(array('news', 'page') as $v) {
				$this->deluser($v, $username, true, true);
			}
			foreach($MODULE as $m) {
				if($m['islink'] || $m['moduleid'] < 5) continue;
				$this->deluser($m['module'].'_'.$m['moduleid'], $username, true, true, $m['moduleid']);
			}
			DB::query("DELETE FROM ".DT_PRE."cart WHERE userid='$userid'");
			DB::query("DELETE FROM ".DT_PRE."type WHERE item='friend-".$userid."'");
			DB::query("DELETE FROM ".DT_PRE."type WHERE item='favorite-".$userid."'");
			DB::query("DELETE FROM ".DT_PRE."type WHERE item='sell-5-".$userid."'");
			DB::query("DELETE FROM ".DT_PRE."type WHERE item='news-".$userid."'");
			DB::query("DELETE FROM ".DT_PRE."type WHERE item='mall-16-".$userid."'");
			foreach(array('admin', 'favorite', 'friend', 'company_setting', $content_table, 'member_misc', 'company', 'member') as $v) {
				$this->deluser($v, $userid, false);
			}
			userclean($username);
			$this->delupload($username, $userid);
		}
		return true;
	}

	function deluser($table, $user, $name = true, $data = false, $moduleid = 0) {
		global $MODULE;
		if(!$user) return;
		$fields = $name ? 'username' : 'userid';
		$module = $moduleid ? $MODULE[$moduleid]['module'] : '';
		if($data) {
			$result = DB::query("SELECT * FROM ".DT_PRE."{$table} WHERE `$fields`='$user'");
			while($r = DB::fetch_array($result)) {
				$itemid = $r['itemid'];
				DB::query("DELETE FROM ".DT_PRE."{$table} WHERE itemid='$itemid'");
				$table_data = strpos($table, '_') === false ? $table.'_data' : str_replace('_', '_data_', $table);
				$table_data = DT_PRE.$table_data;
				if($moduleid) $table_data = content_table($moduleid, $itemid, is_file(DT_CACHE.'/'.$moduleid.'.part'), $table_data);
				DB::query("DELETE FROM {$table_data} WHERE itemid='$itemid'");
				if($module == 'sell') {
					DB::query("DELETE FROM ".DT_PRE."sell_search_{$moduleid} WHERE itemid=$itemid");
				}
				if($moduleid && $r['linkurl'] && strpos($r['linkurl'], '://') === false && strpos($r['linkurl'], '.php') === false && strpos($r['linkurl'], 'show-') === false) {
					$html = DT_ROOT.'/'.$MODULE[$moduleid]['moduledir'].'/'.$r['linkurl'];
					if(is_file($html)) file_del($html);
				}
			}
		} else {
			DB::query("DELETE FROM ".DT_PRE."{$table} WHERE `$fields`='$user'");
		}
	}

	function delupload($username, $userid) {
		if(!$userid || !$username) return;
		$result = DB::query("SELECT fileurl FROM ".DT_PRE."upload_".($userid%10)." WHERE username='$username'");
		while($r = DB::fetch_array($result)) {
			 delete_upload($r['fileurl'], $userid);
		}
	}

	function rename($cusername, $nusername) {
		global $MODULE, $L, $_username;
		$cusername = trim($cusername);
		$nusername = trim($nusername);
		if($cusername == $nusername) return false;
		if(!$this->username_exists($cusername)) return $this->_($L['member_rename_not_member']);
		if(!$this->is_username($nusername)) return false;
		$tables = array('address', 'alert', 'agent', 'app_bind', 'app_push', 'app_scan', 'ask', 'chat_data_0', 'chat_data_1', 'chat_data_2', 'chat_data_3', 'chat_data_4', 'chat_data_5', 'chat_data_6', 'chat_data_7', 'chat_data_8', 'chat_data_9','upload_convert', 'upload_0', 'upload_1', 'upload_2', 'upload_3', 'upload_4', 'upload_5', 'upload_6', 'upload_7', 'upload_8', 'upload_9','comment', 'finance_award', 'finance_card', 'finance_cash', 'finance_charge', 'finance_coupon', 'finance_pay', 'finance_promo', 'finance_deposit', 'finance_record', 'finance_sms', 'form_answer', 'form_record', 'guestbook', 'honor', 'link', 'admin_log', 'login', 'mail_list', 'spread', 'news', 'page', 'oauth', 'vote_record', 'gift_order', 'poll_record', 'weixin_bind', 'weixin_user', 'member_check', 'member_upgrade', 'member_blacklist', 'company_check', 'company_vip', 'validate', 'follow', 'history', 'oauth_login', 'company', 'member_misc', 'member');
		foreach($MODULE as $m) {
			if($m['islink'] || $m['moduleid'] < 5) continue;
			$tables[] = $m['module'].'_'.$m['moduleid'];
			if($m['module'] == 'mall') {
				$tables[] = $m['module'].'_express_'.$m['moduleid'];
				DB::query("UPDATE ".DT_PRE."mall_stat_".$m['moduleid']." SET buyer='$nusername' WHERE buyer='$cusername'");
				DB::query("UPDATE ".DT_PRE."mall_stat_".$m['moduleid']." SET seller='$nusername' WHERE seller='$cusername'");
				DB::query("UPDATE ".DT_PRE."mall_comment_".$m['moduleid']." SET buyer='$nusername' WHERE buyer='$cusername'");
				DB::query("UPDATE ".DT_PRE."mall_comment_".$m['moduleid']." SET seller='$nusername' WHERE seller='$cusername'");
			}
			if($m['module'] == 'club') {
				$tables[] = $m['module'].'_fans_'.$m['moduleid'];
				$tables[] = $m['module'].'_group_'.$m['moduleid'];
				$tables[] = $m['module'].'_manage_'.$m['moduleid'];
				$tables[] = $m['module'].'_reply_'.$m['moduleid'];
			}
			if($m['module'] == 'job') {
				$tables[] = $m['module'].'_talent_'.$m['moduleid'];
				DB::query("UPDATE ".DT_PRE."job_apply_".$m['moduleid']." SET apply_username='$nusername' WHERE apply_username='$cusername'");
			}
			if($m['module'] == 'know') {
				$tables[] = $m['module'].'_answer_'.$m['moduleid'];
				$tables[] = $m['module'].'_expert_'.$m['moduleid'];
				$tables[] = $m['module'].'_vote_'.$m['moduleid'];
			}
			if($m['module'] == 'quote') {
				$tables[] = $m['module'].'_price_'.$m['moduleid'];
			}
			if($m['module'] == 'exhibit') {
				$tables[] = $m['module'].'_sign_'.$m['moduleid'];
			}
			if($m['module'] == 'special') {
				$tables[] = $m['module'].'_item_'.$m['moduleid'];
			}
			if($m['module'] == 'moment') {
				$tables[] = $m['module'].'_topic_'.$m['moduleid'];
			}
			if($m['module'] == 'group') {
				DB::query("UPDATE ".DT_PRE."group_order_".$m['moduleid']." SET buyer='$nusername' WHERE buyer='$cusername'");
				DB::query("UPDATE ".DT_PRE."group_order_".$m['moduleid']." SET seller='$nusername' WHERE seller='$cusername'");
			}
		}
		foreach($tables as $table) {
			DB::query("UPDATE ".DT_PRE."{$table} SET username='$nusername' WHERE username='$cusername'");
		}
		DB::query("UPDATE ".DT_PRE."chat SET fromuser='$nusername' WHERE fromuser='$cusername'");
		DB::query("UPDATE ".DT_PRE."chat SET touser='$nusername' WHERE touser='$cusername'");
		DB::query("UPDATE ".DT_PRE."order SET buyer='$nusername' WHERE buyer='$cusername'");
		DB::query("UPDATE ".DT_PRE."order SET seller='$nusername' WHERE seller='$cusername'");
		DB::query("UPDATE ".DT_PRE."order_service SET buyer='$nusername' WHERE buyer='$cusername'");
		DB::query("UPDATE ".DT_PRE."order_service SET seller='$nusername' WHERE seller='$cusername'");
		DB::query("UPDATE ".DT_PRE."contract SET buyer='$nusername' WHERE buyer='$cusername'");
		DB::query("UPDATE ".DT_PRE."contract SET seller='$nusername' WHERE seller='$cusername'");
		DB::query("UPDATE ".DT_PRE."invoice SET buyer='$nusername' WHERE buyer='$cusername'");
		DB::query("UPDATE ".DT_PRE."invoice SET seller='$nusername' WHERE seller='$cusername'");
		DB::query("UPDATE ".DT_PRE."message SET fromuser='$nusername' WHERE fromuser='$cusername'");
		DB::query("UPDATE ".DT_PRE."message SET touser='$nusername' WHERE touser='$cusername'");		
		DB::query("UPDATE ".DT_PRE."agent SET pusername='$nusername' WHERE pusername='$cusername'");		
		DB::query("UPDATE ".DT_PRE."follow SET fusername='$nusername' WHERE fusername='$cusername'");		
		DB::query("UPDATE ".DT_PRE."member_blacklist SET busername='$nusername' WHERE busername='$cusername'");		
		DB::query("INSERT INTO ".DT_PRE."validate (title,history,type,username,ip,addtime,status,editor,edittime) VALUES ('$nusername','$cusername','username','$nusername','".DT_IP."','".DT_TIME."','3','$_username','".DT_TIME."')");
		userclean($cusername);
		return true;
	}

	function rename_passport($cpassport, $npassport, $username) {
		global $MODULE, $_username;
		if($cpassport == $npassport) return false;
		if(!$this->is_passport($npassport)) return false;
		DB::query("UPDATE ".DT_PRE."chat_data_0 SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."chat_data_1 SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."chat_data_2 SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."chat_data_3 SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."chat_data_4 SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."chat_data_5 SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."chat_data_6 SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."chat_data_7 SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."chat_data_8 SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."chat_data_9 SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."chat SET fpassport='$npassport' WHERE fpassport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."chat SET tpassport='$npassport' WHERE tpassport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."comment SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."follow SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."follow SET fpassport='$npassport' WHERE fpassport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."friend SET fpassport='$npassport' WHERE fpassport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."member_blacklist SET bpassport='$npassport' WHERE bpassport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."message SET fpassport='$npassport' WHERE fpassport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."message SET tpassport='$npassport' WHERE tpassport='$cpassport'");
		DB::query("UPDATE ".DT_PRE."order SET buyer_passport='$npassport' WHERE buyer_passport='$cpassport'");
		foreach($MODULE as $m) {
			if($m['islink'] || $m['moduleid'] < 5) continue;
			if($m['module'] == 'club') {
				DB::query("UPDATE ".DT_PRE."club_".$m['moduleid']." SET passport='$npassport' WHERE passport='$cpassport'");
				DB::query("UPDATE ".DT_PRE."club_fans_".$m['moduleid']." SET passport='$npassport' WHERE passport='$cpassport'");
				DB::query("UPDATE ".DT_PRE."club_group_".$m['moduleid']." SET passport='$npassport' WHERE passport='$cpassport'");
				DB::query("UPDATE ".DT_PRE."club_reply_".$m['moduleid']." SET passport='$npassport' WHERE passport='$cpassport'");
				DB::query("UPDATE ".DT_PRE."club_".$m['moduleid']." SET replyer='$npassport' WHERE replyer='$cpassport'");
			}
			if($m['module'] == 'know') {
				DB::query("UPDATE ".DT_PRE."know_".$m['moduleid']." SET passport='$npassport' WHERE passport='$cpassport'");
				DB::query("UPDATE ".DT_PRE."know_answer_".$m['moduleid']." SET passport='$npassport' WHERE passport='$cpassport'");
				DB::query("UPDATE ".DT_PRE."know_expert_".$m['moduleid']." SET passport='$npassport' WHERE passport='$cpassport'");
				DB::query("UPDATE ".DT_PRE."know_vote_".$m['moduleid']." SET passport='$npassport' WHERE passport='$cpassport'");
			}
			if($m['module'] == 'moment') {
				DB::query("UPDATE ".DT_PRE."moment_".$m['moduleid']." SET passport='$npassport' WHERE passport='$cpassport'");
			}
			if($m['module'] == 'group') {
				DB::query("UPDATE ".DT_PRE."group_order_".$m['moduleid']." SET buyer_passport='$npassport' WHERE buyer_passport='$cpassport'");
			}
		}
		DB::query("UPDATE ".DT_PRE."member SET passport='$npassport' WHERE passport='$cpassport'");
		DB::query("INSERT INTO ".DT_PRE."validate (title,history,type,username,ip,addtime,status,editor,edittime) VALUES ('$npassport','$cpassport','passport','$username','".DT_IP."','".DT_TIME."','3','$_username','".DT_TIME."')");
		userclean($username);
		return true;
	}

	function move($userid, $groupid) {
		global $CFG, $L;
		if(is_array($userid)) {
			foreach($userid as $v) { $this->move($v, $groupid); }
		} else {
			$userid = intval($userid);
			if($userid == 1 || is_founder($userid)) return $this->_($L['member_founder_move']);
			$this->userid = $userid;
			$user = $this->get_one();
			if($user) {
				DB::query("UPDATE {$this->table_member} SET groupid='$groupid' WHERE userid=$userid");
				DB::query("UPDATE {$this->table_company} SET groupid='$groupid' WHERE userid=$userid");
				userclean($user['username']);
			}
			
		}
		return true;
	}

	function check($userid) {
		if(is_array($userid)) {
			foreach($userid as $v) { $this->check($v); }
		} else {
			$this->userid = $userid;
			$user = $this->get_one();
			if($user) {
				$groupid = $user['regid'] ? $user['regid'] : 6;
				DB::query("UPDATE {$this->table_member} SET groupid=$groupid WHERE userid=$userid");
				DB::query("UPDATE {$this->table_company} SET groupid=$groupid WHERE userid=$userid");
				userclean($user['username']);
			}
			return true;
		}
	}

	function login_log($username, $password, $salt, $admin = 0, $message = '', $type = '') {
		global $L, $DT_PC;
		$password = dpassword($password, $salt);
		$port = get_env('port');
		check_name($type) or $type = $DT_PC ? 'pc' : 'mob';
		$agent = addslashes(dhtmlspecialchars(strip_sql(strip_tags(DT_UA))));
		$message or $message = $L['member_login_ok'];
		if($message == $L['member_login_ok']) cache_delete(DT_IP.'.php', 'ban');
		DB::query("INSERT INTO ".DT_PRE."login (username,password,passsalt,admin,type,loginip,loginport,logintime,message,agent) VALUES ('$username','$password','$salt','$admin','$type','".DT_IP."','".get_env('port')."','".DT_TIME."','$message','$agent')");
	}

	function check_get() {
		$r = DB::get_one("SELECT content FROM {$this->table_member_check} WHERE username='$this->username' AND edittime=0");
		return $r && $r['content'] ? dstripslashes(unserialize($r['content'])) : array();
	}

	function check_add($post) {
		global $_company;
		if(isset($post['content'])) {
			$content = dsafe($post['content']);
			unset($post['content']);
			$post = dhtmlspecialchars($post);
			$post['content'] = $content;
		} else {
			$post = dhtmlspecialchars($post);
		}
		$content = addslashes(serialize($post));
		DB::query("INSERT INTO {$this->table_member_check} (username,company,ip,content,addtime) VALUES ('$this->username','$_company','".DT_IP."','$content','".DT_TIME."')");
	}
	
	function get_uid() {
		DB::query("INSERT INTO ".DT_PRE."uid () VALUES ()");
		return DB::insert_id();
	}

	function get_pwd() {
		global $MOD;
		if(!$MOD['minpassword'] || $MOD['minpassword'] < 6) $MOD['minpassword'] = 6;
		if(!$MOD['maxpassword'] || $MOD['maxpassword'] > 30) $MOD['maxpassword'] = 30;
		if($MOD['minpassword'] > $MOD['maxpassword'] || $MOD['maxpassword'] - $MOD['minpassword'] < 6) $MOD['maxpassword'] = $MOD['minpassword'] + 6;
		return random(1, 'A-Z').random(1, '0-9').random(1, 'a-z').random(1, '@#-=;:').random($MOD['minpassword']);
	}
	
	function get_gradeid($credit) {
		global $GRADE;
		$GRADE or $GRADE = cache_read('grade.php');
		$credit = intval($credit);
		$gradeid = 0;
		foreach($GRADE as $k=>$v) {
			$gradeid = $k;
			if($credit <= $v['credit']) return $gradeid;
		}
		return $gradeid;
	}

	function agent($member) {
		if(check_name($member['inviter'])) {
			$user = userinfo($member['inviter']);
			if($user) {
				$G = cache_read('group-'.$user['groupid'].'.php');
				if($G['agent']) {
					$pusername = $member['username'];
					$username = $user['username'];
					$r = DB::get_one("SELECT itemid FROM ".DT_PRE."agent WHERE username='$username' AND pusername='$pusername'");
					if(!$r) {
						global $L;
						$pcompany = $member['company'];
						$$mobile = $member['$mobile'];
						$company = addslashes($user['company']);
						$reason = $L['member_agent_reason'];
						DB::query("INSERT INTO ".DT_PRE."agent (username,company,pusername,pcompany,mobile,discount,status,reason) VALUES ('$username','$company','$pusername','$pcompany','$mobile','99','2','$reason')");
					}
				}
			} else {
				DB::query("UPDATE {$this->table_member} SET inviter='' WHERE userid=$member[userid]");
			}
		}
	}

	function _($e) {
		$this->errmsg = $e;
		return false;
	}
}
?>