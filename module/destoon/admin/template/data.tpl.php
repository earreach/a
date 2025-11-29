<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form method="post" action="?" onsubmit="return check();">
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="download"/>
<table cellspacing="0" class="tb">
<tr>
<td class="tl"><span class="f_red">*</span> 数据来源</td>
<td>
<select name="table" onchange="get_fields(this.value);" size="2" style="height:500px;width:300px;" id="tb">
<option value="">选择表</option>
<?php echo $table_select;?>
</select>&nbsp;&nbsp;
<span id="fields"><select name="fields[]" id="fd" multiple="multiple" size="2" style="height:500px;width:300px;"><option value="">选择字段(按Ctrl多选)</option></select></span> <span id="dfd" class="f_red"></span>
</td>
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 导出条件</td>
<td><input name="condition" type="text" size="50" id="condition" onblur="get_pages();"/> &nbsp; <span class="f_gray" onclick="$('#condition').val('AND status=3');">AND开头的MySQL条件语句，例如AND status=3</span></td> 
</tr>
<tr class="dsn" id="time-fd">
<td class="tl"><span class="f_hid">*</span> 时间字段</td>
<td id="time-td"></td> 
</tr>
<tr class="dsn" id="time-ft">
<td class="tl"><span class="f_hid">*</span> 时间范围</td>
<td><?php echo dcalendar('fromdate', '', '-', 1);?> 至 <?php echo dcalendar('todate', '', '-', 1);?></td> 
</tr>
<tr>
<td class="tl"><span class="f_hid">*</span> 排序方式</td>
<td><input name="order" type="text" size="50" id="order"/> &nbsp; <span class="f_gray" onclick="$('#order').val('itemid DESC');">数据的排序方式，例如itemid DESC</span></td> 
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 导出格式</td>
<td>
<select name="ext">
<option value="csv">CSV</option>
<option value="xml">XML</option>
<option value="json">JSON</option>
</select>
</td> 
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 每轮查询</td>
<td><input name="psize" type="text" size="10" value="5000" id="psize" onblur="get_pages();"/> &nbsp; <span class="f_gray">条数据</span></td> 
</tr>
<tr>
<td class="tl"><span class="f_red">*</span> 页码</td>
<td><input name="page" type="text" size="10" value="1" id="page"/> &nbsp; <span class="f_gray">共<span id="pages">0</span>页/<span id="total">0</span>条</span></td> 
</tr>
<tr>
<td class="tl">&nbsp;</td>
<td><input type="submit" value="导 出" class="btn-g"/></td> 
</tr>
</table>
</form>
<script type="text/javascript">
var max_page = 0;
function get_fields(tb) {
	if(!tb) return false;
	$('#condition').val('');
	$('#order').val('');
	$.get('?file=<?php echo $file;?>&action=fields&table='+tb, function(data) {
		if(data.select) {
			$('#fields').html(data.select);
			$('#order').val(data.order);
			$('#time-td').html(data.time);
			if(data.time) {
				$('#time-fd,#time-ft').show();
			} else {
				$('#time-fd,#time-ft').hide();
			}
		}
		get_pages();
	}, 'json');
}
function get_pages() {
	$.post('?', 'file=<?php echo $file;?>&action=pages&table='+$('#tb').val()+'&psize='+$('#psize').val()+'&condition='+encodeURIComponent($('#condition').val()), function(data) {
		if(data && data.ok) {
			$('#pages').html(data.page);
			$('#total').html(data.total);
			max_page = data.page;
		} else {
			$('#pages').html('0');
			$('#total').html('0');
			max_page = 0;
			alert('导出条件填写错误');
		}
	},'json');
}
function check() {
	if(Dd('tb').value == '') {
		alert('请选择数据表');
		Dd('tb').focus();
		return false;
	}
	if(Dd('page').value > 1 && Dd('page').value > max_page) {
		alert('页码数值超出最大值');
		Dd('page').focus();
		return false;
	}
}
<?php if($table) echo 'get_fields("'.$table.'")';?>
</script>
<script type="text/javascript">Menuon(8);</script>
<?php include tpl('footer');?>