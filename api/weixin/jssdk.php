<?php
defined('IN_DESTOON') or exit('Access Denied');
class JSSDK {
	private $appId;
	private $appSecret;

	public function __construct($appId, $appSecret) {
		$this->appId = $appId;
		$this->appSecret = $appSecret;
	}

	public function JSSDK() {
		$this->__construct();
	}

	public function getSignPackage() {
		global $linkurl, $DT_URL;
		$jsapiTicket = $this->getJsApiTicket();
		if(!$jsapiTicket) return array();
		$url = (isset($linkurl) && is_uri($linkurl)) ? $linkurl : $DT_URL;
		$timestamp = DT_TIME;
		$nonceStr = random(16);
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		$signature = sha1($string);
		$signPackage = array(
			"appId"     => $this->appId,
			"nonceStr"  => $nonceStr,
			"timestamp" => $timestamp,
			"url"       => $url,
			"signature" => $signature,
			"rawString" => $string
		);
		return $signPackage; 
	}

	private function getJsApiTicket() {
		global $dc;
		$ticket = $dc->get('weixin_ticket');
		if($ticket) return $ticket;
		$url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token='.$this->getAccessToken();
		$arr = json_decode(dcurl($url), true);
		$ticket = isset($arr['ticket']) ? $arr['ticket'] : '';
		$dc->set('weixin_ticket', $ticket, 7000);
		return $ticket;
	}

	private function getAccessToken() {
		global $dc;
		$access_token = $dc->get('weixin_token');
		if($access_token) return $access_token;
		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appId.'&secret='.$this->appSecret;
		$arr = json_decode(dcurl($url), true);
		$access_token = isset($arr['access_token']) ? $arr['access_token'] : '';
		$dc->set('weixin_token', $access_token, 7000);
		return $access_token;
	}
}