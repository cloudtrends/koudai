<?php
/**
 * Created by PhpStorm.
 * User: haoyu
 * Date: 2014/12/25
 * Time: 20:58
 */

namespace common\services;

use common\api\HttpRequest;
use common\exceptions\PayException;
use common\helpers\StringHelper;
use common\models\BankConfig;
use common\models\Order;
use common\models\User;
use common\models\UserAccount;
use common\models\UserPayOrder;
use common\models\UserWithdraw;
use mobile\components\ApiUrl;
use Yii;
use common\helpers\TimeHelper;
use common\models\UserBankCard;
use yii\base\Object;
use yii\helpers\Url;
use yii\web\IdentityInterface;

class LLPayService extends Object
{
    const VALID_ORDER_LIMIT = 1440; // 有效时间一天,单位分钟

    // 日志category
    const LOG_CATEGORY = "koudai.llpay.*";

    //充值完后，客户端查询状态
    public function userChargeQuery($curUser, $no_order, $info_order)
    {
        if( !($curUser instanceof IdentityInterface) )
        {
            PayException::throwCodeExt(2102);
        }

        $info_order = json_decode($info_order, true);
        if(empty($info_order))
        {
            PayException::throwCodeExt(2226);
        }

        if( strtolower($info_order['action']) == strtolower("userCharge"))
        {
            $db = Yii::$app->db;

            // 是否存在对应成功的订单号
            $sql = "select "."
                      user_id,
                      third_platform,
                      pay_amount,
                      order_char_id,
                      status,
                      created_at,
                      updated_at
                    from ". UserPayOrder::tableName() .
                " where user_id={$curUser->id}
                        and order_char_id=\"{$no_order}\"
                        and status=" . UserPayOrder::STATUS_CHARGE_SUCCESS ."
                        and third_platform=" . BankConfig::PLATFORM_LLPAY;

            $existChargeOrder = $db->createCommand($sql)->queryOne();

            if( !empty($existChargeOrder))
                return true;

        }
        else if (strtolower($info_order['action']) == strtolower("userBindCard"))
        {
            if($curUser->card_bind_status == 1)
                return true;
        }
        return false;
    }

    // 用户充值
    public function userCharge($curUser, $amount, $pay_src)
    {
        if( !($curUser instanceof IdentityInterface) )
        {
            PayException::throwCodeExt(2102);
        }

        $uid = $curUser->id;
        $db = Yii::$app->db;

        // 如果存在一份已经申请的充值，且金额一致，状态为未初始状态
        $sql = "select "."
                   user_id,
                   third_platform,
                   pay_amount,
                   order_char_id,
                   status,
                   created_at,
                   updated_at
                from ". UserPayOrder::tableName() . "
                where user_id={$uid}
                    and pay_amount={$amount}
                    and status=". UserPayOrder::STATUS_CHARGE_INIT . "
                    and third_platform=" . BankConfig::PLATFORM_LLPAY;

        $existChargeOrder = $db->createCommand($sql)->queryOne();

        Yii::info("已存在的LL充值订单：".var_export($existChargeOrder,true));

        if( empty($existChargeOrder) or empty($existChargeOrder['order_char_id'])
            or (TimeHelper::Now() - $existChargeOrder['updated_at'] > 60 * ( LLPayService::VALID_ORDER_LIMIT - 1 )))
        {
            // 如果为空，或者订单已经过期, 则先置为过期
            $db->createCommand()->update(UserPayOrder::tableName(),[
                'status' => UserPayOrder::STATUS_CHARGE_EXPIRED,
            ],[
                'user_id' => $uid,
                'pay_amount' => $amount,
                'status' => UserPayOrder::STATUS_CHARGE_INIT,
                "third_platform" => BankConfig::PLATFORM_LLPAY,
                'updated_at' => TimeHelper::Now(),
            ]);

            // 然后重新生成一条记录
            $order_char_id = Order::generateOrderId();

            $existChargeOrder = [
                'user_id' => $uid,
                'third_platform' => BankConfig::PLATFORM_LLPAY,
                'pay_amount' => $amount,
                'order_char_id' => $order_char_id,
                'status' => UserPayOrder::STATUS_CHARGE_INIT,
                'created_at' => TimeHelper::Now(),
                'updated_at' => TimeHelper::Now(),
                'action' => UserPayOrder::ACTION_CHARGE_PAY,
                'pay_source' => $pay_src,
            ];

            $affected_rows = $db->createCommand()->insert( UserPayOrder::tableName(), $existChargeOrder )->execute();

            if(empty($affected_rows)){
                PayException::throwCodeExt(2207);
            }
        }


        $dt_order = strval(date("YmdHis", $existChargeOrder['updated_at']));

        $info_order = [
            'uid' => $uid,
            'action' => "userCharge",
            'pay_amount' => $amount,

        ];


        if( YII_ENV != 'prod' )
        {
            $money_order = "0.01";
        }
        else{
            $money_order = strval(sprintf("%.2f", $amount / 100 ));
        }

        // 参与签名的参数


        $signParams = [
            'oid_partner' => strval(self::LLPAY_OID_PARTNER),
            'sign_type' => "RSA",
            'busi_partner' => "101001",
            'no_order' => strval($existChargeOrder['order_char_id']),
            'dt_order' => $dt_order,
            'name_goods' => "充值".$money_order."元",
            'info_order' => json_encode($info_order),
            'money_order' => $money_order,
            'notify_url' =>  Url::toRoute('notify/lian-lian-charge-notify',true),
            'valid_order' => strval(self::VALID_ORDER_LIMIT),
            'risk_item' => $this->_getRiskItem($curUser),
        ];

        $sql = "select "."
                    no_agree
                from tb_user_bank_card
                where user_id={$uid}
                    and status= ". UserBankCard::STATUS_BIND;

        $existBankCard =  $db->createCommand($sql)->queryOne();
        $no_agree = empty($existBankCard['no_agree']) ? "" : $existBankCard['no_agree'];

        Yii::info("协议号：".var_export($no_agree,true));

        // 补充不参与签名的参数
        $payParams = $signParams;
        $payParams['sign'] = $this->_sign($signParams);
        $payParams['no_agree'] = strval($no_agree);
        $payParams['user_id'] = strval($curUser->username);
        $payParams['id_type'] = "0";
        $payParams['id_no'] = strval($curUser->id_card);
        $payParams['acct_name'] = strval($curUser->realname);

        Yii::info("返回参数:".var_export($signParams,true));
        // 如果是连连支付，返回特定 2002 错误码，表示需要绑卡需要跳转连连支付

        return [
            'code' => 2002,
            'msg' => "",
            'chargeOrder' => $existChargeOrder,
            'payParams' => $payParams,
        ];

        // 开启事务进行账户余额修改

        // 调用练练支付
    }

    public function chargeNotify($chargeResult)
    {

        // 用户状态修改
        $db = Yii::$app->db;

        $sql = "select * from ". UserPayOrder::tableName() .
            " where order_char_id=\"{$chargeResult['no_order']}\"";

        $existCharge = $db->createCommand($sql)->queryOne();

        if( empty($existCharge) )
        {
            PayException::throwCodeExt(2220);
        }

        $uid = $existCharge['user_id'];
        $affected_row = $db->createCommand()->update(UserPayOrder::tableName(),[
            "status" => UserPayOrder::STATUS_CHARGE_SUCCESS,
            "pay_result" => json_encode($chargeResult),
            "card_no" => empty($chargeResult['card_no']) ? "" : $chargeResult['card_no'],
            "third_platform_order_id" => empty($chargeResult['oid_paybill']) ? "" : $chargeResult['oid_paybill'],
            "updated_at" => TimeHelper::Now(),
        ],[
            "order_char_id" => $chargeResult['no_order'],
        ])->execute();

        if(empty($affected_row))
        {
            PayException::throwCodeExt(2220);
        }

        if( YII_ENV != 'prod' )
        {
            $amount = $existCharge['pay_amount'];
        }
        else{
            $amount = StringHelper::safeConvertCentToInt($chargeResult['money_order']);
        }

        // 更新用户资金信息
        $affected_row = UserAccount::updateAccount($uid, [
            ['usable_money', '+', $amount],
            ['total_money', '+', $amount],
        ],false);

        if(empty($affected_row)){
            PayException::throwCodeExt(2223);
        }

        UserAccount::addLog($uid,UserAccount::TRADE_TYPE_RECHARGE,$amount,"连连充值");

        Yii::info("Charge Success, chargeResult:" . var_export($chargeResult, true), self::LOG_CATEGORY);
    }

    // 连连绑卡，支付0.01元
    public function userBindCard($curUser, $bank_card, $no_order, $dt_order)
    {
        // YYYYMMDDHHIISS 14位精确到秒 比如2014年12月26号，17点15分10秒，20141226171510
        $pattern = '/^[0-9]{14}$/';
        if ( preg_match($pattern, $dt_order) == 0){
            PayException::throwCodeExt(2200,"$dt_order");
        }

        // 构造连连支付 0.01 元 的参数
        // 签名参数

        $uid = $curUser->id;
        $info_order = [
            'uid' => $uid,
            'action' => "userBindCard",
        ];

        // 需要参与签名的参数
        $signParams = [
            'oid_partner' => strval(self::LLPAY_OID_PARTNER),
            'sign_type' => "RSA",
            'busi_partner' => "101001",
            'no_order' => strval($no_order),
            'dt_order' => strval($dt_order),
            'info_order' => json_encode($info_order),
            'name_goods' => "绑卡支付0.01元",
            'money_order' => "0.01",
            'notify_url' =>  Url::toRoute('notify/lian-lian-bind-notify',true),
            'valid_order' => strval(self::VALID_ORDER_LIMIT),
            'risk_item' => $this->_getRiskItem($curUser),
        ];

        // 补充不参与签名的参数
        $payParams = $signParams;
        $payParams['sign'] = $this->_sign($signParams);
        $payParams['user_id'] = strval($curUser->username);
        $payParams['id_type'] = "0";
        $payParams['id_no'] = strval($curUser->id_card);
        $payParams['card_no'] = strval($bank_card);
        $payParams['acct_name'] = strval($curUser->realname);

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
            'payParams' => $payParams,
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
        UserAccount::addLog($uid, UserAccount::TRADE_TYPE_RECHARGE, $amount);

        if(empty($affected_row)){
            PayException::throwCodeExt(2223);
        }


        Yii::info("Bind Success parameter:" . var_export($bindResult, true), self::LOG_CATEGORY);
    }


    public function withdraw( $withdraw,
                              $money,
                              $phone_no)
    {
        // 非正式环境下，写死返回成功，add by yake
        if (YII_ENV != 'prod' && YII_ENV != 'pre_release') {
            return [
                'httpCode' => 200,
                'code' => '0',
                'message' => '',
            ];
        }

        if (!($withdraw instanceof UserWithdraw))
        {
            PayException::throwCodeExt(2105);
        }

        $order_id = $withdraw['order_id'];
        $dt_order = strval(date("YmdHis", $withdraw['updated_at']));

        // 1. 找到 $order_id 对应的用户信息

        $db = Yii::$app->db;

        $sql = "select * from " . User::tableName() ." u ".
            " inner join " . UserBankCard::tableName() . " ubc " .
            " on u.id = ubc.user_id where u.username =\"{$phone_no}\"";

        $userInfo = $db->createCommand($sql)->queryOne();
        $bankInfo = BankConfig::findOne([
            'bank_id' => $userInfo['bank_id'],
            'third_platform' => BankConfig::PLATFORM_LLPAY,
        ]);

        if (empty($bankInfo))
        {
            PayException::throwCodeExt();
        }

        Yii::info("userInfo=".var_export($userInfo, true));
        Yii::info("bankInfo=".var_export($bankInfo->toArray(), true));

        // 2. 发起提现请求
        $amount = $withdraw['money'] / 100;
        $info_order = json_encode([
            'uid' => $withdraw['user_id'],
            'action' => "userWithDraw",
            'amount' => $amount
        ]);

        // 需要参与签名的参数
        $signParams = [
            'oid_partner' => strval(self::LLPAY_OID_PARTNER),
            'sign_type' => "RSA",
            //'busi_partner' => "101001",
            'no_order' => strval($order_id),
            'dt_order' => strval($dt_order),// 格式：YYYYMMDDH24MISS 14 位数字，精确到秒
            'money_order' => strval($amount),
            'flag_card' => "0",
            'card_no' => strval($userInfo['card_no']),
            "acct_name" => strval($userInfo['realname']),
            'info_order' => json_encode($info_order),
            'notify_url' =>  "http://42.96.204.114/koudai/frontend/web/notify/lian-lian-withdraw-notify",//ApiUrl::toRoute('notify/lian-lian-withdraw-notify',true),
            'api_version' => "1.2",
            //'valid_order' => strval(self::VALID_ORDER_LIMIT),
        ];

        /*
        return [
            'httpCode' => 200,
            'code' => -1,
            'message' => "签名测试",
        ];*/

        $signParams['sign'] = $this->_sign($signParams);
        Yii::info("signParams=".var_export($signParams,true));

        $ret = $this->_withdrawReq($signParams);

        // http 请求返回200 表示成功
        if ( $ret['code'] != HttpRequest::HTTP_Status_200_Code_OK)
        {
            PayException::throwCodeExt(2227);
        }

        $withdraw_resp = json_decode($ret['resp'],true);

        $code = $withdraw_resp['ret_code'];
        if( intval($code) == 0 )
        {
            $code = 0;
        }

        return [
            'httpCode' => 200,
            'code' => $code,
            'message' => $withdraw_resp['ret_msg'],
        ];
    }


    public function withdrawQuery($withdraw)
    {
        if (YII_ENV != 'prod' && YII_ENV != 'pre_release') {
            return [
                'httpCode' => 200,
                'code' => '0',
                'message' => '',
            ];
        }

        if (!($withdraw instanceof UserWithdraw))
        {
            PayException::throwCodeExt(2105);
        }

        $order_id = $withdraw['order_id'];

        // 1. 发起提现查询请求


        // 需要参与签名的参数
        $signParams = [
            'oid_partner' => strval(self::LLPAY_OID_PARTNER),
            'sign_type' => "RSA",
            'no_order' => strval($order_id),
            'type_dc' => "1",
        ];


        $signParams['sign'] = $this->_sign($signParams);
        Yii::info("signParams=".var_export($signParams,true));

        $ret = $this->_withdrawQuery($signParams);

        // http 请求返回200 表示成功
        if ( $ret['code'] != HttpRequest::HTTP_Status_200_Code_OK)
        {
            PayException::throwCodeExt(2227);
        }

        $withdraw_resp = json_decode($ret['resp'],true);

        $code = $withdraw_resp['ret_code'];
        if( intval($code) == 0 )
        {
            $code = 0;
        }

        return $withdraw_resp;
    }

    // -------------- 私有函数开始 --------------

    // 获取风控参数
    private function _getRiskItem($curUser)
    {
        $risk_item = [
            'frms_ware_category' => "2009",
            'user_info_mercht_userno' => strval($curUser->username),
            'user_info_bind_phone' => strval($curUser->username),
            'user_info_dt_register' => strval(date("YmdHis",$curUser->created_at)),
            'user_info_full_name' => strval($curUser->realname),
            'user_info_id_type' => "0",
            'user_info_id_no' => strval($curUser->id_card),
            'user_info_identify_state' => "1",
            'user_info_identify_type' => "3",
        ];

        return json_encode($risk_item);
    }

    private function _sign($params)
    {
        // 签名数组去空值，并且重新排序
        $signParams = [];
        foreach($params as $key=>$param){
            if( isset( $param ))
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
        $private_key_file = Yii::getAlias('@common') . '/config/cert/kd_ll_private_key.pem';
        if(!file_exists($private_key_file)){
            Yii::info($private_key_file);
            PayException::throwCodeExt(2225);
        }

        $private_key = file_get_contents($private_key_file);

        $pi_key = openssl_pkey_get_private($private_key);

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
        $public_key_file = Yii::getAlias('@common') . '/config/certkey/kd_ll_public_key.pem';
        if(!file_exists($public_key_file)){
            PayException::throwCodeExt(2225);
        }

        $pubKey = file_get_contents( $public_key_file );

        //转换为openssl格式密钥
        $res = openssl_get_publickey($pubKey);

        //调用openssl内置方法验签，返回bool值
        $result = (bool)openssl_verify($data, base64_decode($sign), $res,OPENSSL_ALGO_MD5);

        //释放资源
        openssl_free_key($res);

        //返回资源是否成功
        return $result;
    }

    private function _withdrawReq($signParams)
    {
        $httpReq = new HttpRequest();
        $httpReq->url = "https://yintong.com.cn/traderapi/cardandpay.htm";
        $httpReq->method = "POST";
        $httpReq->postDataFormat = HttpRequest::POST_DATA_TYPE_JSON;
        $httpReq->postFields = $signParams;

        $ret = $httpReq->send();
        return $ret;
    }

    private function _withdrawQuery($signParams)
    {
        $httpReq = new HttpRequest();
        $httpReq->url = "https://yintong.com.cn/traderapi/orderquery.htm";
        $httpReq->method = "POST";
        $httpReq->postDataFormat = HttpRequest::POST_DATA_TYPE_JSON;
        $httpReq->postFields = $signParams;

        $ret = $httpReq->send();
        return $ret;
    }

    const LLPAY_TEST_OID_PARTNER = "201408071000001543";
    const LLPAY_TEST_OID_MD5_KEY = "201408071000001543test_20140812";
    const LLPAY_OID_PARTNER = "201409181000031503";

} 