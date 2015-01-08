<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2015/1/5
 * Time: 10:51
 */

namespace frontend\controllers;

use Yii;
use common\exceptions\PayException;
use common\services\LLPayService;
use yii\base\Exception;

class NotifyController extends BaseController
{
    protected $llPayService;

    public function __construct($id, $module, LLPayService $llPayService,$config = [])
    {
        $this->llPayService = $llPayService;
        parent::__construct($id, $module, $config);
    }

    // 连连绑卡回调
    public function actionLianLianBindNotify()
    {
        $bindResult = [];
        try
        {
            $bindResult = $this->getLLPayResp();

            $this->llPayService->bindNotify($bindResult);

            return [
                'ret_code' => "0000",
                'ret_msg' => "交易成功",
            ];
        }
        catch(PayException $e) {
            Yii::info("Bind Failed, parameter:" . var_export($bindResult, true), LLPayService::LOG_CATEGORY);
            Yii::info("Bind Failed, code=" . $e->getCode() . ",message=" . $e->getMessage(), LLPayService::LOG_CATEGORY);
            throw $e;
        }
    }


    // 连连充值回调
    public function actionLianLianChargeNotify()
    {
        $chargeResult = [];
        try
        {
            $chargeResult = $this->getLLPayResp();

            $this->llPayService->chargeNotify($chargeResult);

            return [
                'ret_code' => "0000",
                'ret_msg' => "交易成功",
            ];
        }
        catch(Exception $e) {
            Yii::info("Charge Failed, chargeResult:" . var_export($chargeResult, true), LLPayService::LOG_CATEGORY);
            Yii::info("Charge Failed, code=" . $e->getCode() . ",message=" . $e->getMessage(), LLPayService::LOG_CATEGORY);
            throw $e;
        }
    }

    private function getLLPayResp()
    {
        $str = file_get_contents("php://input");
        Yii::info($str,LLPayService::LOG_CATEGORY);
        $resp = json_decode($str, true);
        Yii::info(var_export($resp,true),LLPayService::LOG_CATEGORY);

        if(empty($resp)){
            PayException::throwCodeExt(2104);
        }

        //首先对获得的商户号进行比对
        if( empty($resp['oid_partner']) or $resp['oid_partner'] != LLPayService::LLPAY_OID_PARTNER) {
            //商户号错误
            PayException::throwCodeExt(2201);
        }

        // 支付成功
        if ( empty($resp['result_pay']) or $resp['result_pay'] != "SUCCESS" )
        {
            PayException::throwCodeExt(2203);
        }

        return $resp;
    }

    private function getVal($data,$k){
        if(isset($data[$k])){
            return trim($data[$k]);
        }
        return "";
    }
} 