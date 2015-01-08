<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\helpers\Html;

/**
 * Base controller
 * 
 * @property \yii\web\Request $request The request component.
 * @property \yii\web\Response $response The response component.
 * @property common\models\Client $client The Client model.
 */
abstract class BaseController extends Controller
{
	// 由于都是api接口方式，所以不启用csrf验证
	public $enableCsrfValidation = false;
	
	public function init()
	{
		parent::init();
		// 参数有callback的话则是jsonp
		if ($this->request->get('callback')) {
			$this->getResponse()->format = Response::FORMAT_JSONP;
		} else {
			$this->getResponse()->format = Response::FORMAT_JSON;
		}
	}
	
	public function beforeAction($action)
	{
		// 用于微信的openid登录
		if (Yii::$app->user->getIsGuest() && $this->getRequest()->get('contact_id')) {
			Yii::$app->user->loginByAccessToken(trim($this->getRequest()->get('contact_id')));
		}
		return parent::beforeAction($action);
	}
	
	public function afterAction($action, $result)
	{
		$result = parent::afterAction($action, $result);
		if ($this->getResponse()->format == Response::FORMAT_JSONP) {
			// jsonp返回数据特殊处理
			$callback = Html::encode($this->request->get('callback'));
			$result = [
				'data' => $result,
				'callback' => $callback,
			];
		}
		return $result;
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
	
	/**
	 * 获得请求客户端信息
	 * 从request中获得，便于调试，有默认值
	 */
	public function getClient()
	{
		return Yii::$app->getRequest()->getClient();
	}

    public function params()
    {
        return array_merge($_GET, $_POST);
    }
}