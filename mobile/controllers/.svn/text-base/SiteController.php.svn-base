<?php
namespace mobile\controllers;

use mobile\components\ApiUrl;
use Yii;
use common\models\UserContact;
use mobile\controllers\WeixinapiController;
use yii\filters\AccessControl;
use yii\helpers\Url;

class SiteController extends BaseController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                // 除了下面的action其他都需要登录
                'except' => [
                    'index',
                    'login',
                    'register',
                    'regcheck',
                    //'information',
                    //'bank',
                    //'setpwd',
                    //'kdb-confirm',
                    //'kdb-invest'
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

	/**
	 * 默认首页
	 */
    public function actionIndex()
    {
        return $this->render('index');
    }

    //登陆页
    public function actionLogin()
    {
        // 登录先去拿一个token
        $params = $this->params();
        if (empty($params['code']))
        {
            return $this->render('error');
        }
        return $this->render('login', array('OPENID' => $params['code']));
    }

    //手机号输入页面
    //ValidCode邀请编码
    public function actionRegister(){
        $params = $this->params();

        //有邀请码注册方式传参
        if (!empty($params['ValidCode'])){
            return $this->render('register',array('ValidCode'=>$params['ValidCode']));
        }

        if (empty($params['OPENID'])|| empty($params['type'] ) )
        {
            return $this->render('error');
        }
        return $this->render('register',array('OPENID'=>$params['OPENID'],'type'=>$params['type']));
    }

    //密码验证页面
    public function actionRegcheck(){
        $params = $this->params();

        //有邀请码注册方式传参
        if (!empty($params['ValidCode']) && !empty($params['mobile']) ){
            return $this->render('regcheck',array('mobile'=>$params['mobile'],'ValidCode'=>$params['ValidCode']));
        }

         if(empty($params['OPENID']) || empty($params['type'])|| empty($params['mobile']))
        {
            return $this->render('error');
        }
        return $this->render('regcheck',array('mobile'=>$params['mobile'],'type'=>$params['type'],'OPENID'=>$params['OPENID']));
    }

    // --------------- 口袋宝相关 ---------------
    // 口袋宝投资页面
    public function actionKdbInvest()
    {
        /*
        $curUser = Yii::$app->user->identity;
        if( empty($curUser) )
        {
            $this->redirect(Yii::$app->user->loginUrl);
        }*/
        $url = ApiUrl::toRoute(['koudaibao/info']);
        $ret = $this->postData($url,$obj);
        if($ret !== true)
        {
            return $ret;
        }
        //echo iconv("UTF-8","GBK",$obj['info']['title']);die;

        $apr = $obj['info']['apr'];
        $remain_money = sprintf('%.2f',$obj['info']['remain_money'] /100);

        if ($remain_money >= 100000000)
        {
            $remain_money = sprintf("%.2f", $remain_money/100000000)."亿";
        }
        else if ($remain_money >= 10000000)
        {
            $remain_money = sprintf("%.2f", $remain_money/10000)."万";
        }
        else if ($remain_money >= 1000000)
        {
            $remain_money = sprintf("%.2f", $remain_money/10000)."万";
        }
        else{
            $remain_money = $remain_money ."元";
        }

        $min_invest_money = $obj['info']['min_invest_money'] / 100;
        $daily_withdraw_limit = sprintf('%.2f',$obj['info']['daily_withdraw_limit'] / 100);

        return $this->render('kdb-invest',
            [
                'apr' => $apr,
                'remain_money' => $remain_money,
                'min_invest_money' => $min_invest_money,
                'daily_withdraw_limit' => $daily_withdraw_limit
            ]
        );

    }

    // 口袋宝投资确认页
    public function actionKdbConfirm()
    {
        $params = $this->params();
        if(empty($params['orderid']) || empty($params['money']) )
        {
            return $this->render('error');
        }
        return $this->render('kdb-confirm',
            [
                'money'=> $params['money'],
                'orderid'=> $params['orderid'],
            ]
        );
    }

    // 投资成功
    public function actionSuccess()
    {
        $params = $this->params();
         if(empty($params['money']))
        {
            return $this->render('error');
        }
        return $this->render('success',
            [
                'money'=> $params['money'],
            ]
        );
    }

    public function postData($url, &$resp)
    {
        $ch = curl_init();
        $timeout = 300;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $handles = curl_exec($ch);
        curl_close($ch);

        $resp = json_decode($handles,true);
        if(!$resp or $resp['code'] != 0)
        {
            return $this->render('error');
        }
        return true;
    }

    //    身份验证
    public function actionInformation(){

        $params = $this->params();
        return $this->render('information');
    }

    //    绑定银行卡
    public function actionBank()
    {
        $curUser = Yii::$app->user->identity;
        $params = $this->params();
         if(empty($curUser['realname']))
        {
            return $this->render('error');
        }
        return $this->render('bank',[
            'realname' => $curUser['realname'],
        ]);
    }

    //    设置密码
    public function actionSetpwd(){
        $params = $this->params();
        return $this->render('setpwd'
               
                );
    }


}