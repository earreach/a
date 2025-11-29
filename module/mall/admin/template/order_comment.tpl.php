<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<input type="hidden" name="action" value="<?php echo $action;?>"/>
<table cellspacing="0" class="tb">
<tr>
<td>&nbsp;
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词"/>&nbsp;
<?php echo $order_select;?>&nbsp;
<input type="checkbox" name="thumb" value="1"<?php echo $thumb ? ' checked' : '';?>/> 晒图&nbsp;
<input type="checkbox" name="video" value="1"<?php echo $video ? ' checked' : '';?>/> 视频&nbsp;
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=<?php echo $action;?>');"/>
</td>
</tr>
<tr>
<td>&nbsp;
<input type="text" name="itemid" value="<?php echo $itemid;?>" size="15" placeholder="订单号"/>&nbsp;
<input type="text" name="mallid" value="<?php echo $mallid;?>" size="15" placeholder="商品ID"/>&nbsp;
<input type="text" name="seller" value="<?php echo $seller;?>" size="15" placeholder="卖家"/>&nbsp;
<input type="text" name="buyer" value="<?php echo $buyer;?>" size="15" placeholder="买家"/>&nbsp;
<select name="seller_star">
<option value="0"<?php if($seller_star == 0) echo ' selected';?>>商品</option>
<option value="6"<?php if($seller_star == 6) echo ' selected';?>>已评</option>
<option value="5"<?php if($seller_star == 5) echo ' selected';?>>5星</option>
<option value="4"<?php if($seller_star == 4) echo ' selected';?>>4星</option>
<option value="3"<?php if($seller_star == 3) echo ' selected';?>>3星</option>
<option value="2"<?php if($seller_star == 2) echo ' selected';?>>2星</option>
<option value="1"<?php if($seller_star == 1) echo ' selected';?>>1星</option>
</select>&nbsp;
<select name="seller_star_express">
<option value="0"<?php if($seller_star_express == 0) echo ' selected';?>>物流</option>
<option value="6"<?php if($seller_star_express == 6) echo ' selected';?>>已评</option>
<option value="5"<?php if($seller_star_express == 5) echo ' selected';?>>5星</option>
<option value="4"<?php if($seller_star_express == 4) echo ' selected';?>>4星</option>
<option value="3"<?php if($seller_star_express == 3) echo ' selected';?>>3星</option>
<option value="2"<?php if($seller_star_express == 2) echo ' selected';?>>2星</option>
<option value="1"<?php if($seller_star_express == 1) echo ' selected';?>>1星</option>
</select>&nbsp;
<select name="seller_star_service">
<option value="0"<?php if($seller_star_service == 0) echo ' selected';?>>商家</option>
<option value="6"<?php if($seller_star_service == 6) echo ' selected';?>>已评</option>
<option value="5"<?php if($seller_star_service == 5) echo ' selected';?>>5星</option>
<option value="4"<?php if($seller_star_service == 4) echo ' selected';?>>4星</option>
<option value="3"<?php if($seller_star_service == 3) echo ' selected';?>>3星</option>
<option value="2"<?php if($seller_star_service == 2) echo ' selected';?>>2星</option>
<option value="1"<?php if($seller_star_service == 1) echo ' selected';?>>1星</option>
</select>&nbsp;
<select name="buyer_star">
<option value="0" <?php if($buyer_star == 0) echo ' selected';?>>买家</option>
<option value="6" <?php if($buyer_star == 6) echo ' selected';?>>已评</option>
<option value="5" <?php if($buyer_star == 5) echo ' selected';?>>5星</option>
<option value="4" <?php if($buyer_star == 4) echo ' selected';?>>4星</option>
<option value="3" <?php if($buyer_star == 3) echo ' selected';?>>3星</option>
<option value="2" <?php if($buyer_star == 2) echo ' selected';?>>2星</option>
<option value="1" <?php if($buyer_star == 1) echo ' selected';?>>1星</option>
</select>&nbsp;
</td>
</tr>
<tr>
<td>&nbsp;
<select name="datetype">
<option value="buyer_ctime"<?php if($datetype == 'buyer_ctime') echo ' selected';?>>卖家评价时间</option>
<option value="buyer_rtime"<?php if($datetype == 'buyer_rtime') echo ' selected';?>>卖家回复时间</option>
<option value="seller_ctime"<?php if($datetype == 'seller_ctime') echo ' selected';?>>买家评价时间</option>
<option value="seller_rtime"<?php if($datetype == 'seller_rtime') echo ' selected';?>>买家回复时间</option>
</select>&nbsp;
<?php echo dcalendar('fromdate', $fromdate, '-' ,1);?> 至 <?php echo dcalendar('todate', $todate, '-' ,1);?>&nbsp;
</td>
</tr>
</table>
</form>
<style type="text/css">
.comment-r {overflow:hidden;text-align:left;max-width:400px;}
.comment-r ul {margin-top:12px;overflow:hidden;}
.comment-r li {width:84px;height:84px;float:left;}
.comment-r li img {width:64px;height:64px;border:#EEEEEE 1px solid;padding:2px;cursor:pointer;}
.comment-r p {margin:0;overflow:hidden;clear:both;display:none;}
.comment-r p img {max-width:400px;cursor:url('<?php echo DT_SKIN;?>next.cur'),default;}
.comment-info {padding:0 0 10px 0;}
.comment-info span {float:right;color:#666666;}
.comment-content {font-size:14px;line-height:24px;}
.comment-reply {font-size:14px;line-height:24px;color:#FF6600;margin-top:10px;border-top:#EAEAEA 1px dotted;padding-top:10px;clear:both;}
.comment-reply span {float:right;color:#666666;font-size:12px;}
.comment-empty {padding:128px;text-align:center;color:#666666;font-size:14px;}
</style>
<?php load('mall.js');load('player.js');?>
<table cellspacing="0" class="tb">
<tr>
<th>单号</th>
<th>商品ID</th>
<th>买家</th>
<th>买家评价</th>
<th>卖家</th>
<th>卖家评价</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td title="订单详情"><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=show&mallid=<?php echo $mallid;?>&itemid=<?php echo $v['itemid'];?>', '订单详情');"><?php echo $v['itemid'];?></a></td>
<td><a href="<?php echo gourl('?mid='.$moduleid.'&itemid='.$v['mallid']);?>" target="_blank"><?php echo $v['mallid'];?></a></td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['buyer'];?>');"><?php echo $v['buyer'];?></a></td>
<td class="comment-r" valign="top">
<div class="comment-info">
<span onclick="Dq('datetype','seller_ctime',0);Dq('date',this.title);" class="c_p" title="<?php echo $v['seller_ctime'] ? timetodate($v['seller_ctime'], 5) : 'N/A';?>"><?php echo timetoread($v['seller_ctime'], 5);?></span>
<img src="<?php echo DT_STATIC;?>image/star<?php echo $v['seller_star'];?>.gif" align="absmiddle" title="商品：<?php echo $v['seller_star'];?>星 <?php echo $STARS[$v['seller_star']];?>&#10;物流：<?php echo $v['seller_star_express'];?>星 <?php echo $STARS[$v['seller_star_express']];?>&#10;商家：<?php echo $v['seller_star_service'];?>星 <?php echo $STARS[$v['seller_star_service']];?>"/>
</div>
<div class="comment-content"><?php echo $v['seller_comment'] ? nl2br($v['seller_comment']) : '买家没有发表评价内容';?></div>
<?php if($v['thumbs'] || $v['video']) { ?>
<ul id="thumbs-<?php echo $v['itemid'];?>">
<?php if($v['video']) { ?><li><img src="<?php echo DT_STATIC;?>image/play.gif" onclick="comment_thumb_show(<?php echo $v['itemid'];?>, this);" data-video="<?php echo $v['video'];?>"/></li><?php } ?>
<?php if(is_array($v['thumbs'])) { foreach($v['thumbs'] as $t) { ?>
<?php if($t) { ?><li><img src="<?php echo $t;?>" onclick="comment_thumb_show(<?php echo $v['itemid'];?>, this);"/></li><?php } ?>
<?php } } ?>
</ul>
<p id="thumbshow-<?php echo $v['itemid'];?>" onclick="comment_thumb_next(<?php echo $v['itemid'];?>);"></p>
<?php } ?>
<?php if($v['buyer_reply']) {?>
<div class="comment-reply">
<span onclick="Dq('datetype','buyer_rtime',0);Dq('date',this.title);" class="c_p" title="<?php echo $v['buyer_ctime'] ? timetodate($v['buyer_ctime'], 5) : 'N/A';?>"><?php echo timetoread($v['buyer_rtime'], 5);?></span>
卖家回复：<br/>
<?php echo nl2br($v['buyer_reply']);?>
</div>
<?php } ?>
</td>
<td><a href="javascript:;" onclick="_user('<?php echo $v['seller'];?>');"><?php echo $v['seller'];?></a></td>
<td class="comment-r" valign="top">
<div class="comment-info">
<span onclick="Dq('datetype','buyer_ctime',0);Dq('date',this.innerHTML);" class="c_p"><?php echo $v['buyer_ctime'] ? timetodate($v['buyer_ctime'], 5) : 'N/A';?></span>
<img src="<?php echo DT_STATIC;?>image/star<?php echo $v['buyer_star'];?>.gif" align="absmiddle" title="买家：<?php echo $v['buyer_star'];?>星 <?php echo $STARS[$v['buyer_star']];?>"/>
</div>
<div class="comment-content"><?php echo $v['buyer_comment'] ? nl2br($v['buyer_comment']) : '卖家没有发表评价内容';?></div>
<?php if($v['seller_reply']) {?>
<div class="comment-reply">
<span onclick="Dq('datetype','seller_rtime',0);Dq('date',this.innerHTML);" class="c_p"><?php echo $v['seller_rtime'] ? timetodate($v['seller_rtime'], 5) : 'N/A';?></span>
买家回复：<br/>
<?php echo nl2br($v['seller_reply']);?>
</div>
<?php } ?>
</td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=comment_edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>