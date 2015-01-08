<?php
namespace mobile\components;

use Yii;

class View extends \yii\web\View
{
	/**
	 * 入口文件，不包括域名的目录
	 */
	public $baseUrl;
	
	/**
	 * 域名
	 */
	public $hostInfo;
	
	/**
	 * $hostInfo + $baseUrl
	 */
	public $absBaseUrl;
	
	public function init()
	{
		parent::init();
		$this->baseUrl = Yii::$app->getRequest()->getBaseUrl();
		$this->hostInfo = Yii::$app->getRequest()->getHostInfo();
		$this->absBaseUrl = $this->hostInfo . $this->baseUrl;
	}
}