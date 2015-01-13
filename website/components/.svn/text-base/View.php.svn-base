<?php
namespace website\components;

use Yii;
use common\models\NoticeSms;
use yii\helpers\Html;
use common\helpers\StringHelper;

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
	public $userName;
	
	public function init()
	{
		parent::init();
		$this->baseUrl = Yii::$app->getRequest()->getBaseUrl();
		$this->hostInfo = Yii::$app->getRequest()->getHostInfo();
		$this->absBaseUrl = $this->hostInfo . $this->baseUrl;
		$this->userName = Yii::$app->user->identity['username'];
	}

	public function GetMsgCount()
	{
		return NoticeSms::findNewNoticeByCount(Yii::$app->user->identity->account->user_id);
	}

	public function GetUserName()
	{
		return Html::encode( StringHelper::blurPhone(Yii::$app->user->identity['username']) );
	}

}