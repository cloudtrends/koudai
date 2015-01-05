<?php
namespace mobile\controllers;

use Yii;
use common\models\UserContact;
use mobile\components\ApiUrl;

/**
 * created by JohnnyLin
 * AppAjaxController
 */
class AppAjaxController extends BaseController
{

    public function actionAjax(){
        $params = $this->params();
        $res = $this->error_res();
        switch($params['type']) {
            case 'login':
                if (!empty($params['username']) && !empty($params['password']) && !empty($params['OPENID']) ){
                    $url = ApiUrl::toRoute(['user/login']);
                    $paramsArr = array('username'=>$params['username'],'password'=>$params['password']);
                    $result = json_decode(self::postData($url,$paramsArr));
                    if ($result->code == 0){
                        $check_user = UserContact::find()->where(['user_id'=>$result->user->username,'contact_id'=>$params['OPENID']])->count();
                        if (!$check_user){
                            //插入用户表方法
                            UserContact::instance()->InsertDate($result->user->username,$params['OPENID']);
                        }
                        $res = $this->init_res();
                    }else{
                        $res = $this->error_res('用户名或密码错误');
                    }
                }else{
                    $res = $this->error_res('用户名或密码不能为空');
                }
                break;
            case 'getRegCode':
                if (!empty($params['mobile'])){
                    $url = ApiUrl::toRoute(['user/reg-get-code']);
                    //$url = HOSTURL .'/frontend/web/user/reg-get-code?';
                    $paramsArr = array('phone'=>$params['mobile']);
                    $result_obj = json_decode(self::postData($url,$paramsArr));
                    $result = (Array)$result_obj;
                    if ($result["code"] == 0 && $result["result"] == true){
                        $res = $this->init_res();
                    }else{
                        $res = $this->error_res('获取短信验证码失败');
                    }
                }
                break;
            case 'register':
                //is_reg 1为忘记密码 2为注册
                if (!empty($params['mobile']) && !empty($params['msgcode']) && !empty($params['password']) && !empty($params['OPENID']) ){
                    if ( isset($params['is_reg']) && $params['is_reg'] == 2 ){
                        $url = ApiUrl::toRoute(['user/register']);
                        $paramsArr = array('phone'=>$params['mobile'],'code'=>$params['msgcode'],
                            'password'=>$params['password'],'contact_id'=>$params['OPENID'],'type'=>0);
                        $result_obj = json_decode(self::postData($url,$paramsArr));
                        $result = (Array)$result_obj;
                        if ($result['code'] == 0 ){
                            $res = $this->init_res();
                        }else{
                            $res = $this->error_res($result['message']);
                        }
                    }

                    if ( isset($params['is_reg']) && $params['is_reg'] == 1 ){

                    }
                }
                break;

            default :
                $res = $this->error_res();
                break;
        }
        echo json_encode($res);
        exit;
    }


    public static function postData($url,$data = array()){
        $ch = curl_init();
        $timeout = 300;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $handles = curl_exec($ch);
        curl_close($ch);
        return $handles;
    }

    private function error_res($result = ''){
        return array(
            "code" => "error",
            "msg" => "操作失败！",
            "info" => $result,
        );
    }
    private function init_res($result = null){
        $_res = array(
            "code" => "success",
            "msg" => "成功！",
            "info" => $result,
        );
        return $_res;
    }

}