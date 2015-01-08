<?php
namespace frontend\controllers;

use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends BaseController
{
	public $layout = 'site';
	
	/**
	 * 首页
	 */
	public function actionIndex()
	{
		$this->getResponse()->format = Response::FORMAT_HTML;
		return $this->render('index');
	}
}