/*20230206*/
var mpBridge=function(){
	var e={},
	t=function(t,i){
		Object.defineProperty(e,t,{configurable:!1,writable:!1,value:i})
	},
	i=navigator.userAgent;
	t("isWx",/MicroMessenger.*miniProgram/i.test(i)),
	t("isAlipay",/Alipay.*MiniProgram/i.test(i)),
	t("isTt",/toutiaomicroapp/i.test(i)),
	t("isQq",/QQ.*miniProgram/i.test(i)),
	t("isBaidu",/swan\//.test(i)||/^webswan-/.test(window.name)),
	t("isJd",/JD.*miniProgram/i.test(i)),
	t("inMp",e.isWx||e.isAlipay||e.isTt||e.isQq||e.isBaidu||e.isJd);
	var o="此接口仅能运行在小程序环境中",
	s=function(e,t){
		console.error(t),e&&e(!1,{error:t})
	},
	a={},
	n={},
	r="onLogin";
	t("on",(function(e,t){var i=a[e];return!!i&&(t&&t(i.success,i.data),delete a[e],!0)}));
	var c=function(e){var t=n[e.on];if(t){try{t(e.success,e.data)}catch(e){console.error(e)}delete n[e.on]}},
	l=0,
	d=function(e){var t,i,o="xapp-mp-cbs=",s=location.hash;if(s&&((t=s.lastIndexOf(i="#"+o))>=0||(t=s.lastIndexOf(i="%23"+o))>=0)){for(var n=JSON.parse(decodeURIComponent(s.substr(t+i.length))),r=0,l=n.length;r<l;r++){var d=n[r];if(d&&d.data&&d.data._&&delete d.data._,e)c(d);else if(d.on){var p=d.on.substr(2,1).toLowerCase()+d.on.substr(3);a[p]={success:d.success,data:d.data}}}e&&history.back()}};
	d(0);
	var p=function(t,i){if(n[t]=i,e.isAlipay){var o=u();o&&!o.onMessage&&(o.onMessage=c)}else l||(window.addEventListener("hashchange",(function(){d(1)})),l=1)},
	u=function(t,i){var a,n;return e.isWx?a="object"==typeof wx?i?wx:wx.miniProgram:0:e.isAlipay?a="object"==typeof my?my:0:e.isTt?(a="object"==typeof tt?i?tt:tt.miniProgram:0,i&&a&&!a.getLocation&&(a.getLocation=function(e){p("onGetLocation",t),j("location_get",e,1,t)},a.openLocation=function(e){p("onOpenLocation",t),j("location_open",e,1,t)})):e.isQq?a="object"==typeof qq?i?qq:qq.miniProgram:0:e.isBaidu?a="object"==typeof swan?i?swan:swan.webView:0:e.isJd&&(a="object"==typeof jd?i?jd:jd.miniProgram:0),null==a?n=e.isWx&&wx?"js 冲突，请移除微信公众号 js SDK 引用。":o:0==a&&(n="未引用小程序平台官方 js 库，可将 mpBridge.ready 的第二个参数设为 true 自动引用。"),n&&s(t,n),a},
	f=0,
	g=0,
	m=[];
	t("ready",(function(t,i){i?function(t){if(f)g?t&&t(e):t&&m.push(t);else{var i;if(f=1,t&&m.push(t),e.isWx)i="https://res.wx.qq.com/open/js/jweixin-1.3.2.js";else if(e.isAlipay)i="https://appx/web-view.min.js";else if(e.isTt)i="https://lf1-cdn-tos.bytegoofy.com/goofy/developer/jssdk/jssdk-1.1.0.js";else if(e.isQq)i="https://qqq.gtimg.cn/miniprogram/webview_jssdk/qqjssdk-1.0.0.js";else if(e.isBaidu)i="https://b.bdstatic.com/searchbox/icms/searchbox/js/swan-2.0.30.js";else{if(!e.isJd)return void console.error(o);i="https://storage.360buyimg.com/api-test/jssdk.js"}var s=function(t){if("load"===t.type||/^(complete|loaded)$/.test((t.currentTarget||t.srcElement).readyState)){g=1;for(var i=0;i<m.length;i++)m[i](e);m=null}},a=document.createElement("script");a.type="text/javascript",a.charset="utf-8",a.async=!0,a.addEventListener?a.addEventListener("load",s,!1):a.attachEvent("onreadystatechange",s),a.src=i,(document.head||document.getElementsByTagName("head")[0]).appendChild(a)}}(t):t&&t(e)}));
	var y=function(e,t,i){if(!e)return s(i,"此小程序平台不支持本接口"),0;var o=t||{};return i&&(o.success=function(e){i(!0,e)},o.fail=function(e){var t=e.errMsg||(e.error?e.error+":"+e.errorMessage:"")||"失败";s(i,t)}),e(o),1},
	v={navigateTo:{r:0},
	redirectTo:{r:0},
	navigateBack:{r:0},
	switchTab:{r:0},
	reLaunch:{r:0},
	getLocation:{r:1},
	openLocation:{r:1}};
	for(var h in v)t(h,function(t,i){return function(o,s){e.isAlipay&&"getLocation"==t&&o&&"number"!=typeof o.type&&(o.type=1),console.log("call:",t,o);var a=u(s,i.r);a&&y(a[t],o,s)}}(h,v[h]));
	var j=function(t,i,o,s){o&&(i={u:location.href,d:JSON.stringify(i||{})});
	var a="";
	if(i)for(h in i)a+=(a?"&":"")+h+"="+encodeURIComponent(i[h]);u(s)&&e.navigateTo({url:"/pages/_ym/"+t+"?"+a})};
	return t("open",(function(e,t){e&&e.url&&j("web",e,0,t)})),
	t("postMessage",(function(e,t){console.log("call:","postMessage",e);
	var i=u(t);i&&(i.postMessage({data:e}),t&&t(!0,{}))})),t("login",(function(t,i){console.log("call:","login",t),e.isWx||e.isAlipay||e.isTt||e.isBaidu?(p(r,i),e.isAlipay?e.postMessage({on:r,data:t}):j("login",t,1,i)):s(i,"暂不支持")})),t("pay",(function(t,i){if(console.log("call:","pay",t),e.isAlipay){var o=u(i);o&&y(o.tradePay,t,i)}else e.isWx||e.isTt||e.isBaidu?(p("onPay",i),j("pay",t,1,i)):s(i,"暂不支持")})),e
}();