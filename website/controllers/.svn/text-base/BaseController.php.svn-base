<?php
namespace website\controllers;

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
}