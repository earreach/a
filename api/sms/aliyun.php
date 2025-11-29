<?php
#https://www.aliyun.com/product/sms
#https://help.aliyun.com/zh/sms/developer-reference/api-dysmsapi-2017-05-25-sendsms
#https://help.aliyun.com/zh/sdk/product-overview/v3-request-structure-and-signature
require '../../common.inc.php';
//配置账号信息 PHP>=7.4.33
$DT['sms_api'] == 'aliyun' or exit('ko.api');
$DT['sms_code'] or exit('ko.code');
$DT['sms_template'] or exit('ko.template');
$DT['sms_appid'] or exit('ko.appid');
$DT['sms_appsecret'] or exit('ko.appsecret');
$auth = isset($auth) ? decrypt($auth, DT_KEY.'SMS') : '';
if(strpos($auth, '|') === false) exit('ko.auth');
list($mobile, $message) = explode('|', $auth);
is_mobile($mobile) or exit('ko.mobile');
strpos($message, $DT['sms_code']) !== false or exit('ko.code');
$code = preg_match("/[0-9]{4,6}/", $message, $matches) ? $matches[0] : '';
$code or exit('ko.code');
define('Ali_AccessKeyId', $DT['sms_appid']);//Access Key ID
define('Ali_AccessKeySecret', $DT['sms_appsecret']);//Access Key Secret
define('Ali_TemplateCode', $DT['sms_template']);//短信模板编号
$queryParam = array(
	'PhoneNumbers' => $mobile,
	'SignName' => cutstr($message, '【', '】'),
	'TemplateCode' => Ali_TemplateCode,
	'TemplateParam' => '{"code":"'.$code.'"}',
);
class SignatureAli
{
    // 加密算法
    private $ALGORITHM;
    // Access Key ID
    private $AccessKeyId;
    // Access Key Secret
    private $AccessKeySecret;

    public function __construct()
    {
        //date_default_timezone_set('UTC'); // 设置时区为GMT
        $this->AccessKeyId = Ali_AccessKeyId; // getenv()表示从环境变量中获取RAM用户Access Key ID
        $this->AccessKeySecret = Ali_AccessKeySecret; // getenv()表示从环境变量中获取RAM用户Access Key Secret
        $this->ALGORITHM = 'ACS3-HMAC-SHA256'; // 设置加密算法
    }

    /**
     * 签名示例，您需要根据实际情况替换main方法中的示例参数。
     * ROA接口和RPC接口只有canonicalUri取值逻辑是完全不同，其余内容都是相似的。
     *
     * 通过API元数据获取请求方法（methods）、请求参数名称（name）、请求参数类型（type）、请求参数位置（in），并将参数封装到SignatureRequest中。
     * 1. 请求参数在元数据中显示"in":"query"，通过queryParam传参。
     * 2. 请求参数在元数据中显示"in": "body"，通过body传参。
     * 3. 请求参数在元数据中显示"in": "formData"，通过body传参。
     */
    public function main($queryParam)
    {
        // RPC接口请求示例一：请求参数"in":"query"
        $request = $this->createRequest('POST', '/', 'dysmsapi.aliyuncs.com', 'SendSms', '2017-05-25');
        // DescribeInstanceStatus请求参数如下：
        $request['queryParam'] = $queryParam;
        $this->getAuthorization($request);
        return $this->callApi($request);
    }
    private function createRequest($httpMethod, $canonicalUri, $host, $xAcsAction, $xAcsVersion)
    {
        $headers = [
            'host' => $host,
            'x-acs-action' => $xAcsAction,
            'x-acs-version' => $xAcsVersion,
            'x-acs-date' => gmdate('Y-m-d\TH:i:s\Z'),
            'x-acs-signature-nonce' => bin2hex(random_bytes(16)),
        ];
        return [
            'httpMethod' => $httpMethod,
            'canonicalUri' => $canonicalUri,
            'host' => $host,
            'headers' => $headers,
            'queryParam' => [],
            'body' => null,
        ];
    }

    private function getAuthorization(&$request)
    {
        $request['queryParam'] = $this->processObject($request['queryParam']);
        $canonicalQueryString = $this->buildCanonicalQueryString($request['queryParam']);
        $hashedRequestPayload = hash('sha256', $request['body'] ?? '');
        $request['headers']['x-acs-content-sha256'] = $hashedRequestPayload;
        $canonicalHeaders = $this->buildCanonicalHeaders($request['headers']);
        $signedHeaders = $this->buildSignedHeaders($request['headers']);

        $canonicalRequest = implode("\n", [
            $request['httpMethod'],
            $request['canonicalUri'],
            $canonicalQueryString,
            $canonicalHeaders,
            $signedHeaders,
            $hashedRequestPayload,
        ]);

        $hashedCanonicalRequest = hash('sha256', $canonicalRequest);
        $stringToSign = "{$this->ALGORITHM}\n$hashedCanonicalRequest";

        $signature = strtolower(bin2hex(hash_hmac('sha256', $stringToSign, $this->AccessKeySecret, true)));

        $authorization = "{$this->ALGORITHM} Credential={$this->AccessKeyId},SignedHeaders=$signedHeaders,Signature=$signature";

        $request['headers']['Authorization'] = $authorization;
    }

    private function callApi($request)
    {
        try {
            // 通过cURL发送请求
            $url = "https://" . $request['host'] . $request['canonicalUri'];

            // 添加请求参数到URL
            if (!empty($request['queryParam'])) {
                $url .= '?' . http_build_query($request['queryParam']);
            }

            //echo $url;
            // 初始化cURL会话
            $ch = curl_init();

            // 设置cURL选项
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 禁用SSL证书验证，请注意，这会降低安全性，不应在生产环境中使用（不推荐！！！）
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回而不是输出内容
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->convertHeadersToArray($request['headers'])); // 添加请求头

            // 根据请求类型设置cURL选项
            switch ($request['httpMethod']) {
                case "GET":
                    break;
                case "POST":
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $request['body']);
                    break;
                case "DELETE":
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                    break;
                default:
                    echo "Unsupported HTTP method: " . $request['body'];
                    throw new Exception("Unsupported HTTP method");
            }

            // 发送请求
            $result = curl_exec($ch);

            // 检查是否有错误发生 DT 2024/12/11
            if (curl_errno($ch)) {
                return "Failed to send request: " . curl_error($ch);
            } else {
				return $result;
            }

        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        } finally {
            // 关闭cURL会话
            curl_close($ch);
        }
    }

    function formDataToString($formData)
    {
        $res = self::processObject($formData);
        return http_build_query($res);
    }

    function processObject($value)
    {
        // 如果值为空，则无需进一步处理
        if ($value === null) {
            return;
        }
        $tmp = [];
        foreach ($value as $k => $v) {
            if (0 !== strpos($k, '_')) {
                $tmp[$k] = $v;
            }
        }
        return self::flatten($tmp);
    }

    private static function flatten($items = [], $delimiter = '.', $prepend = '')
    {
        $flatten = [];
        foreach ($items as $key => $value) {
            $pos = \is_int($key) ? $key + 1 : $key;

            if (\is_object($value)) {
                $value = get_object_vars($value);
            }

            if (\is_array($value) && !empty($value)) {
                $flatten = array_merge(
                    $flatten,
                    self::flatten($value, $delimiter, $prepend . $pos . $delimiter)
                );
            } else {
                if (\is_bool($value)) {
                    $value = true === $value ? 'true' : 'false';
                }
                $flatten["$prepend$pos"] = $value;
            }
        }
        return $flatten;
    }


    private function convertHeadersToArray($headers)
    {
        $headerArray = [];
        foreach ($headers as $key => $value) {
            $headerArray[] = "$key: $value";
        }
        return $headerArray;
    }


    private function buildCanonicalQueryString($queryParams)
    {

        ksort($queryParams);
        // Build and encode query parameters
        $params = [];
        foreach ($queryParams as $k => $v) {
            if (null === $v) {
                continue;
            }
            $str = rawurlencode($k);
            if ('' !== $v && null !== $v) {
                $str .= '=' . rawurlencode($v);
            } else {
                $str .= '=';
            }
            $params[] = $str;
        }
        return implode('&', $params);
    }

    private function buildCanonicalHeaders($headers)
    {
        // Sort headers by key and concatenate them
        uksort($headers, 'strcasecmp');
        $canonicalHeaders = '';
        foreach ($headers as $key => $value) {
            $canonicalHeaders .= strtolower($key) . ':' . trim($value) . "\n";
        }
        return $canonicalHeaders;
    }

    private function buildSignedHeaders($headers)
    {
        // Build the signed headers string
        $signedHeaders = array_keys($headers);
        sort($signedHeaders, SORT_STRING | SORT_FLAG_CASE);
        return implode(';', array_map('strtolower', $signedHeaders));
    }
}
$sms = new SignatureAli();
$res = $sms->main($queryParam);
if(strpos($res, '"OK"') !== false && strpos($res, $DT['sms_ok']) === false) $res = $DT['sms_ok'].'/'.$DT['sms_api'].'/'.$res;
echo $res;
?>