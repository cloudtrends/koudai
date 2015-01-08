<?php

namespace console\controllers;

use Yii;
use yii\db\Query;
use common\helpers\MessageHelper;
use common\models\NoticeSms;
use common\models\User;


class SendsmsController extends BaseController{


    public function actionIndex(){
        //获取需要发短信的信息
        $userArr = (new Query)->select(['id','user_id','remark'])->from(NoticeSms::tableName())->where(['status'=>array(NoticeSms::SEND_WAIT,NoticeSms::SEND_FAIL)])->all();
        if (!empty($userArr)){
            $successArr = $errorArr = $userid = array();

            //获取所有用户ID
            foreach ($userArr as $userArrVal){
                $userid[] = $userArrVal['user_id'];
            }
            $userid = array_unique($userid);

            //获取用户手机号
            $userinfo = (new Query)->select(['id','phone'])->from(User::tableName())->where(['id'=>$userid])->all();
            $userinfo = $this->init_arr_by_key($userinfo);
            foreach ($userArr as $userArrVal){
                //$userinfo[$userArrVal['user_id']] = '15900678785';
                $sendresult = MessageHelper::sendSMS($userinfo[$userArrVal['user_id']],$userArrVal['remark']);
                if ($sendresult){
                    $successArr[] = $userArrVal['id'];
                }else{
                    $errorArr[] = $userArrVal['id'];
                }
            }

            //更新数据
            if ($successArr){
                NoticeSms::updateAll(
                    ['status' => NoticeSms::SEND_SUCCESS],
                    ['id' => $successArr]
                );
            }

            if ($errorArr){
                NoticeSms::updateAll(
                    ['status' => NoticeSms::SEND_FAIL],
                    ['id' => $errorArr]
                );
            }

        }
    }

    private function init_arr_by_key($arr){
        if (empty($arr)){
            return $arr;
        }
        $result = array();
        foreach ($arr as $arrVal){
            $result[$arrVal['id']] = $arrVal['phone'];
        }
        return $result;
    }


}
