<?php
defined('IN_DESTOON') or exit('Access Denied');
?>
<table cellspacing="0" class="tb">
<tr>
<td class="tl">提示信息：</td>
<td>
以下接口需要申请的是<span class="f_red">即时到帐</span>交易，为了提升用户支付体验，不建议设置手续费，但是可以在用户提现时适当收费
</td>
</tr>
<tr>
<td class="tl"><a href="<?php echo gourl('https://www.alipay.com');?>" target="_blank"><strong>支付宝 Alipay</strong></a></td>
<td>
<label><input type="radio" name="pay[alipay][enable]" value="1"  <?php if($alipay['enable']) echo 'checked';?> onclick="Dd('alipay').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[alipay][enable]" value="0"  <?php if(!$alipay['enable']) echo 'checked';?> onclick="Dd('alipay').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/alipay/icon.png" width="16" align="absmiddle"/> <a href="<?php echo gourl('https://b.alipay.com/signing/productDetail.htm?productId=I1011000290000001000');?>" target="_blank" class="t">[帐号申请]</a>
</td>
</tr>
<tbody style="display:<?php echo $alipay['enable'] ? '' : 'none';?>" id="alipay">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[alipay][name]" value="<?php echo $alipay['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[alipay][order]" value="<?php echo $alipay['order'];?>"/></td>
</tr>
<tr>
<td class="tl">支付宝帐号</td>
<td><input type="text" size="30" name="pay[alipay][email]" value="<?php echo $alipay['email'];?>"/><?php tips('仅支持即时到账接口，新版接口可不填');?></td>
</tr>
<tr>
<td class="tl">合作者partnerID/APPID</td>
<td><input type="text" size="60" name="pay[alipay][partnerid]" value="<?php echo $alipay['partnerid'];?>"/><?php tips('新版接口请填写APPID');?></td>
</tr>
<tr>
<td class="tl">交易安全校验码(key)/私钥</td>
<td><input type="text" size="60" name="pay[alipay][keycode]" value="<?php echo $alipay['keycode'];?>" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/><?php tips('新版接口请填写商户私钥');?></td>
</tr>
<tr>
<td class="tl">支付宝公钥</td>
<td><input type="text" size="60" name="pay[alipay][public]" value="<?php echo $alipay['public'];?>" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/><?php tips('新版接口请填写支付宝公钥，旧版接口不需要填写');?></td>
</tr>
<tr>
<td class="tl">接收服务器通知文件名</td>
<td><input type="text" size="30" name="pay[alipay][notify]" value="<?php echo $alipay['notify'];?>"/> <?php tips('默认为notify.php 保存于 api/pay/alipay/rsa2/notify.php（新版）或 api/pay/alipay/md5/notify.php（旧版）<br/>为了支付安全，建议你修改此文件名，然后在此填写新文件名<br/>完整通知回调地址为：<br/>'.DT_PATH.'api/pay/alipay/rsa2/'.($alipay['notify'] ? $alipay['notify'] : 'notify.php'));?></td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[alipay][percent]" value="<?php echo $alipay['percent'];?>"/> %</td>
</tr>
</tbody>

<tr>
<td class="tl"><a href="<?php echo gourl('https://www.alipay.com');?>" target="_blank"><strong>支付宝手机支付 Alipay</strong></a></td>
<td>
<label><input type="radio" name="pay[aliwap][enable]" value="1"  <?php if($aliwap['enable']) echo 'checked';?> onclick="Dd('aliwap').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[aliwap][enable]" value="0"  <?php if(!$aliwap['enable']) echo 'checked';?> onclick="Dd('aliwap').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/aliwap/icon.png" width="16" align="absmiddle"/> <a href="<?php echo gourl('https://b.alipay.com/signing/productDetail.htm?productId=I1011000290000001001');?>" target="_blank" class="t">[帐号申请]</a>
</td>
</tr>
<tbody style="display:<?php echo $aliwap['enable'] ? '' : 'none';?>" id="aliwap">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[aliwap][name]" value="<?php echo $aliwap['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[aliwap][order]" value="<?php echo $aliwap['order'];?>"/></td>
</tr>
<tr>
<td class="tl">合作者partnerID/APPID</td>
<td><input type="text" size="60" name="pay[aliwap][partnerid]" value="<?php echo $aliwap['partnerid'];?>"/><?php tips('新版接口请填写APPID');?></td>
</tr>
<tr>
<td class="tl">交易安全校验码(key)/私钥</td>
<td><input type="text" size="60" name="pay[aliwap][keycode]" value="<?php echo $aliwap['keycode'];?>" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/><?php tips('新版接口请填写商户私钥');?></td>
</tr>
<tr>
<td class="tl">支付宝公钥</td>
<td><input type="text" size="60" name="pay[aliwap][public]" value="<?php echo $aliwap['public'];?>" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/><?php tips('新版接口请填写支付宝公钥，旧版接口不需要填写');?></td>
</tr>
<tr>
<td class="tl">接收服务器通知文件名</td>
<td><input type="text" size="30" name="pay[aliwap][notify]" value="<?php echo $aliwap['notify'];?>"/> <?php tips('默认为notify.php 保存于 api/pay/aliwap/rsa2/notify.php（新版）或 api/pay/aliwap/md5/notify.php（旧版）<br/>为了支付安全，建议你修改此文件名，然后在此填写新文件名<br/>完整通知回调地址为：<br/>'.DT_PATH.'api/pay/aliwap/rsa2/'.($aliwap['notify'] ? $aliwap['notify'] : 'notify.php'));?></td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[aliwap][percent]" value="<?php echo $aliwap['percent'];?>"/> %</td>
</tr>
</tbody>

<tr>
<td class="tl"><a href="<?php echo gourl('https://www.alipay.com/');?>" target="_blank"><strong>支付宝扫码 AliScan</strong></a></td>
<td>
<label><input type="radio" name="pay[aliscan][enable]" value="1"  <?php if($aliscan['enable']) echo 'checked';?> onclick="Dd('aliscan').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[aliscan][enable]" value="0"  <?php if(!$aliscan['enable']) echo 'checked';?> onclick="Dd('aliscan').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/aliscan/icon.png" width="16" align="absmiddle"/> <a href="<?php echo gourl('https://www.alipay.com/');?>" target="_blank" class="t">[帐号申请]</a> <?php tips('此接口使用了支付宝静态收款码，付款记录需人工审核');?>
</td>
</tr>
<tbody style="display:<?php echo $aliscan['enable'] ? '' : 'none';?>" id="aliscan">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[aliscan][name]" value="<?php echo $aliscan['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[aliscan][order]" value="<?php echo $aliscan['order'];?>"/></td>
</tr>
<tr>
<td class="tl">收款二维码</td>
<td>
<?php if(is_file(DT_ROOT.'/api/pay/aliscan/qrcode.png')) { ?>
<img src="<?php echo DT_PATH;?>api/pay/aliscan/qrcode.png" width="128"/>
<?php } else { ?>
<span class="f_red">二维码未上传</span> <span class="f_gray">请将收款码命名为qrcode.png 上传至api/pay/aliscan/</span>
<?php } ?>
</tr>
<tr>
<td class="tl">收款人帐号</td>
<td><input type="text" size="30" name="pay[aliscan][account]" value="<?php echo $aliscan['account'];?>"/> <?php tips('收款人支付宝手机号或邮箱，无法扫码时提示转账到此帐号');?></td>
</tr>
<tr>
<td class="tl">审核人邮箱</td>
<td><input type="text" size="30" name="pay[aliscan][email]" value="<?php echo $aliscan['email'];?>"/> <?php tips('审核通知邮件发送至此邮箱');?></td>
</tr>
<tr>
<td class="tl">审核人手机</td>
<td><input type="text" size="30" name="pay[aliscan][mobile]" value="<?php echo $aliscan['mobile'];?>"/> <?php tips('审核通知短信发送至此手机');?></td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[aliscan][percent]" value="<?php echo $aliscan['percent'];?>"/> %</td>
</tr>
</tbody>

<tr>
<td class="tl"><a href="<?php echo gourl('https://pay.weixin.qq.com/');?>" target="_blank"><strong>微信支付 Weixin</strong></a></td>
<td>
<label><input type="radio" name="pay[weixin][enable]" value="1"  <?php if($weixin['enable']) echo 'checked';?> onclick="Dd('weixin').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[weixin][enable]" value="0"  <?php if(!$weixin['enable']) echo 'checked';?> onclick="Dd('weixin').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/weixin/icon.png" width="16" align="absmiddle"/> <a href="<?php echo gourl('https://pay.weixin.qq.com/');?>" target="_blank" class="t">[帐号申请]</a>
</td>
</tr>
<tbody style="display:<?php echo $weixin['enable'] ? '' : 'none';?>" id="weixin">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[weixin][name]" value="<?php echo $weixin['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[weixin][order]" value="<?php echo $weixin['order'];?>"/></td>
</tr>
<tr>
<td class="tl">商户编号</td>
<td><input type="text" size="60" name="pay[weixin][partnerid]" value="<?php echo $weixin['partnerid'];?>"/><?php tips('详见开户邮件');?></td>
</tr>
<tr>
<td class="tl">公众号APPID</td>
<td><input type="text" size="60" name="pay[weixin][appid]" value="<?php echo $weixin['appid'];?>"/><?php tips('详见开户邮件');?></td>
</tr>
<tr>
<td class="tl">交易密钥</td>
<td><input type="text" size="60" name="pay[weixin][keycode]" value="<?php echo $weixin['keycode'];?>" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/>&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo gourl('https://pay.weixin.qq.com/index.php/account/api_cert');?>" target="_blank" class="t">[密钥设置]</a></td>
</tr>
<tr>
<td class="tl">接收服务器通知文件名</td>
<td><input type="text" size="30" name="pay[weixin][notify]" value="<?php echo $weixin['notify'];?>"/> <?php tips('默认为notify.php 保存于 api/pay/weixin/notify.php<br/>建议你修改此文件名，然后在此填写新文件名<br/>完整通知回调地址为：<br/>'.DT_PATH.'api/pay/weixin/'.($weixin['notify'] ? $weixin['notify'] : 'notify.php'));?></td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[weixin][percent]" value="<?php echo $weixin['percent'];?>"/> %</td>
</tr>
</tbody>

<tr>
<td class="tl"><a href="<?php echo gourl('https://weixin.qq.com/');?>" target="_blank"><strong>微信扫码 WXScan</strong></a></td>
<td>
<label><input type="radio" name="pay[wxscan][enable]" value="1"  <?php if($wxscan['enable']) echo 'checked';?> onclick="Dd('wxscan').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[wxscan][enable]" value="0"  <?php if(!$wxscan['enable']) echo 'checked';?> onclick="Dd('wxscan').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/wxscan/icon.png" width="16" align="absmiddle"/> <a href="<?php echo gourl('https://weixin.qq.com/');?>" target="_blank" class="t">[帐号申请]</a> <?php tips('此接口使用了微信静态收款码，付款记录需人工审核');?>
</td>
</tr>
<tbody style="display:<?php echo $wxscan['enable'] ? '' : 'none';?>" id="wxscan">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[wxscan][name]" value="<?php echo $wxscan['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[wxscan][order]" value="<?php echo $wxscan['order'];?>"/></td>
</tr>
<tr>
<td class="tl">收款二维码</td>
<td>
<?php if(is_file(DT_ROOT.'/api/pay/wxscan/qrcode.png')) { ?>
<img src="<?php echo DT_PATH;?>api/pay/wxscan/qrcode.png" width="128"/>
<?php } else { ?>
<span class="f_red">二维码未上传</span> <span class="f_gray">请将收款码命名为qrcode.png 上传至api/pay/wxscan/</span>
<?php } ?>
</tr>
<tr>
<td class="tl">收款人帐号</td>
<td><input type="text" size="30" name="pay[wxscan][account]" value="<?php echo $wxscan['account'];?>"/> <?php tips('收款人微信号或手机号，无法扫码时提示转账到此帐号');?></td>
</tr>
<tr>
<td class="tl">审核人邮箱</td>
<td><input type="text" size="30" name="pay[wxscan][email]" value="<?php echo $wxscan['email'];?>"/> <?php tips('审核通知邮件发送至此邮箱');?></td>
</tr>
<tr>
<td class="tl">审核人手机</td>
<td><input type="text" size="30" name="pay[wxscan][mobile]" value="<?php echo $wxscan['mobile'];?>"/> <?php tips('审核通知短信发送至此手机');?></td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[wxscan][percent]" value="<?php echo $wxscan['percent'];?>"/> %</td>
</tr>
</tbody>
<tr>
<td class="tl"><a href="<?php echo gourl('https://im.qq.com/');?>" target="_blank"><strong>QQ扫码 QQScan</strong></a></td>
<td>
<label><input type="radio" name="pay[qqscan][enable]" value="1"  <?php if($qqscan['enable']) echo 'checked';?> onclick="Dd('qqscan').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[qqscan][enable]" value="0"  <?php if(!$qqscan['enable']) echo 'checked';?> onclick="Dd('qqscan').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/qqscan/icon.png" width="16" align="absmiddle"/> <a href="<?php echo gourl('https://im.qq.com/');?>" target="_blank" class="t">[帐号申请]</a> <?php tips('此接口使用了QQ钱包静态收款码，付款记录需人工审核');?>
</td>
</tr>
<tbody style="display:<?php echo $qqscan['enable'] ? '' : 'none';?>" id="qqscan">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[qqscan][name]" value="<?php echo $qqscan['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[qqscan][order]" value="<?php echo $qqscan['order'];?>"/></td>
</tr>
<tr>
<td class="tl">收款二维码</td>
<td>
<?php if(is_file(DT_ROOT.'/api/pay/qqscan/qrcode.png')) { ?>
<img src="<?php echo DT_PATH;?>api/pay/qqscan/qrcode.png" width="128"/>
<?php } else { ?>
<span class="f_red">二维码未上传</span> <span class="f_gray">请将收款码命名为qrcode.png 上传至api/pay/qqscan/</span>
<?php } ?>
</tr>
<tr>
<td class="tl">收款人帐号</td>
<td><input type="text" size="30" name="pay[qqscan][account]" value="<?php echo $qqscan['account'];?>"/> <?php tips('收款人QQ号或手机号，无法扫码时提示转账到此帐号');?></td>
</tr>
<tr>
<td class="tl">审核人邮箱</td>
<td><input type="text" size="30" name="pay[qqscan][email]" value="<?php echo $qqscan['email'];?>"/> <?php tips('审核通知邮件发送至此邮箱');?></td>
</tr>
<tr>
<td class="tl">审核人手机</td>
<td><input type="text" size="30" name="pay[qqscan][mobile]" value="<?php echo $qqscan['mobile'];?>"/> <?php tips('审核通知短信发送至此手机');?></td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[qqscan][percent]" value="<?php echo $qqscan['percent'];?>"/> %</td>
</tr>
</tbody>

<tr>
<td class="tl"><strong>银行汇款 Bank</strong></td>
<td>
<label><input type="radio" name="pay[bank][enable]" value="1"  <?php if($bank['enable']) echo 'checked';?> onclick="Dd('bank').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[bank][enable]" value="0"  <?php if(!$bank['enable']) echo 'checked';?> onclick="Dd('bank').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/bank/icon.png" width="16" align="absmiddle"/> <?php tips('此接口使用了银行汇款方式，付款记录需人工审核<br/>如有收款码，请将收款码命名为qrcode.png 上传至api/pay/bank/');?>
</td>
</tr>
<tbody style="display:<?php echo $bank['enable'] ? '' : 'none';?>" id="bank">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[bank][name]" value="<?php echo $bank['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[bank][order]" value="<?php echo $bank['order'];?>"/></td>
</tr>
<tr>
<td class="tl">银行名称</td>
<td><input type="text" size="30" name="pay[bank][type]" value="<?php echo $bank['type'];?>"/></td>
</tr>
<tr>
<td class="tl">开户分行</td>
<td><input type="text" size="60" name="pay[bank][branch]" value="<?php echo $bank['branch'];?>"/></td>
</tr>
<tr>
<td class="tl">收款户名</td>
<td><input type="text" size="60" name="pay[bank][company]" value="<?php echo $bank['company'];?>"/></td>
</tr>
<tr>
<td class="tl">收款账号</td>
<td><input type="text" size="60" name="pay[bank][account]" value="<?php echo $bank['account'];?>"/></td>
</tr>
<tr>
<td class="tl">审核人邮箱</td>
<td><input type="text" size="30" name="pay[bank][email]" value="<?php echo $bank['email'];?>"/> <?php tips('审核通知邮件发送至此邮箱');?></td>
</tr>
<tr>
<td class="tl">审核人手机</td>
<td><input type="text" size="30" name="pay[bank][mobile]" value="<?php echo $bank['mobile'];?>"/> <?php tips('审核通知短信发送至此手机');?></td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[bank][percent]" value="<?php echo $bank['percent'];?>"/> %</td>
</tr>
</tbody>

<tr>
<td class="tl"><a href="<?php echo gourl('https://www.99bill.com');?>" target="_blank"><strong>快钱支付 99bill</strong></a></td>
<td>
<label><input type="radio" name="pay[kq99bill][enable]" value="1"  <?php if($kq99bill['enable']) echo 'checked';?> onclick="Dd('kq99bill').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[kq99bill][enable]" value="0"  <?php if(!$kq99bill['enable']) echo 'checked';?> onclick="Dd('kq99bill').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/kq99bill/icon.png" width="16" align="absmiddle"/> <a href="<?php echo gourl('https://www.99bill.com/z/pay_net_bank.html');?>" target="_blank" class="t">[帐号申请]</a>
</td>
</tr>
<tbody style="display:<?php echo $kq99bill['enable'] ? '' : 'none';?>" id="kq99bill">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[kq99bill][name]" value="<?php echo $kq99bill['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[kq99bill][order]" value="<?php echo $kq99bill['order'];?>"/></td>
</tr>
<tr>
<td class="tl">商户编号</td>
<td><input type="text" size="60" name="pay[kq99bill][partnerid]" value="<?php echo $kq99bill['partnerid'];?>"/></td>
</tr>
<tr>
<td class="tl">证书文件</td>
<td><input type="text" size="60" name="pay[kq99bill][cert]" value="<?php echo $kq99bill['cert'];?>"/> <?php tips('请将pfx格式证书文件，上传至 api/pay/kq99bill/，证书文件名类似20200212.3000000378110755.pfx<br/>99bill.RSA.cer文件也必须上传至此目录');?></td>
</tr>
<tr>
<td class="tl">接收服务器通知文件名</td>
<td><input type="text" size="30" name="pay[kq99bill][notify]" value="<?php echo $kq99bill['notify'];?>"/> <?php tips('默认为notify.php 保存于 api/pay/kq99bill/notify.php<br/>建议你修改此文件名，然后在此填写新文件名<br/>完整通知回调地址为：<br/>'.DT_PATH.'api/pay/kq99bill/'.($kq99bill['notify'] ? $kq99bill['notify'] : 'notify.php'));?></td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[kq99bill][percent]" value="<?php echo $kq99bill['percent'];?>"/> %</td>
</tr>
</tbody>


<tr>
<td class="tl"><a href="<?php echo gourl('https://www.yeepay.com');?>" target="_blank"><strong>易宝支付 YeePay</strong></a></td>
<td>
<label><input type="radio" name="pay[yeepay][enable]" value="1"  <?php if($yeepay['enable']) echo 'checked';?> onclick="Dd('yeepay').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[yeepay][enable]" value="0"  <?php if(!$yeepay['enable']) echo 'checked';?> onclick="Dd('yeepay').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/yeepay/icon.png" width="16" align="absmiddle"/> <a href="<?php echo gourl('http://www.yeepay.com/productCenter/internetBankingPayment');?>" target="_blank" class="t">[帐号申请]</a>
</td>
</tr>
<tbody style="display:<?php echo $yeepay['enable'] ? '' : 'none';?>" id="yeepay">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[yeepay][name]" value="<?php echo $yeepay['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[yeepay][order]" value="<?php echo $yeepay['order'];?>"/></td>
</tr>
<tr>
<td class="tl">商户编号</td>
<td><input type="text" size="60" name="pay[yeepay][partnerid]" value="<?php echo $yeepay['partnerid'];?>"/></td>
</tr>
<tr>
<td class="tl">商户密钥</td>
<td><input type="text" size="60" name="pay[yeepay][keycode]" value="<?php echo $yeepay['keycode'];?>" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/></td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[yeepay][percent]" value="<?php echo $yeepay['percent'];?>"/> %</td>
</tr>
</tbody>

<tr>
<td class="tl"><a href="<?php echo gourl('http://cn.unionpay.com/');?>" target="_blank"><strong>中国银联 UnionPay</strong></a></td>
<td>
<label><input type="radio" name="pay[upay][enable]" value="1"  <?php if($upay['enable']) echo 'checked';?> onclick="Dd('upay').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[upay][enable]" value="0"  <?php if(!$upay['enable']) echo 'checked';?> onclick="Dd('upay').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/upay/icon.png" width="16" align="absmiddle"/> <a href="<?php echo gourl('https://open.unionpay.com/ajweb/product/detail?id=1');?>" target="_blank" class="t">[帐号申请]</a>
</td>
</tr>
<tbody style="display:<?php echo $upay['enable'] ? '' : 'none';?>" id="upay">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[upay][name]" value="<?php echo $upay['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[upay][order]" value="<?php echo $upay['order'];?>"/></td>
</tr>
<tr>
<td class="tl">商户编号</td>
<td><input type="text" size="60" name="pay[upay][partnerid]" value="<?php echo $upay['partnerid'];?>"/></td>
</tr>
<tr>
<td class="tl">证书文件</td>
<td><input type="text" size="60" name="pay[upay][cert]" value="<?php echo $upay['cert'];?>"/> <?php tips('请将.pfx证书文件上传至 api/pay/upay/，并在这里填写文件名，例如zhengshu.pfx');?></td>
</tr>
<tr>
<td class="tl">证书密码</td>
<td><input type="text" size="60" name="pay[upay][keycode]" value="<?php echo $upay['keycode'];?>" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/></td>
</tr>
<tr>
<td class="tl">接收服务器通知文件名</td>
<td><input type="text" size="30" name="pay[upay][notify]" value="<?php echo $upay['notify'];?>"/> <?php tips('默认为notify.php 保存于 api/pay/upay/notify.php<br/>建议你修改此文件名，然后在此填写新文件名<br/>完整通知回调地址为：<br/>'.DT_PATH.'api/pay/upay/'.($upay['notify'] ? $upay['notify'] : 'notify.php'));?></td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[upay][percent]" value="<?php echo $upay['percent'];?>"/> %</td>
</tr>
</tbody>

<tr>
<td class="tl"><a href="<?php echo gourl('https://www.chinapay.com');?>" target="_blank"><strong>银联在线 ChinaPay</strong></a></td>
<td>
<label><input type="radio" name="pay[chinapay][enable]" value="1"  <?php if($chinapay['enable']) echo 'checked';?> onclick="Dd('chinapay').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[chinapay][enable]" value="0"  <?php if(!$chinapay['enable']) echo 'checked';?> onclick="Dd('chinapay').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/chinapay/icon.png" width="16" align="absmiddle"/> <a href="<?php echo gourl('http://www.chinapay.com/web2016/concern/index.jsp');?>" target="_blank" class="t">[帐号申请]</a>
</td>
</tr>
<tbody style="display:<?php echo $chinapay['enable'] ? '' : 'none';?>" id="chinapay">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[chinapay][name]" value="<?php echo $chinapay['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[chinapay][order]" value="<?php echo $chinapay['order'];?>"/></td>
</tr>
<tr>
<td class="tl">私钥文件</td>
<td><input type="text" size="60" name="pay[chinapay][partnerid]" value="<?php echo $chinapay['partnerid'];?>"/> <?php tips('银联提供的Mer开头的.key文件名，例如MerPrK_808080808080808_20101111222333.key，请将银联提供的两个key文件上传至api/pay/chinapay/目录，另一个key文件名为PgPubk.key<br/>本接口需要 mcrypt 和 bcmath 两个PHP扩展库的支持，请先确认您安装并启用了这两个库');?></td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[chinapay][percent]" value="<?php echo $chinapay['percent'];?>"/> %</td>
</tr>
</tbody>

<tr>
<td class="tl"><a href="<?php echo gourl('https://www.chinabank.com.cn');?>" target="_blank"><strong>网银在线 ChinaBank</strong></a></td>
<td>
<label><input type="radio" name="pay[chinabank][enable]" value="1"  <?php if($chinabank['enable']) echo 'checked';?> onclick="Dd('chinabank').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[chinabank][enable]" value="0"  <?php if(!$chinabank['enable']) echo 'checked';?> onclick="Dd('chinabank').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/chinabank/icon.png" width="16" align="absmiddle"/> <a href="<?php echo gourl('http://www.chinabank.com.cn/product/payment_gateway.jsp');?>" target="_blank" class="t">[帐号申请]</a>
</td>
</tr>
<tbody style="display:<?php echo $chinabank['enable'] ? '' : 'none';?>" id="chinabank">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[chinabank][name]" value="<?php echo $chinabank['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[chinabank][order]" value="<?php echo $chinabank['order'];?>"/></td>
</tr>
<tr>
<td class="tl">商户编号</td>
<td><input type="text" size="60" name="pay[chinabank][partnerid]" value="<?php echo $chinabank['partnerid'];?>"/></td>
</tr>
<tr>
<td class="tl">支付密钥</td>
<td><input type="text" size="60" name="pay[chinabank][keycode]" value="<?php echo $chinabank['keycode'];?>" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/></td>
</tr>
<tr>
<td class="tl">接收服务器通知文件名</td>
<td><input type="text" size="30" name="pay[chinabank][notify]" value="<?php echo $chinabank['notify'];?>"/> <?php tips('默认为notify.php 保存于 api/pay/chinabank/notify.php<br/>建议你修改此文件名，然后在此填写新文件名<br/>完整通知回调地址为：<br/>'.DT_PATH.'api/pay/chinabank/'.($chinabank['notify'] ? $chinabank['notify'] : 'notify.php'));?></td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[chinabank][percent]" value="<?php echo $chinabank['percent'];?>"/> %</td>
</tr>
</tbody>

<tr>
<td class="tl"><a href="<?php echo gourl('https://www.paypal.com');?>" target="_blank"><strong>贝&nbsp;&nbsp;&nbsp;宝 PayPal</strong></a></td>
<td>
<label><input type="radio" name="pay[paypal][enable]" value="1"  <?php if($paypal['enable']) echo 'checked';?> onclick="Dd('paypal').style.display='';"/> 启用</label>&nbsp;&nbsp;&nbsp;&nbsp;
<label><input type="radio" name="pay[paypal][enable]" value="0"  <?php if(!$paypal['enable']) echo 'checked';?> onclick="Dd('paypal').style.display='none';"/> 禁用</label>&nbsp;&nbsp;&nbsp;&nbsp;<img src="api/pay/paypal/icon.png" width="16" align="absmiddle"/> <a href="<?php echo gourl('www.paypal.com');?>" target="_blank" class="t">[帐号申请]</a>
</td>
</tr>
<tbody style="display:<?php echo $paypal['enable'] ? '' : 'none';?>" id="paypal">
<tr>
<td class="tl">显示名称</td>
<td><input type="text" size="30" name="pay[paypal][name]" value="<?php echo $paypal['name'];?>"/></td>
</tr>
<tr>
<td class="tl">显示顺序</td>
<td><input type="text" size="2" name="pay[paypal][order]" value="<?php echo $paypal['order'];?>"/></td>
</tr>
<tr>
<td class="tl">商户帐号</td>
<td><input type="text" size="30" name="pay[paypal][partnerid]" value="<?php echo $paypal['partnerid'];?>"/></td>
</tr>
<tr>
<td class="tl">IPN 通知文件名</td>
<td><input type="text" size="30" name="pay[paypal][notify]" value="<?php echo $paypal['notify'];?>"/> <?php tips('默认为notify.php 保存于 api/pay/paypal/notify.php<br/>建议你修改此文件名，然后在此填写新文件名<br/>完整通知回调地址为：<br/>'.DT_PATH.'api/pay/paypal/'.($paypal['notify'] ? $paypal['notify'] : 'notify.php'));?></td>
</tr>
<tr>
<td class="tl">PDT Token</td>
<td><input type="text" size="60" name="pay[paypal][keycode]" value="<?php echo $paypal['keycode'];?>" onfocus="if(this.value.indexOf('**')!=-1)this.value='';"/> <?php tips('系统默认使用IPN方式通知，如果在Paypal开启了PDT，请在此填写对应的Token，否则请留空');?></td>
</tr>
<tr>
<td class="tl">支付币种</td>
<td><input type="text" size="3" name="pay[paypal][currency]" value="<?php echo $paypal['currency'];?>"/> 值可以为 "CNY"、"USD"、"EUR"、"TWD"、"JPY"等</td>
</tr>
<tr>
<td class="tl">扣除手续费</td>
<td><input type="text" size="2" name="pay[paypal][percent]" value="<?php echo $paypal['percent'];?>"/> %</td>
</tr>
</tbody>
</table>