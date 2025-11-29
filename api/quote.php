<?php
/*
    DESTOON Copyright (C)2008-2099 www.destoon.com
    This is NOT a freeware,Use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');

$action = isset($action) ? $action : '';

switch($action) {
    case 'get_area':
        // 获取地区数据
        $parentid = isset($parentid) ? intval($parentid) : 0;
        $AREA = cache_read('area.php');
        
        $options = '';
        foreach($AREA as $area) {
            if($area['parentid'] == $parentid) {
                $options .= '<option value="'.$area['areaid'].'">'.$area['areaname'].'</option>';
            }
        }
        
        echo $options;
        break;
        
    case 'get_companies':
        // 获取公司列表
        $areaid = isset($areaid) ? intval($areaid) : 0;
        if(!$areaid) {
            echo '<p>请先选择地区</p>';
            exit;
        }
        
        $companies = array();
        $result = $db->query("SELECT itemid, company, thumb, content, telephone, address FROM {$DT_PRE}company WHERE areaid=$areaid AND status=3 AND groupid IN (6,7) ORDER BY listorder DESC, itemid DESC");
        
        if($db->num_rows($result) == 0) {
            echo '<p>该地区暂无服务公司</p>';
            exit;
        }
        
        $output = '<div class="company-grid">';
        $count = 0;
        
        while($r = $db->fetch_array($result)) {
            $count++;
            $output .= '<div class="company-item">';
            $output .= '<label class="company-radio">';
            $output .= '<input type="radio" name="company_id" value="'.$r['itemid'].'" required>';
            $output .= '<div class="company-info">';
            
            if($r['thumb']) {
                $output .= '<div class="company-thumb"><img src="'.imgurl($r['thumb']).'" alt="'.$r['company'].'"></div>';
            }
            
            $output .= '<div class="company-details">';
            $output .= '<h4>'.$r['company'].'</h4>';
            
            if($r['telephone']) {
                $output .= '<p><strong>电话：</strong>'.$r['telephone'].'</p>';
            }
            
            if($r['address']) {
                $output .= '<p><strong>地址：</strong>'.dsubstr($r['address'], 30).'</p>';
            }
            
            if($r['content']) {
                $output .= '<p class="company-desc">'.dsubstr(strip_tags($r['content']), 50).'</p>';
            }
            
            $output .= '</div>';
            $output .= '</div>';
            $output .= '</label>';
            $output .= '</div>';
            
            // 每3个公司换行（PC端）
            if($count % 3 == 0) {
                $output .= '<div class="clearfix"></div>';
            }
        }
        
        $output .= '</div>';
        echo $output;
        break;
        
    case 'send_verify':
        // 发送验证码
        $email = isset($email) ? trim($email) : '';
        $mobile = isset($mobile) ? trim($mobile) : '';
        $verify_type = isset($verify_type) ? $verify_type : '';
        $captcha = isset($captcha) ? trim($captcha) : '';
        
        // 验证验证码
        $session = new dsession();
        if(!isset($_SESSION['captchastr']) || decrypt($_SESSION['captchastr'], DT_KEY.'CPC') != strtoupper($captcha)) {
            echo json_encode(array('status' => 'error', 'message' => '验证码错误'));
            exit;
        }
        
        // 生成验证码
        $verify_code = random(6, '0-9');
        $_SESSION['quote_verify_code'] = md5(($verify_type == 'email' ? $email : $mobile).'|'.$verify_code);
        $_SESSION['quote_verify_time'] = DT_TIME;
        
        if($verify_type == 'email' && $email) {
            // 发送邮件验证码
            $title = '设备故障报价验证码';
            $content = "您的验证码是：<strong>{$verify_code}</strong><br>请在30分钟内完成验证，如非本人操作请忽略。";
            
            if(send_mail($email, $title, $content)) {
                echo json_encode(array('status' => 'success'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => '邮件发送失败'));
            }
            
        } elseif($verify_type == 'mobile' && $mobile) {
            // 发送短信验证码
            $content = "您的验证码是：{$verify_code}，请在30分钟内完成验证。".$DT['sms_sign'];
            
            $result = send_sms($mobile, $content);
            if(strpos($result, $DT['sms_ok']) !== false) {
                echo json_encode(array('status' => 'success'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => '短信发送失败'));
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => '参数错误'));
        }
        break;
        
    case 'verify_code':
        // 验证验证码
        $code = isset($code) ? trim($code) : '';
        $email = isset($email) ? trim($email) : '';
        $mobile = isset($mobile) ? trim($mobile) : '';
        
        $session = new dsession();
        $target = $email ? $email : $mobile;
        
        if(isset($_SESSION['quote_verify_code']) && $_SESSION['quote_verify_code'] == md5($target.'|'.$code)) {
            if(DT_TIME - $_SESSION['quote_verify_time'] < 1800) { // 30分钟有效
                echo json_encode(array('status' => 'success'));
            } else {
                echo json_encode(array('status' => 'error', 'message' => '验证码已过期'));
            }
        } else {
            echo json_encode(array('status' => 'error', 'message' => '验证码错误'));
        }
        break;
        
    default:
        echo json_encode(array('status' => 'error', 'message' => '未知操作'));
        break;
}
?>