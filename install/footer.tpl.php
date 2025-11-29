<?php
defined('IN_DESTOON') or exit('Access Denied');
?>
</td>
</tr>
</table>
</div>
</td>
</tr>
</table>
<br/><br/><br/><br/><br/><br/>
</td>
</tr>
</table>
<script type="text/javascript">
$('percent').innerHTML = '<label disabled><?php echo $percent;?></label>';
$('progress').parentNode.title = '安装进度:<?php echo $percent;?>';
$('progress').style.width = '<?php echo $percent;?>';
</script>
</body>
</html>