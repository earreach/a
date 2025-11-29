if(UA.indexOf('iphone')!=-1 || UA.indexOf('ipod')!=-1 || UA.indexOf('ipad')!=-1) {
	document.write('<style type="text/css">');
	if(parseInt(UA.match(/os (\d+)_/)[1]) > 7) {/*IOS8+ 细线*/
		document.write('.bd-b,'+(DTMobc ? '' : '.head-bar,')+'.list-set,.list-img,.list-msg li {border-bottom:#D3D3D3 0.5px solid;}.bd-t,.foot-bar,.list-set div,.list-set {border-top:#D3D3D3 0.5px solid;}.bd-r {border-right:#D3D3D3 0.5px solid;}.bd-l {border-left:#D3D3D3 0.5px solid;}');
	}
	if(Dbrowser == 'web') {
		document.write('.head-bar{border-top:'+(DTMobc ? DTMobc : '#F7F7F7')+' 20px solid;}.head-bar-fix{height:68px;background:#F7F7F7;}');
	} else if(Dbrowser == 'app') {
		var apptop = parseInt(get_cookie('apptop'));
		var appbottom = parseInt(get_cookie('appbottom'));
		//alert(appbottom);
		if(apptop < 20 || apptop > 50) apptop = 20;
		document.write('.head-bar{border-top:'+(DTMobc ? DTMobc : '#F7F7F7')+' '+apptop+'px solid;}.head-bar-fix{padding-top:'+parseInt(apptop/2)+'px;background:#F7F7F7;}');
		document.write('.member-info{border-top:'+(DTMobc ? DTMobc : '#3A82F6')+' '+apptop+'px solid;}');
		document.write('.ui-pop{top:'+(apptop+36)+'px;}');
		if(appbottom) document.write('.foot-bar{border-bottom:#FFFFFF '+appbottom+'px solid;}.foot-bar-fix{border-bottom:#FFFFFF '+appbottom+'px solid;}');
	} else if(UA.indexOf('micromessenger/')!=-1) {/*微信*/
		document.write((DTMobc ? '' : '.head-bar{background:#'+(UA.indexOf('miniprogram')!=-1 ? 'FFFFFF' : 'EDEDED')+';}')+'.head-bar-left {padding:0 0 0 16px;}.head-bar-back {padding:0 0 0 10px;}');
		if(UA.indexOf('miniprogram')==-1) document.write('.head-bar-title input[type="search"] {background-color:#F9F9F9;}');
	} else if(UA.indexOf('tim/')!=-1) {/*TIM*/
		document.write((DTMobc ? '' : '.head-bar{background:#F3F5F8;}')+'.head-bar-left {padding:0 0 0 12px;}.head-bar-back {padding:0 0 0 6px;}.head-bar-right {padding:0 6px 0 0;}');
	} else if(UA.indexOf('qq/')!=-1) {/*QQ*/
		document.write((DTMobc ? '' : '.head-bar{background:#FFFFFF;}')+'.head-bar-left {padding:0 0 0 12px;}.head-bar-back {padding:0 0 0 6px;}.head-bar-right {padding:0 10px 0 0;}');
	} else if(UA.indexOf('alipay')!=-1) {/*支付宝*/
		document.write((DTMobc ? '' : '.head-bar{background:#FFFFFF;}')+'.head-bar-left {padding:0 0 0 12px;}.head-bar-back {padding:0 0 0 6px;}.head-bar-right {padding:0 10px 0 0;}');
	} else if(UA.indexOf('dingtalk')!=-1) {/*钉钉*/
		document.write(DTMobc ? '' : '.head-bar{background:#FFFFFF;}');
	} else if(UA.indexOf('weibo')!=-1) {/*微博*/
		document.write((DTMobc ? '' : '.head-bar{background:#FCFCFC;}')+'.head-bar-left {padding:0 0 0 12px;}.head-bar-back {padding:0 0 0 4px;}.head-bar-right {padding:0 6px 0 0;}');
	}
	var safeb = parseInt(get_local('safeb'));/*刘海屏*/
	if(safeb > 0) {
		document.write('.foot-bar{height:68px;}.foot-bar-fix{height:68px;}');
	} else {		
		safeb = parseInt(getComputedStyle(document.documentElement).getPropertyValue("--sab").replace('px', ''));
		if(safeb > 0) {
			set_local('safeb', safeb);
			document.write('.foot-bar{height:68px;}.foot-bar-fix{height:68px;}');
		}
	}
	if(DTMobc) document.write('.head-bar{border-bottom:none;}');
	document.write('</style>');
	if(Dbrowser != 'screen' && navigator.standalone) {/*IOS 主屏打开*/
		document.write('<script type="text/javascript" src="'+AJPath+'?action=screen"></sc'+'ript>');
	}
} else if(UA.indexOf('android')!=-1) {
	document.write('<style type="text/css">');
	if(Dbrowser == 'web') {
		//
	} else if(Dbrowser == 'app') {
		var apptop = parseInt(get_cookie('apptop'));
		var appbottom = parseInt(get_cookie('appbottom'));
		//alert(appbottom);
		if(apptop < 20 || apptop > 50) apptop = 20;
		document.write('.head-bar{border-top:'+(DTMobc ? DTMobc : '#F7F7F7')+' '+apptop+'px solid;}.head-bar-fix{padding-top:'+parseInt(apptop/2)+'px;background:#F7F7F7;}');
		document.write('.member-info{border-top:'+(DTMobc ? DTMobc : '#3A82F6')+' '+apptop+'px solid;}');
		document.write('.ui-pop{top:'+(apptop+36)+'px;}');
		if(appbottom) document.write('.foot-bar{border-bottom:#FFFFFF '+appbottom+'px solid;}.foot-bar-fix{border-bottom:#FFFFFF '+appbottom+'px solid;}');
	} else if(UA.indexOf('micromessenger/')!=-1) {/*微信*/
		document.write((DTMobc ? '' : '.head-bar {background:#'+(UA.indexOf('miniprogram')!=-1 ? 'FFFFFF' : 'EDEDED')+';}')+'.head-bar-left {padding:0 0 0 10px;}.head-bar-back {padding:0 0 0 2px;}.head-bar-right {padding:0 10px 0 0;}');
		if(UA.indexOf('miniprogram')==-1) document.write('.head-bar-title input[type="search"] {background-color:#F9F9F9;}');
	} else if(UA.indexOf('tim/')!=-1) {/*TIM*/
		document.write((DTMobc ? '' : '.head-bar{background:#FFFFFF;}')+'.head-bar-left {padding:0 0 0 10px;}.head-bar-back {padding:0 0 0 8px;}.head-bar-right {padding:0 14px 0 0;}');
	} else if(UA.indexOf('qq/')!=-1) {/*QQ*/
		document.write('.head-bar-left {padding:0 0 0 6px;}.head-bar-back {padding:0 0 0 0;.head-bar-right {padding:0 0 0 0;}.head-bar-right img {padding:12px 0;}');
	} else if(UA.indexOf('alipay')!=-1) {/*支付宝*/
		document.write(DTMobc ? '' : '.head-bar{background:#FFFFFF;}');
	} else if(UA.indexOf('dingtalk')!=-1) {/*钉钉*/
		document.write(DTMobc ? '' : '.head-bar{background:#FFFFFF;}');
	} else if(UA.indexOf('weibo')!=-1) {/*微博*/
		document.write((DTMobc ? '' : '.head-bar{background:#FCFCFC;}')+'.head-bar-back {padding:0 0 0 12px;}.head-bar-right {padding:0 12px 0 0;}');
	}
	if(DTMobc) document.write('.head-bar{border-bottom:none;}');
	document.write('</style>');
}