<?php

namespace common\services;

use common\models\Order;
use Yii;
use yii\base\Object;
use common\exceptions\InvestException;
use common\exceptions\PayException;
use common\helpers\TimeHelper;
use common\models\BankConfig;
use common\models\User;
use common\models\UserBankCard;
use common\models\UserPayOrder;
use common\models\UserWithdraw;
use common\api\HttpRequest;
use yii\web\IdentityInterface;


require_once Yii::getAlias('@common') . '/api/umpay/common.php';
require_once Yii::getAlias('@common') . '/api/umpay/mer2Plat.php';
require_once Yii::getAlias('@common') . '/api/umpay/plat2Mer.php';


/**
 * 联动支付模块 service
 */
class PayService extends Object
{

    // 日志category
    const LOG_CATEGORY = "koudai.pay.*";

    // 商户id
    const MER_ID = wzd_mer_id;

    // 版本（接口文档中为固定值）
    const SERVICE_VERSION = '4.0';

    // 银行卡类型
    const CARD_TYPE_CREDIT = 'CREDITCARD';
    const CARD_TYPE_DEBIT = 'DEBITCARD';


    /**
     * 用户支付
     */
    public function pay($curUser, $pay_amount, $pay_src)
    {
        if( !($curUser instanceof IdentityInterface) )
        {
            PayException::throwCodeExt(2102, gettype($curUser));
        }


        if( empty($pay_amount) or $pay_amount < 1){
            PayException::throwCodeExt(2321);
        }

        // 电话号码和用户ID
        $phone_no = $curUser->username;
        $uid = $curUser->id;

        // 后台先记录支付流水
        // 1. 找到绑定的银行卡号
        $db = Yii::$app->db;
        $sql = "select * from " . User::tableName() ." u ".
            " inner join " . UserBankCard::tableName() . " ubc " .
            " on u.id = ubc.user_id where u.id ={$uid}";

        $result = $db->createCommand($sql)->queryOne();
        Yii::info("已绑定银行卡:" . var_export($result, true));

        $order_char_id = Order::generateOrderId16();
        $new_pay_order = [
            'order_char_id' => $order_char_id,
            'user_id' => $uid,
            'user_name' => $result['username'],
            'card_no' => $result['card_no'],
            'pay_amount' => $pay_amount,
            'third_platform' => BankConfig::PLATFORM_UMPAY,
            'pay_source' => $pay_src,
            'created_at' => TimeHelper::Now(),
            'updated_at' => TimeHelper::Now(),
            'action' => UserPayOrder::ACTION_INVEST_PAY
        ];

        Yii::info("支付订单信息:" . var_export($new_pay_order, true));

        $affected_rows = $db->createCommand()->insert(UserPayOrder::tableName(),$new_pay_order)->execute();

        if(empty($affected_rows))
        {
            PayException::throwCodeExt(2323);
        }

        //$order_id = $db->getLastInsertID();

        if( strlen($order_char_id) < 4 or strlen($order_char_id) > 16)
        {
            PayException::throwCodeExt(2324);
        }

        // 2.
        $map = self::serviceMap("syn_pay");
        $map->put("media_id", $phone_no);
        $map->put("media_type", "MOBILE");
        //$map->put("order_id", $order_id);
        $map->put("order_id", $order_char_id);
        $map->put("mer_date", date("Ymd"));
        // 非线上环境先写死支付1分钱，线上环境扣实款
        if (YII_ENV == 'prod') {
            $map->put("amount", $pay_amount);;
        } else {
            $map->put("amount", 1);
        }
        $map->put("amt_type", "RMB");
        $map->put("busi_no", "1");

        Yii::info(var_export($map,true));

        // 发送请求
        self::sendRequest($map, $httpResp, $httpRespMap );

        // debug 输出结果
        Yii::info(var_export($httpRespMap,true));

        $code = $httpRespMap->get('ret_code') == '0000' ? '0' : $httpRespMap->get('ret_code');
        if (!empty($httpRespMap->H_table))
        {
            // 联动支付是实时的，记录平台返回的结果
            $pay_result = json_encode($httpRespMap->H_table);
            $trade_no = $httpRespMap->H_table['trade_no'];

            // 00131062 该订单已支付成功请不要重复提交 参考联动错误码文档
            if($code == 0 or $code == "00131062" )
            {
                $updated_data = [
                    'pay_result' => $pay_result,
                    'third_platform_order_id' => $trade_no,
                    'status' => UserPayOrder::STATUS_UMP_PAY_SUCCESS,
                ];
            }
            else
            {
                $updated_data = [
                    'pay_result' => $pay_result,
                    'third_platform_order_id' => $trade_no,
                    'status' => UserPayOrder::STATUS_UMP_PAY_FAILED,
                ];
            }

            $db->createCommand()->update(UserPayOrder::tableName(),$updated_data,[
                'order_char_id' => $order_char_id,
            ])->execute();
        }

        return [
            'httpCode' => $httpResp['code'],
            'code' => $code,
            'message' => $httpRespMap->get('ret_msg')
        ];
    }

    /**
     * 用户签约
     */
    public function userBindCard(
        $curUser, // 用户信息
        $card_id // 卡号
    ){

        if( !($curUser instanceof IdentityInterface) )
        {
            PayException::throwCodeExt(2102, gettype($curUser));
        }

        $card_holder = $curUser->realname;
        $identity_code = $curUser->id_card; // 身份证号码
        $phone_no = $curUser->username;
        // 1. 发起绑卡请求
        $map = self::serviceMap("user_reg");

        // 业务参数
        $map->put('pub_pri_flag', '2');
        $map->put('identity_type', 'IDENTITY_CARD');
        $map->put('identity_code', $identity_code);
        $map->put('card_id', $card_id);
        $map->put('card_holder', $card_holder);
        $map->put('media_id',$phone_no);
        $map->put('card_type','0');
        $map->put('busi_no','1');

        self::sendRequest($map, $httpResp, $httpRespMap);

        $code = $httpRespMap->get('ret_code');
        $bindResult = $httpRespMap->H_table;

        //00160083	银行卡绑定关系已经存在，无需重复绑定 参考联动返回码文档
        if( intval($code) == 0 or $code == "00160083")
        {
            // 绑定成功，或者重复绑定
            $code = 0;
            $status = UserBankCard::STATUS_BIND;
        }
        else
        {
            // 绑定失败
            $status = UserBankCard::STATUS_UNBIND;
        }

        return [
            'httpCode' => $httpResp['code'],
            'code' => $code == '0000' ? '0' : $code,
            'message' => $httpRespMap->get('ret_msg'),
            'status' => $status,
            'bindResult' => $bindResult
        ];
    }

    /**
     * 用户解绑
     */
    public function unBindCard($phone_no)
    {
        $map = self::serviceMap("user_cancel");

        // 业务参数
        $map->put("media_id", $phone_no);
        $map->put("media_type", "MOBILE");
        $map->put("busi_no", "1");

        // 发送请求
        self::sendRequest($map, $httpResp, $httpRespMap);

        Yii::info(var_export($httpRespMap,true));

        return [
            'httpCode' => $httpResp['code'],
            'code' => $httpRespMap->get('ret_code'),
            'message' => $httpRespMap->get('ret_msg')
        ];
    }
    
    /**
     * 提现
     */
    public function withdraw(
        $order_id,
        $money,
        $phone_no)
    {
        // 非正式环境下，写死返回成功，add by yake
        if (YII_ENV != 'prod') {
            return [
                'httpCode' => 200,
                'code' => '0',
                'message' => '',
            ];
        }

        $db = Yii::$app->db;

        $sql = "select * from " . User::tableName() ." u ".
            " inner join " . UserBankCard::tableName() . " ubc " .
            " on u.id = ubc.user_id where u.username =\"{$phone_no}\"";

        $result = $db->createCommand($sql)->queryOne();
        // $bankInfo = UserBankCard::getBankInfo($result['bank_id']);
        $bankInfo = BankConfig::findOne([
        	'bank_id' => $result['bank_id'],
        	'third_platform' => BankConfig::PLATFORM_UMPAY,
        ]);

        $map = self::serviceMap("transfer_direct_req");
        $map->put('notify_url', "http://api.koudailc.com/app/pay-notify");
        $map->put('order_id', $order_id);
        $map->put('mer_date', date("Ymd",time()));
        $map->put('amount', $money);
        $map->put('recv_account_type', '00');
        $map->put('recv_bank_acc_pro', '0');
        $map->put('recv_account', $result['card_no'] );
        $map->put('recv_user_name',  $result['realname'] );
        $map->put('identity_type', '01');
        $map->put('identity_code',$result['id_card']);
        $map->put('identity_holder',  $result['realname']);
        $map->put('media_type', 'MOBILE');
        $map->put('media_id',  $result['username']);
        $map->put('recv_gate_id', $bankInfo['abbreviation']);
        $map->put('bank_brhname', "上海市徐汇支行");
        $map->put('purpose', "提现");

        Yii::info("map original=".var_export($map,true), PayService::LOG_CATEGORY);

        self::sendRequest($map, $httpResp, $httpRespMap);

        $code = $httpRespMap->get('ret_code');
        $withdrawResult = $httpRespMap->H_table;

        // 2. 根据用户ID查找绑卡信息
        $db = Yii::$app->db;

        $db->createCommand()->update(UserWithdraw::tableName(),[
            "result" => json_encode($withdrawResult),
            "status" => $withdrawResult['trade_state'],
            "updated_at" => time(),
        ],[
            "id" => $order_id
        ])->execute();

        return [
            'httpCode' => $httpResp['code'],
            'code' => $code == '0000' ? '0' : $code,
            'message' => $httpRespMap->get('ret_msg'),
        ];
    }
    
    /**
     * 提现付款查询
     */
    public function withdrawQuery($order_id)
    {
        $withdraw = UserWithdraw::findOne($order_id);
        $map = self::serviceMap("transfer_query");
        $map->put('order_id', $order_id);
        $map->put('mer_date', date('Ymd', $withdraw->review_time));
        self::sendRequest($map, $httpResp, $httpRespMap);
        $result = $httpRespMap->H_table;
        return $result;
    }
    
    /**
     * 提现付款回调处理
     */
    public function withdrawHandleNotify($params)
    {
    	//return UmpPay::withdrawHandleNotify($params);
        $map = new \HashMap();
        foreach ($params as $key => $value) {
            $map->put($key, $value);
        }

        $resData = new \HashMap();
        try {
            //获取UMPAY平台请求商户的支付结果通知数据,并对请求数据进行验签
            //如验证平台签名正确，即应响应UMPAY平台返回码为0000。【响应返回码代表通知是否成功，和通知的交易结果（支付失败、支付成功）无关】
            //验签支付结果通知 如验签成功，则返回ret_code=0000
            $reqData = \PlatToMer::getNotifyRequestData($map);
            $resData->put("ret_code","0000");
        } catch (\Exception $e) {
            //如果验签失败，则抛出异常，返回ret_code=1111
            Yii::error('验证签名发生异常：' . $e->getMessage(), PayService::LOG_CATEGORY);
            $resData->put("ret_code","1111");
        }

        $resData->put("mer_id", $map->get("mer_id"));
        $resData->put("sign_type", $map->get("sign_type"));
        $resData->put("mer_date", $map->get("mer_date"));
        $resData->put("order_id", $map->get("order_id"));
        $resData->put("version", $map->get("version"));
        $resData->put("ret_msg", "success");
        $returnUrl = \MerToPlat::notifyResponseData($resData);
        $return = '<META NAME="MobilePayPlatform" CONTENT="' . $returnUrl . '" />';

        return [
            'code' => $resData->get('ret_code') == '0000' ? '0' : $resData->get('ret_code'),
            'return' => $return,
        ];
    }
    
    /**
     * 获得商户号可用余额，单位分
     */
    public function getRemainMoney()
    {
        $map = self::serviceMap("query_account_balance");
        self::sendRequest($map, $httpResp, $httpRespMap);
        $result = $httpRespMap->H_table;
        if ($result['ret_code'] == '0000') {
            return intval($result['bal_sign']);
        } else {
            return 0;
        }
    }



    /*
     * 获取Map，包含公共的参数
     * return HashMap $map
     * */
    private static function serviceMap($service){
        $map = new \HashMap();
        $map->put("service", $service);
        $map->put('sign_type', 'RSA');
        $map->put('mer_id', wzd_mer_id);
        $map->put('version', '4.0');
        $map->put('charset', 'UTF-8');
        return $map;
    }

    /*
    * 发送请求获取返回值
    * param HashMap $map
    * param Array $httpResp
    * param HashMap $httpRespMap
    * return HashMap $map
    * */
    private static function sendRequest($map, &$httpResp, &$httpRespMap)
    {
        Yii::info("map original=".var_export($map,true),PayService::LOG_CATEGORY);
        $reqData = \MerToPlat::makeRequestDataByGet($map);
        Yii::info("map encoded=".var_export($map,true),PayService::LOG_CATEGORY);
        if($reqData === 1301)
        {
            // 证书配置错误
            InvestException::throwCodeExt(1301);
        }
        $httpReq = new HttpRequest();
        $httpReq->url = $reqData->getUrl();

        Yii::info("httpReq=".var_export($httpReq,true),PayService::LOG_CATEGORY);
        // http 请求返回结果
        $httpResp = $httpReq->send();

        Yii::info("httpResp=".var_export($httpResp,true),PayService::LOG_CATEGORY);

        // $httpResp['resp'] 为页面返回结果，解析过后放到 $httpRespMap中
        $httpRespMap = \PlatToMer::getResDataByHtml($httpResp['resp']);
    }

}



