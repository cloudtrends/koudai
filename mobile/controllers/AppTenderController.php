<?php
namespace mobile\controllers;

use Yii;
use yii\web\Response;
use mobile\controllers\AppAjaxController;
use mobile\components\ApiUrl;

/**
 * AppTenderController
 * APP投标页面逻辑模块
 */
class AppTenderController extends BaseController
{

    //首页
	public function actionIndex()
    {
        $url = ApiUrl::toRoute(['koudaibao/info']);
        $obj = json_decode(AppAjaxController::postData($url));

        $apr = $obj->info->apr;
        $remain_money = sprintf('%.2f',$obj->info->remain_money /100);
   
        if ($remain_money >= 100000000){
            $remain_money = sprintf("%.2f", $remain_money/100000000)."亿"; 
        }else if ($remain_money >= 10000000){
            $remain_money = sprintf("%.2f", $remain_money/10000)."万"; 
        }else if ($remain_money >= 1000000){
            $remain_money = sprintf("%.2f", $remain_money/10000)."万"; 
        }else{
            $remain_money = $remain_money ."元";
        }
        $min_invest_money = $obj->info->min_invest_money /100;
        $daily_withdraw_limit = sprintf('%.2f',$obj->info->daily_withdraw_limit /100);
        return $this->render('index',array('apr'=>$apr,'remain_money'=>$remain_money,
                    'min_invest_money'=>$min_invest_money,'daily_withdraw_limit'=>$daily_withdraw_limit));
    }

    public function actionConfirm(){
        $params = $this->params();
        return $this->render('confirm',
            array(
                'money'=> $params['money'],
                'orderid'=> $params['orderid'],
            )
        );
    }

    public function actionSuccess(){
        $params = $this->params();
        return $this->render('success',
            array(
                'params'=> $params
            )
        );
    }


    public function actionBank(){
        $params = $this->params();
        return $this->render('bank',array());
    }

    public function actionInformation(){
        $params = $this->params();
        
        return $this->render('information',array());
    }

    public function actionSetpwd(){
        $params = $this->params();
        return $this->render('setpwd',array());
    }

}