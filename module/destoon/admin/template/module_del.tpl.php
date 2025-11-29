<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
show_menu($menus);
?>
<style>.tb li {padding:10px 0;}.tb li img {margin-right:6px;}</style>
<div class="tt">卸载说明</div>
<table cellspacing="0" class="tb">
<tr>
<td style="line-height:32px;padding:16px 24px;">
通常情况下，如果不使用某个模块，可以直接在<a href="?file=module" class="t">模块管理</a>里禁用或删除，不建议卸载。<br/>
如果确定不再使用某个模型，可以手动删除以下模块、目录和文件以彻底卸载该模型。<br/>
</td>
</tr>
</table>
<?php if($mods) { ?>
<table cellspacing="0" class="tb">
<tr>
<th width="105">模块</th>
<th width="200">目录</th>
<th width="400">文件</th>
<th></th>
</tr>
<tr>
<td valign="top" align="center">
<ul>
<?php foreach($mods as $k=>$v) {?>
<li><a href="<?php echo $v['linkurl'];?>" target="_blank" class="t"><?php echo $v['name'];?></a></li>
<?php
}
?>
</ul>
</td>
<td valign="top">
<ul>
<?php foreach($dirs as $k=>$v) {?>
<li><img src="file/ext/folder.gif" align="absmiddle"/><?php echo $v;?></li>
<?php
}
?>
</ul>
</td>
<td valign="top">
<ul>
<?php foreach($files as $k=>$v) {?>
<li><img src="file/ext/<?php echo file_ext($v);?>.gif" align="absmiddle"/><?php echo $v;?></li>
<?php
}
?>
</ul>
</td>
<td></td>
</tr>
</table>
<div class="sbt"><input type="button" value="返 回" class="btn-g" onclick="Go('?file=<?php echo $file;?>&action=sys');"/></div>
<?php } ?>
<script type="text/javascript">Menuon(2);</script>
<?php include tpl('footer');?>