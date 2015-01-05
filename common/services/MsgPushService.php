<?php
/**
 * Created by PhpStorm.
 * User: haoyu
 * Date: 2014/11/18
 * Time: 16:31
 */

namespace common\services;

use common\exceptions\InvestException;
use common\models\MsgPushTask;
use yii;
use yii\base\Object;
use yii\base\UserException;

use common\api\external\XingeApp;
use common\api\external\Message;
use common\api\external\MessageIOS;
use common\api\external\TagTokenPair;
use common\api\external\ClickAction;
use common\api\external\Style;
use common\api\external\TimeInterval;
//use common\api\external\ParamsBase;
//use common\api\external\RequestBase;

use common\api\HttpRequest;

class MsgPushService extends Object
{
    const accessId = '2200062785'; 
    const secretKey = '10805537dcb1d246bcce4fc7583843be';
    
    
    private function getErrInfo($code){
        return array(
            'code' => $code,
            "msg" => InvestException::$ERROR_MSG[$code]
        );
    }

    private function getTextErrInfo($code){
        return array(
            'code' => $code,
            "msg" => MsgPushTask::$textOpCode[$code]
        );
    }
    private function getXinggeErrInfo($code){
        return array(
            'code' => $code,
            "msg" => MsgPushTask::$xinggeOpCode[$code]
        );
    }
    public function sendTextMsg($msg, $mobile)
    {
        // http://210.5.158.31/hy/?uid=90073&auth=c6ff639c0eb0e4cb7c1505e808ea914b&mobile=15102105045&msg=%E6%AD%A5%E8%A1%8C%E8%A1%97&expid=0
        $para = array(
            'uid' => MsgPushTask::txtEnterpriseUid,
            'auth' => md5(MsgPushTask::txtEnterpriseCode . MsgPushTask::txtEnterprisePwd),
            'msg' => $msg,
            'expid' => 0,
            'encode' => "utf-8",
        );


        // 如果是字符串，需要转成数组
        if ( is_string($mobile) )
        {
            $mobile = explode(",",$mobile);
        }

        if (!is_array($mobile) || count($mobile) > 2000){
            return $this->getErrInfo(1401);
        }

        $validMobile = array();

        foreach($mobile as $m)
        {
            if( self::checkMobileNum($m))
            {
                $validMobile[] = $m;
            }
        }

        if (count($validMobile) < 1){
            return $this->getErrInfo(1402);
        }

        $para['mobile'] = implode(",",$validMobile);

        $httpReq = new HttpRequest();
        $httpReq->url = MsgPushTask::txtUrl;
        $httpReq->postFields = $para;
        $httpReq->method = "POST";

        $ret = $httpReq->send();
        if ( $ret['code'] != HttpRequest::HTTP_Status_Code_OK)
        {
            return $this->getErrInfo(1403);
        }

        if ( $ret['resp'] != 0)
        {
            return $this->getTextErrInfo($ret['resp']);
        }


        return true;
    }

    public function updateStatus($task_id,$status){
        $affected_row = Yii::$app->db->createCommand()->update( MsgPushTask::tableName(),[
            'status' => $status
        ],[
            "task_id" => $task_id
        ])->execute();

        return $affected_row;
    }

    public function checkMobileNum($moblie){
        return true;
    }
    /* --------------------------------------- 短信推送 Ends --------------------------------------- */
    /* --------------------------------------- ios推送  --------------------------------------- */
    //下发IOS账号消息
    /*
    ACCESS ID 2200062785
    ACCESS KEY I88Q5B9EKL4N
    SECRET KEY 10805537dcb1d246bcce4fc7583843be
    */
    function PushAccountListIOS($msg, $mobile) {
        $push = new XingeApp(self::accessId, self:: secretKey);
        $mess = new MessageIOS($msg);
        $mess->setExpireTime(86400);
        $mess->setAlert($msg);
        $mess->setBadge(1);
        $mess->setSound("beep.wav");
        $acceptTime1 = new TimeInterval(0, 0, 23, 59);
        $mess->addAcceptTime($acceptTime1);
        $accountList = $this->findMobile($mobile);
        $r = $push->PushAccountList(0, $accountList, $mess, XingeApp::IOSENV_DEV);
        $ret['code'] = $r['ret_code'];     
        $ret['msg'] = empty($r['result']['0']) ? "" : $r['result']['0'];
        if ( $ret['msg'] != 0)
        {
            return $this->getXinggeErrInfo($ret['msg']);
        }
        return $ret;
    }   
      /* --------------------------------------- 向多人推送以及Android平台  --------------------------------------- */
    function PushAccountList($msg, $mobile){
        $push = new XingeApp(self::accessId, self:: secretKey);
        $mess = new Message();
        $mess->setExpireTime(86400);
        $mess->setTitle('title');
        $mess->setContent($msg);
        $mess->setType(Message::TYPE_MESSAGE);
        $accountList = $this->findMobile($mobile);
        $r = $push->PushAccountList(0, $accountList, $mess,1);
        //var_dump($r);exit;
        $ret['code'] = $r['ret_code'];
        $ret['msg'] = empty($r['result']['0']) ? "" : $r['result']['0'];
        if ( $ret['msg'] != 0)
        {
            return $this->getXinggeErrInfo($ret['msg']);
        }
        //var_dump($ret);exit;
        return ($ret);
    }
    //下发所有设备
    function PushAllDevices($msg) {
        $push = new XingeApp(self::accessId, self:: secretKey);
        $mess = new Message($msg);
        $mess->setType(Message::TYPE_MESSAGE);
        $mess->setTitle("title");
        $mess->setContent($msg);
        $mess->setExpireTime(86400);
        $r = $push->PushAllDevices(0, $mess,2);
        $ret['code'] = $r['ret_code'];
        //var_dump($mess);
//        var_dump($r);
//        var_dump($ret);exit;
        return ($ret);
    }

    
    protected function findMobile($mobile) {
        // 如果是字符串，需要转成数组
        if (is_string($mobile)) {
            $mobile = explode(",", $mobile);
        }
        if (!is_array($mobile) || count($mobile) > 2000) {
            //UserException::throwCodeExt(1301);
            return $this->getErrInfo(1401);
        }
        $accountList = array();

        foreach ($mobile as $m) {
            if (self::checkMobileNum($m)) {
                $accountList[] = $m;
            }
        }
        if (count($accountList) < 1) {
            //UserException::throwCodeExt(1302);
            return $this->getErrInfo(1402);
        }
        return $accountList;
    }

}