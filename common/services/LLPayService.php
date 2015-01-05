<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2014/12/25
 * Time: 20:58
 */

namespace common\services;

use common\exceptions\PayException;
use common\helpers\StringHelper;
use common\models\BankConfig;
use common\models\Order;
use common\models\User;
use common\models\UserAccount;
use common\models\UserCharge;
use Yii;
use common\helpers\TimeHelper;
use common\models\UserBankCard;
use yii\base\Object;
use yii\helpers\Url;
use yii\web\IdentityInterface;

class LLPayService extends Object
{
    const VALID_ORDER_LIMIT = 1440; // 有效时间一天,单位分钟


    //充值完后，客户端查询状态
    public function userChargeQuery($curUser, $no_order, $info_order)
    {
        if( !($curUser instanceof IdentityInterface) )
        {
            PayException::throwCodeExt(2102);
        }

        $db = Yii::$app->db;

        // 是否存在对应成功的订单号
        $sql = "select "."
                  user_id,
                  third_platform,
                  amount,
                  order_id,
                  status,
                  created_at,
                  updated_at
                from ". UserCharge::tableName() .
            " where user_id={$curUser->id}
                    and order_id=\"{$no_order}\"
                    and status=" . UserCharge::STATUS_CHARGE_SUCCESS;

        $existChargeOrder = $db->createCommand($sql)->queryOne();

        if( empty($existChargeOrder))
            return false;

        return $existChargeOrder;
    }

    // 用户充值
    public function userCharge($curUser, $amount)
    {
        if( !($curUser instanceof IdentityInterface) )
        {
            PayException::throwCodeExt(2102);
        }

        $uid = $curUser->id;
        $db = Yii::$app->db;

        // 如果存在一份已经申请的充值，且金额一致，状态为未初始状态
        $sql = "select "."
                 uc.user_id user_id,
                  uc.third_platform third_platform,
                  uc.amount amount,
                  uc.order_id order_id,
                  uc.status status,
                  uc.created_at created_at,
                  uc.updated_at updated_at,
                  ubc.no_agree
                from tb_user_charge uc left join tb_user_bank_card ubc
                    on uc.user_id = ubc.user_id
                where ubc.status=". UserBankCard::STATUS_BIND."
                    and uc.user_id={$uid}
                    and uc.amount={$amount}
                    and uc.status=" . UserCharge::STATUS_CHARGE_INIT;

        $existChargeOrder = $db->createCommand($sql)->queryOne();

        if( empty($existChargeOrder) or empty($existChargeOrder['order_id'])
            or (TimeHelper::Now() - $existChargeOrder['updated_at'] > 60 * ( LLPayService::VALID_ORDER_LIMIT - 1 )))
        {
            // 如果为空，或者订单已经过期, 则重新生成一条记录
            $order_id = Order::generateOrderId();

            $existChargeOrder = [
                'user_id' => $uid,
                'third_platform' => BankConfig::PLATFORM_LLPAY,
                'amount' => $amount,
                'order_id' => $order_id,
                'status' => UserCharge::STATUS_CHARGE_INIT,
                'created_at' => TimeHelper::Now(),
                'updated_at' => TimeHelper::Now(),
            ];

            $affected_rows = $db->createCommand()->insert(UserCharge::tableName(), $existChargeOrder)->execute();

            if(empty($affected_rows)){
                PayException::throwCodeExt(2207);
            }
        }

        $dt_order = strval(date("YmdHis", $existChargeOrder['updated_at']));

        $info_order = [
            'uid' => $uid,
            'action' => "userCharge",
        ];


        $money_order = strval(sprintf("%.2f", $amount / 100 ));
        $signParams = [
            'oid_partner' => strval(self::LLPAY_OID_PARTNER),
            'sign_type' => "RSA",
            'busi_partner' => "101001",
            'no_order' => strval($existChargeOrder['order_id']),
            'dt_order' => $dt_order,
            'name_goods' => "充值".$money_order."元",
            'info_order' => json_encode($info_order),
            'money_order' => $money_order,
            'notify_url' =>  Url::toRoute('notify/lian-lian-charge-notify',true),
            'valid_order' => strval(self::VALID_ORDER_LIMIT),
        ];

        // 补充不参与签名的参数
        $signParams['sign'] = $this->_sign($signParams);
        $signParams['no_agree'] = $existChargeOrder['no_agree'];
        $signParams['user_id'] = strval($curUser->username);
        $signParams['id_type'] = "0";
        $signParams['id_no'] = strval($curUser->id_card);
        $signParams['acct_name'] = strval($curUser->realname);

        // 如果是连连支付，返回特定 2000 错误码，表示需要绑卡需要跳转连连支付

        return [
            'code' => 2002,
            'msg' => "",
            'chargeOrder' => $existChargeOrder,
            'payParams' => $signParams,
        ];

        // 开启事务进行账户余额修改

        // 调用练练支付
    }

    public function chargeNotify($chargeResult)
    {

        // 用户状态修改
        $db = Yii::$app->db;

        $sql = "select * from ". UserCharge::tableName() .
            " where order_id=\"{$chargeResult['no_order']}\"";

        $existCharge = $db->createCommand($sql)->queryOne();

        if( empty($existCharge) )
        {
            PayException::throwCodeExt(2220);
        }

        $uid = $existCharge['user_id'];
        $affected_row = $db->createCommand()->update(UserCharge::tableName(),[
            "status" => UserCharge::STATUS_CHARGE_SUCCESS,
            "charge_result" => json_encode($chargeResult),
            "card_no" => empty($chargeResult['card_no']) ? "" : $chargeResult['card_no'],
            "updated_at" => TimeHelper::Now(),
        ],[
            "order_id" => $chargeResult['no_order'],
        ])->execute();

        if(empty($affected_row))
        {
            PayException::throwCodeExt(2220);
        }

        $amount = StringHelper::safeConvertCentToInt($chargeResult['money_order']);

        // 更新用户资金信息
        $affected_row = UserAccount::updateAccount($uid, [
            ['usable_money', '+', $amount],
            ['total_money', '+', $amount],
        ],false);

        if(empty($affected_row)){
            PayException::throwCodeExt(2223);
        }

        UserAccount::addLog($uid,UserAccount::TRADE_TYPE_RECHARGE,$amount,"连连充值");

        Yii::info("Charge Success, chargeResult:" . var_export($chargeResult, true), 'koudai.pay.*');
    }

    // 连连绑卡，支付0.01元
    public function userBindCard($uid, $no_order, $dt_order)
    {
        // YYYYMMDDHHIISS 14位精确到秒 比如2014年12月26号，17点15分10秒，20141226171510
        $pattern = '/^[0-9]{14}$/';
        if ( preg_match($pattern, $dt_order) == 0){
            PayException::throwCodeExt(2200,"$dt_order");
        }

        // 构造连连支付 0.01 元 的参数
        // 签名参数

        $info_order = [
            'uid' => $uid,
            'action' => "userBindCard",
        ];

        $signParams = [
            'oid_partner' => strval(self::LLPAY_OID_PARTNER),
            'sign_type' => "RSA",
            //'oid_partner' => strval(self::LLPAY_TEST_OID_PARTNER),
            //'sign_type' => "MD5",
            'busi_partner' => "101001",
            'no_order' => strval($no_order),
            'dt_order' => strval($dt_order),
            'info_order' => json_encode($info_order),
            'name_goods' => "绑卡支付0.01元",
            'money_order' => "0.01",
            'notify_url' =>  Url::toRoute('notify/lian-lian-bind-notify',true),
            'valid_order' => strval(self::VALID_ORDER_LIMIT),
        ];

        // 补充不参与签名的参数
        $signParams['sign'] = $this->_sign($signParams);

        // 如果是连连支付，返回特定 2000 错误码，表示需要绑卡需要跳转连连支付
        $code = 2000;
        $status = UserBankCard::STATUS_UNBIND; // 状态为未绑定
        $bindResult = [];
        $msg = "";

        return [
            'code' => $code,
            'msg' => $msg,
            'status' => $status,
            'bindResult' => $bindResult,
            'payParams' => $signParams,
        ];
    }

    // 绑卡回调
    public function bindNotify($bindResult)
    {
        // 用户状态修改
        $db = Yii::$app->db;

        $sql = "select * from ". UserBankCard::tableName() . " ubc " .
            " where ubc.no_order=\"{$bindResult['no_order']}\"";

        $existBindBank = $db->createCommand($sql)->queryOne();

        if( empty($existBindBank) )
        {
            PayException::throwCodeExt(2202);
        }

        $no_agree = empty($bindResult['no_agree']) ? "" : $bindResult['no_agree'];
        $affected_row = $db->createCommand()->update(UserBankCard::tableName(),[
            "status" => UserBankCard::STATUS_BIND,
            "bind_result" => json_encode($bindResult),
            "no_agree" => $no_agree,
            "updated_at" => TimeHelper::Now(),
        ],[
            "no_order" => $bindResult['no_order'],
        ])->execute();

        if(empty($affected_row))
        {
            PayException::throwCodeExt(2202);
        }

        // 用户状态修改
        $uid = $existBindBank['user_id'];
        $affected_row = $db->createCommand()->update(User::tableName(),[
            "card_bind_status" => UserBankCard::STATUS_BIND,
        ],[
            "id" => $uid,
        ])->execute();

        if(empty($affected_row))
        {
            PayException::throwCodeExt(2204);
        }


        $amount = StringHelper::safeConvertCentToInt($bindResult['money_order']);
        // 更新用户资金信息
        $affected_row = UserAccount::updateAccount($uid, [
            ['usable_money', '+', $amount],
            ['total_money', '+', $amount],
        ],false);

        if(empty($affected_row)){
            PayException::throwCodeExt(2223);
        }


        Yii::info("Pay Success parameter:" . var_export($bindResult, true), 'koudai.pay.*');
    }

    // -------------- 私有函数开始 --------------

    private function _sign($params)
    {
        // 签名数组去空值，并且重新排序
        $signParams = [];
        foreach($params as $key=>$param){
            if( !empty( $param ))
            {
                $signParams[$key] = $param;
            }
        }

        ksort($signParams);
        reset($signParams);

        Yii::info("签名参数: signParams=".var_export($signParams,true));

        // 排序后用私钥签名
        $signStr = $this->_signParamsToString($signParams);


        if($signParams['sign_type'] == "RSA")
        {
            Yii::info("RSA 签名原始串:".var_export($signStr,true));
            $sign = $this->_rsaSign($signStr);
            Yii::info("RSA 签名结果串:".var_export($sign,true));
        }
        else
        {
            $signStr = $signStr . "&key=" . LLPayService::LLPAY_TEST_OID_MD5_KEY;
            Yii::info("MD5 签名原始串:".var_export($signStr,true));
            $sign = md5($signStr);
            Yii::info("MD5 签名结果串:".var_export($sign,true));
        }

        return $sign;
    }


    // 签名参数从数组变成字符串
    private function _signParamsToString($signParams)
    {
        $signStr = "";
        foreach($signParams as $k=>$v){
            $signStr = $signStr . "{$k}={$v}&";
        }

        $signStr = substr($signStr, 0, strlen($signStr) - 1);
        return $signStr;
    }


    // -------------- 私钥加密，公钥解密 --------------
    /*
     * RSA私钥签名
     */
    private function _rsaSign($data)
    {
        $pi_key = openssl_pkey_get_private($this->private_key);

        //私钥加密,采用 MD5withRSA 算法
        openssl_sign($data, $encrypted, $pi_key, OPENSSL_ALGO_MD5);

        //
        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    /**RSA验签
     * $data 待签名数据(需要先排序，然后拼接)
     * $sign 需要验签的签名
     * 验签用连连支付公钥
     * return 验签是否通过 bool值
     */
    private function _rsaVerify($data, $sign)  {
        //读取连连支付公钥文件
        $pubKey = file_get_contents('key/llpay_public_key.pem');

        //转换为openssl格式密钥
        $res = openssl_get_publickey($pubKey);

        //调用openssl内置方法验签，返回bool值
        $result = (bool)openssl_verify($data, base64_decode($sign), $res,OPENSSL_ALGO_MD5);

        //释放资源
        openssl_free_key($res);

        //返回资源是否成功
        return $result;
    }


    const LLPAY_TEST_OID_PARTNER = "201408071000001543";
    const LLPAY_TEST_OID_MD5_KEY = "201408071000001543test_20140812";
    const LLPAY_OID_PARTNER = "201409181000031503";

    private static $version = "1.1";
    private $private_key = <<<EOT
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

    private $public_key = <<<EOT
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDxMqNpnRS82+8oZ5lng5l686ov
1QvrO6xD8VptLtbrbL4ClmHNZxPRmMpKMSoLyeyFSCkZG6EQ/FKQi+ln2qeefuWj
GuYfUiyARScvGzW67+3RCb7HOZQlL9MhIfADaSDW0CH93PTlHfYKm54GiU3ciI4g
RZpd0hJZJ3JOUCfC0wIDAQAB
-----END PUBLIC KEY-----
EOT;
} 