<?php
defined('IN_DESTOON') or exit('Access Denied');
$kf_qq = preg_match("/^[0-9a-z]{32,}$/i", $kf) ? $kf : '';
?>
<tr id="kf_post_qq" style="display:none;">
<td class="tl">在线客服帐号</td>
<td class="tr">
<input type="text" name="kf[qq]" id="kf_qq" value="<?php echo $kf_qq;?>" size="50"/>&nbsp;&nbsp;
<a href="https://b.qq.com/" class="t" target="_blank">帐号管理</a>
<br/><br/>
提示：注册后获取的客服代码“...src="http://wpa.b.qq.com/cgi/wpa.php?key=<span class="f_red">XzgwMDAsTA2M18xNDAzMzhf08AwMDYxMDYzXw</span>">...”中<span class="f_red">XzgwMDAsTA2M18xNDAzMzhf08AwMDYxMDYzXw</span>即为客服帐号
</td>
</tr>