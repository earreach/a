<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<div class="sbox">
<form action="?" id="search">
<input type="hidden" name="moduleid" value="<?php echo $moduleid;?>"/>
<input type="hidden" name="file" value="<?php echo $file;?>"/>
<?php echo $fields_select;?>&nbsp;
<input type="text" size="30" name="kw" value="<?php echo $kw;?>" placeholder="请输入关键词" title="请输入关键词"/>&nbsp;
<?php echo $module_select;?>&nbsp;
<span data-hide-1200="1"><?php echo $order_select;?>&nbsp;</span>
<input type="text" name="psize" value="<?php echo $pagesize;?>" size="2" class="t_c" placeholder="条/页" title="条/页"/>&nbsp;
<input type="submit" value="搜 索" class="btn"/>&nbsp;
<input type="button" value="重 置" class="btn" onclick="Go('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>');"/>
</form>
</div>
<form method="post">
<table cellspacing="0" class="tb ls">
<tr>
<th width="20"><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></th>
<th>标题</th>
<th data-hide-1200="1" data-hide-1400="1" width="380">采集网址</th>
<th width="130">目标</th>
<th width="130">分类</th>
<th width="100"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 3 ? 4 : 3;?>');">网址量 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 4 ? 'asc' : ($order == 3 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="100"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 5 ? 6 : 5;?>');">数据量 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 6 ? 'asc' : ($order == 5 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="130"><a href="javascript:;" onclick="Dq('order','<?php echo $order == 1 ? 2 : 1;?>');">上次采集 <img src="<?php echo DT_STATIC;?>image/ico-<?php echo $order == 2 ? 'asc' : ($order == 1 ? 'dsc' : 'ord');?>.png" width="11" height="11"/></a></th>
<th width="40">设置</th>
<th width="40">修改</th>
</tr>
<?php foreach($lists as $k=>$v) {?>
<tr align="center">
<td><input type="checkbox" name="itemid[]" value="<?php echo $v['itemid'];?>"/></td>
<td align="left">&nbsp;<a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=setting&itemid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 规则设置');"><?php echo $v['title'];?></td>
<td data-hide-1200="1" data-hide-1400="1" title="<?php echo $v['linkurl'];?>"><input type="text" size="50" value="<?php echo $v['linkurl'];?>"/> <a href="<?php echo gourl($v['linkurl']);?>" target="_blank"><img src="<?php echo DT_STATIC;?>admin/link.png" width="16" height="16" title="点击打开网址" alt="" align="absmiddle"/></a></td>
<td><a href="javascript:;" onclick="Dq('fields',3,0);Dq('kw','='+this.innerHTML);"><?php echo $v['name'];?></a></td>
<td><a href="javascript:;" onclick="Dq('fields',4,0);Dq('kw','='+this.innerHTML);"><?php echo $v['catname'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=url&itemid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 网址管理');"><?php echo $v['urls'];?></a></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=data&itemid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 数据管理');"><?php echo $v['datas'];?></a></td>
<td title="编辑:<?php echo $v['editor'];?>&#10;修改时间:<?php echo $v['editdate'];?>&#10;添加时间:<?php echo $v['adddate'];?>"><?php echo $v['lastdate'];?></td>
<td><a href="javascript:;" onclick="Dwidget('?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=setting&itemid=<?php echo $v['itemid'];?>', '[<?php echo $v['alt'];?>] 规则设置');"><img src="<?php echo DT_STATIC;?>admin/set.png" width="16" height="16" title="规则设置" alt=""/></a></td>
<td><a href="?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=edit&itemid=<?php echo $v['itemid'];?>"><img src="<?php echo DT_STATIC;?>admin/edit.png" width="16" height="16" title="修改" alt=""/></a></td>
</tr>
<?php }?>
</table>
<div class="btns">
<label><input type="checkbox" onclick="checkall(this.form);" title="全选/反选"/></label>
<input type="submit" value="开始采集" class="btn-g" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=start&all=2';" title="抓取网址并采集内容"/>&nbsp;
<input type="submit" value="删除规则" class="btn-r" onclick="if(confirm('确定要删除选中规则吗？已采集的网址和数据会同时删除 此操作将不可撤销')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=delete';}else{return false;}"/>&nbsp;
<input type="submit" value="抓取网址" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=start';" title="仅抓取网址"/>&nbsp;
<input type="submit" value="采集内容" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=start&job=show';" title="仅采集内容"/>&nbsp;
<input type="submit" value="发布数据" class="btn" onclick="this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=start&job=post';" title="仅发布数据"/>&nbsp;
<input type="submit" value="一键启动" class="btn" onclick="if(confirm('确定要一键启动吗？系统将自动执行数据采集并发布')){this.form.action='?moduleid=<?php echo $moduleid;?>&file=<?php echo $file;?>&action=start&all=3';}else{return false;}" title="抓取网址 采集内容 发布数据"/>&nbsp;
</div>
</form>
<?php echo $pages ? '<div class="pages">'.$pages.'</div>' : '';?>
<script type="text/javascript">Menuon(1);</script>
<?php include tpl('footer');?>