<?php
defined('DT_ADMIN') or exit('Access Denied');
include tpl('header');
?>
<div style="padding:16px;line-height:2.0;">
通过静态文件分离部署功能，可以将网站的静态文件部署到独立的服务器，从而减轻主站的压力和提高主站访问速度。<br/>
例如静态文件所在服务器绑定的域名为static.destoon.com，请在部署地址处填写https://static.destoon.com/，然后上传网站的static目录里的文件至static.destoon.com所在的站点目录。<br/>
</div>
<?php include tpl('footer');?>