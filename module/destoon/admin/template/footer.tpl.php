<?php
defined('DT_ADMIN') or exit('Access Denied');
if(DT_DEBUG) {
	echo '<br/><center class="f_gray">';
	debug();
	echo '</center><br/>';
}
?>
<div class="back2top"><a href="javascript:void(0);" title="返回顶部">&nbsp;</a></div>
<?php if(strpos($DT_MBS, 'IE') === false) load('notification.js'); ?>
<script type="text/javascript">
$(function(){
	<?php if($_message && strpos($DT_MBS, 'IE') === false) { ?>
	Dnotification('new_message', '<?php echo $MODULE[2]['linkurl'];?>message.php', '<?php echo useravatar($_username, 'large');?>', '站内信(<?php echo $_message;?>)', '收到新的站内信件，点击查看');
	<?php } ?>
	<?php if($_chat && strpos($DT_MBS, 'IE') === false) { ?>
	Dnotification('new_chat', '<?php echo $MODULE[2]['linkurl'];?>im.php', '<?php echo useravatar($_username, 'large');?>', '新交谈(<?php echo $_chat;?>)', '收到新的对话请求，点击交谈');
	<?php } ?>
	setTimeout(function() {
		if($('.sbt').length && $(document).height() > $(window).height()) {$('.sbt').attr('class', 'sbt sbt-fix');$('body').append('<br/><br/><br/><br/>');}
		if($('.btns').length && $(document).height() > $(window).height()) {$('.btns').last().attr('class', 'btns btns-fix');$('body').append('<br/><br/><br/><br/>');}
	}, 300);
	$('#destoon_menu').click(function(e) {
		if(e.target.nodeName == 'TD') $('html, body').animate({scrollTop:0}, 200);
	});
});
</script>
</body>
</html>