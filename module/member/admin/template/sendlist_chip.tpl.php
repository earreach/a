<?php
defined('DT_ADMIN') or exit('Access Denied');
?>
<tr>
<td class="tl"><span class="f_red">*</span> 源数据表</td>
<td>
<input type="text" size="60" name="tb" id="tb" value="<?php echo $DT_PRE;?>member" class="f_fd"/></span>
<select onchange="mk(this.value);" id="op">
<option value="">常用条件</option>
<option value="member|groupid>4" selected>全部会员</option>
<?php foreach($GROUP as $k=>$v) { 
	if($v['groupid'] != 3) { 
?>
<option value="member|groupid=<?php echo $v['groupid'];?>"><?php echo $v['groupname'];?></option>
<?php 
	}
} 
?>
<?php foreach($GRADE as $k=>$v) { 
?>
<option value="member|gradeid=<?php echo $v['gradeid'];?>"><?php echo $v['name'];?></option>
<?php 
} 
?>
<option value="member|validate=0">资料未认证</option>
<option value="member|validate>0">资料已认证</option>
<option value="member|validate=1">个人已认证</option>
<option value="member|validate=2">机构已认证</option>
<option value="member|vmobile=1">手机已认证</option>
<option value="member|vemail=1">邮件已认证</option>
<option value="member|logintime<<?php echo $DT_TIME;?>-3600*24*30">30天未登录会员</option>
<option value="member|regtime<<?php echo $DT_TIME;?>-3600*24*365">注册时间超过1年</option>
<option value="member|message>10">未读站内信超过10封</option>
<option value="member|fans>10000">粉丝超过一万</option>
<option value="member|money>1000">帐户可用<?php echo $DT['money_name'];?>多余1000<?php echo $DT['money_unit'];?></option>
<option value="member m,company c|m.userid=c.userid and c.vip>6"><?php echo VIP;?>指数大于6的企业</option>
<option value="member m,company c|m.userid=c.userid and c.totime><?php echo $DT_TIME;?>"><?php echo VIP;?>服务过期的企业</option>
<option value="member m,company c|m.userid=c.userid and c.totime><?php echo $DT_TIME;?>-3600*24*30"><?php echo VIP;?>服务30天内过期的企业</option>
<option value="member m,company c|m.userid=c.userid and c.domain<>''">绑定了顶级域名的的企业</option>
</select>
<span id="dtb" class="f_red">
<script type="text/javascript">
function mk(v) {
	var pre = '<?php echo $DT_PRE;?>';
	var arr = v.split('|');
	if(arr[0]) Dd('tb').value = pre+arr[0].replace(/,/, ','+pre);
	if(arr[1]) Dd('sql').value = arr[1];
	<?php if($action == 'make') { ?>
	if(arr[0]) Dd('note').value = $('#op').find('option:selected').text();
	<?php } ?>
}
</script>
</td>
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 查询条件</td>
<td><input type="text" size="60" name="sql" id="sql" value="groupid>4" class="f_fd"/> <span id="dsql" class="f_red"></span></td>
</tr>