<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2014/12/15
 * Time: 16:19
 */

namespace common\api\external;

use Yii;
use common\api\HttpRequest;
use common\api\llpay\LLpaySubmit;
use common\api\llpay\LLpayNotify;


class LLPay {

    // -------------- 私钥加密，公钥解密 --------------
    /*
     * RSA私钥加密，解密需要用RSA公钥
     */
    public static function encryptWithPrivateKey($data)
    {
        $pi_key = openssl_pkey_get_private(self::$private_key);

        //私钥加密
        openssl_private_encrypt($data, $encrypted, $pi_key);

        //加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的
        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    /*
     * RSA私钥加密，解密需要用RSA公钥
     */
    public static function decryptWithPublicKey($data)
    {
        $pu_key = openssl_pkey_get_public(self::$public_key);

        // 公钥解密
        openssl_public_decrypt(base64_decode($data),$decrypted,$pu_key);
        return $decrypted;
    }


    // -------------- 公钥加密，私钥解密 --------------
    public static function encryptWithPublicKey($data)
    {
        $pu_key = openssl_pkey_get_public(self::$public_key);

        //公钥加密
        openssl_public_encrypt($data,$encrypted,$pu_key);
        $encrypted = base64_encode($encrypted);

        return $encrypted;
    }

    /*
     * RSA私钥加密，解密需要用RSA公钥
     */
    public static function decryptWithPrivateKey($data)
    {
        $pi_key = openssl_pkey_get_private(self::$private_key);

        // 公钥解密
        openssl_private_decrypt(base64_decode($data),$decrypted,$pi_key);
        return $decrypted;
    }


    // 签名之前重新排序数字段
    public static function sortFields(&$fields){
        if (is_array($fields)){
            ksort($fields);
            reset($fields);
        }
        return $fields;
    }

    public static function md5Sign($fields)
    {

        //$fields['key'] = "201408071000001543test_20140812";
        //$signStr = http_build_query($fields);// . "&key=201408071000001543test_20140812";
        $signStr = "";
        foreach($fields as $key => $value)
        {
            $signStr = $signStr . "{$key}={$value}&";
        }
        $signStr = $signStr."key=201408071000001543test_20140812";

        $signStr = stripslashes($signStr);
        $md5str = md5($signStr);
        Yii::info("签名参数:".var_export($fields,true));
        Yii::info("签名原串:".$signStr);
        Yii::info("签名:".$md5str);
        return $md5str;
    }


    public static function wapPay()
    {
        $httpReq = new HttpRequest();
        $httpReq->url = "https://yintong.com.cn/llpayh5/authpay.htm";
        $httpReq->method = "POST";
        $httpReq->postDataFormat = HttpRequest::POST_DATA_TYPE_ORIGIN;

        // 需要签名的字段
        // 风控参数
        $risk_item = array(
            "frms_ware_category" => 2004,
            "user_info_mercht_userno" => "15102105045",
            "user_info_bind_phone" => "15102105045",
            "user_info_dt_register" => "20141201150000",
        );

        $risk_item_json_str = json_encode($risk_item);

        require_once Yii::getAlias("@common") . "/api/llpay/llpay_submit.class.php";


        $llpaySubmit = new LLpaySubmit();

        // post
        $postFields = array(
            "oid_partner" => self::LLPAY_TEST_OID_PARTNER,
            "user_id" => "changhaoyu",
            "sign_type" => "MD5",
            "busi_partner" => "101001",
            "no_order" => "201412151003",
            "dt_order" => "20141215113553",
            "name_goods" => "羽毛球",
            "money_order" => "0.1",
            "app_request" => "3",
            "card_no" => "6214850211097651",
            //"pay_type" => "D",
            "info_order" => "用户13958069593购买羽毛球3桶",
            "url_return" => "http://10.10.110.246/llpay/return_url.php",
            "notify_url" => "http://10.10.110.246/llpay/notify_url.php",
            //"risk_item" => $risk_item_json_str,
            //"id_type" => 0,
            "id_no" => "430103198702112519",
            "acct_name" => "常昊宇",
            "valid_order" => "30",
        );

        $req_data = $llpaySubmit->buildRequestPara($postFields);

        $httpReq->postFields = "req_data=" . $req_data;
        $ret = $httpReq->send();
        $httpCode = $ret['code'];
        if($httpCode != HttpRequest::HTTP_Status_Code_OK)
        {

        }

        return [
            'code' => $httpCode,
            'message' => $ret['resp'],
            'curl_getinfo' => $ret['curl_getinfo'],
        ];

    }

    const LLPAY_TEST_OID_PARTNER = "201408071000001543";
    const LLPAY_OID_PARTNER = "201409181000031503";

    private static $version = "1.1";
    private static $private_key = <<<EOT
-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQCrTYU37d99Yssx1HlxrbAHx9CrTX0fgCu7yP8xlJk89Veyl+Vx
3cgllVUO7AltY+78LWAZV+KJskjs66n4/5nw3o8jtJb3+QcLfViasl9AebqoH166
/RFJz4JNb4jJEYSVCsyx0naMLF4ENqQLiqPza2ovXUNag4PyGp4zBS+KAwIDAQAB
AoGABVBdTpPZd/lFlmEh903NBSDEr1uzAvQl5yhgCjiy3Do8IzUlD/gySkAsqE7Y
KAWOl1INBhw80cqvCnJxDmFXdB1irdey4lEUSbPcpUJAfnzyI6ngQehK4ePd2tMw
6Z/wDX7xRkMxivcQ52cU9s/n1ibfzmiUKs2iGLn5rse96nkCQQDZ0/gogBk4wMXF
hVBauZZ5vE6gqCs+ZKS5tqAO0kk6QDUzCGJLvb/l7acuGAoyx4xntve1cSGKKkGg
S+UywxKXAkEAyVJfweR/z29ZuIl8GDaiIyw8BviSciPeygXEKfhBBkbWlpFpiIJh
Kh92WHYbrWgLAG5toLJxKRZmzeHi3EitdQJACa3BeQs4E619HCmwSFe2t/IGDF1s
jnkqWJYkxoPRfSUdOAdHVtY3kJ/erc2jpl33fyRCHW3Jb7ow8E5vALJqQQJAVT3Y
p7s9VrJ6FcW40nPHgQcIv5beQw/nFDkOzwp7VdIGqCgXvCIgS/qYXGpd27Vy+xLG
vkTv3wrKKqBbMxRexQJBALR9AiNukbN++PAGv1OrYvnu8JccASV/zqmyHtBc7Psb
hxbbUBl8OsWrWLjdFZ8uCud45hJvFwJ/lmPU7Ue7jqQ=
-----END RSA PRIVATE KEY-----
EOT;

    private static $public_key = <<<EOT
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCrTYU37d99Yssx1HlxrbAHx9Cr
TX0fgCu7yP8xlJk89Veyl+Vx3cgllVUO7AltY+78LWAZV+KJskjs66n4/5nw3o8j
tJb3+QcLfViasl9AebqoH166/RFJz4JNb4jJEYSVCsyx0naMLF4ENqQLiqPza2ov
XUNag4PyGp4zBS+KAwIDAQAB
-----END PUBLIC KEY-----
EOT;

} 