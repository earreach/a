<?php
/*
	[DESTOON B2B System] Copyright (c) 2008-2018 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
class dftp {
    var $connected = 0;
    var $ak;
    var $sk;
    var $bk;

    function __construct($ftphost, $ftpuser, $ftppass, $ftpport = 21, $root = '/', $pasv = 0, $ssl = 0) {
        $this->connected = 1;
        $this->endpoint = $ftphost;
        $this->ak = $ftpuser;
        $this->sk = $ftppass;
        $this->bk = $root;
    }

    function dftp($ftphost, $ftpuser, $ftppass, $ftpport = 21, $root = '/', $pasv = 0, $ssl = 0) {
        $this->__construct($ftphost, $ftpuser, $ftppass, $ftpport, $root, $pasv, $ssl);
    }

    function dftp_delete($file) {
        global $DT;
        require_once DT_ROOT.'/api/ftpos/alioss/autoload.php';
        $accessKeyId        = $this->ak;
        $accessKeySecret    = $this->sk;
        $endpoint           = $this->endpoint;
        $bucket             = $DT['ftp_path'];
        $ossClient = new \OSS\OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $result = $ossClient->deleteObject($bucket, $file);
    }

    function dftp_put($local, $remote = '') {
        global $DT_TIME, $DT;
        $remote or $remote = $local;
        $local = DT_ROOT.'/'.$local;
        $key = $remote;
        require_once DT_ROOT.'/api/ftpos/alioss/autoload.php';
        $accessKeyId        = $this->ak;
        $accessKeySecret    = $this->sk;
        $endpoint           = $this->endpoint;
        $bucket             = $DT['ftp_path'];
        $ossClient = new \OSS\OssClient($accessKeyId, $accessKeySecret, $endpoint);
        try{
            $result = $ossClient->uploadFile($bucket, $remote, $local);
            //print_r($result);die;
            //echo $result;die;
            return true;
        } catch(OssException $e) {
            return false;
            //printf($e->getMessage() . "\n");
            return;
        }
    }

    function dftp_chdir() {
        if(!function_exists('hash_hmac')) return false;
        if(!function_exists('curl_init')) return false;
        return true;
    }

    function dftp_encode($str) {
        return str_replace(array('+', '/'), array('-', '_'), base64_encode($str));
    }
}
?>