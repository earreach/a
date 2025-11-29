<?php
// /api/get_company_detail.php
define('IN_DESTOON', true);
require '../common.inc.php';

header('Content-Type: text/html; charset=utf-8');

$userid = isset($_GET['userid']) ? intval($_GET['userid']) : 0;
if(!$userid) {
  echo '参数错误'; exit;
}

$r = $db->get_one("SELECT userid,company,areaid,telephone,address,linkurl,introduce,thumb FROM {$DT_PRE}company WHERE userid=$userid");
if(!$r) {
  echo '未找到该公司信息'; exit;
}

// 地区文本
$area_text = function_exists('area_pos') ? area_pos($r['areaid'], ' / ') : '';
$area_parts = $area_text ? explode(' / ', $area_text) : array();

// 简单HTML块，你可以按需求美化
?>
<div class="company-detail">
  <h3><?php echo htmlspecialchars($r['company'], ENT_QUOTES, 'UTF-8'); ?></h3>

  <?php if($r['thumb']) { ?>
  <div>
    <img src="<?php echo $r['thumb']; ?>" alt="<?php echo htmlspecialchars($r['company'], ENT_QUOTES, 'UTF-8'); ?>" style="max-width:150px;max-height:150px;">
  </div>
  <?php } ?>

  <?php if($area_parts) { ?>
  <div>
    地区：<br>
    <?php if(isset($area_parts[0])) echo htmlspecialchars($area_parts[0], ENT_QUOTES, 'UTF-8').'<br>'; ?>
    <?php if(isset($area_parts[1])) echo '&nbsp;&nbsp;'.htmlspecialchars($area_parts[1], ENT_QUOTES, 'UTF-8').'<br>'; ?>
    <?php if(isset($area_parts[2])) echo '&nbsp;&nbsp;&nbsp;&nbsp;'.htmlspecialchars($area_parts[2], ENT_QUOTES, 'UTF-8').'<br>'; ?>
  </div>
  <?php } ?>

  <?php if($r['address']) { ?>
  <div>地址：<?php echo htmlspecialchars($r['address'], ENT_QUOTES, 'UTF-8'); ?></div>
  <?php } ?>

  <?php if($r['telephone']) { ?>
  <div>电话：<?php echo htmlspecialchars($r['telephone'], ENT_QUOTES, 'UTF-8'); ?></div>
  <?php } ?>

  <?php if($r['introduce']) { ?>
  <div style="margin-top:8px;">
    简介：<br>
    <?php echo nl2br(htmlspecialchars($r['introduce'], ENT_QUOTES, 'UTF-8')); ?>
  </div>
  <?php } ?>

  <?php if($r['linkurl']) { ?>
  <div style="margin-top:8px;">
    <a href="<?php echo $r['linkurl']; ?>" target="_blank">在新窗口查看完整公司页面</a>
  </div>
  <?php } ?>
</div>
