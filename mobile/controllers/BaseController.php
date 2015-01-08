<?php
namespace mobile\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;

/**
 * Base controller
 * 
 * @property \yii\web\Request $request The request component.
 * @property \yii\web\Response $response The response component.
 */
abstract class BaseController extends Controller
{
    public $enableCsrfValidation = false;

	public function init()
	{
		parent::init();

        $login_url = Url::toRoute(['/site/login'], true);
        $wx_login_url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx9a8fce4b97312d3f&redirect_uri=".$login_url."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
        Yii::$app->user->loginUrl = $wx_login_url;

        // other init
	}
	
	/**
	 * 获得请求对象
	 */
	public function getRequest()
	{
		return Yii::$app->getRequest();
	}
	
	/**
	 * 获得返回对象
	 */
	public function getResponse()
	{
		return Yii::$app->getResponse();
	}

    public function params(){
        return array_merge($_GET,$_POST);
    }
}