<?php
namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use common\models\DeviceInfo;
use common\models\DeviceVisitInfo;

/**
 * Device controller
 */
class DeviceController extends BaseController
{
	/**
	 * 设备列表
	 */
	public function actionList()
	{
		$query = DeviceInfo::find()->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$devices = $query->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('list', [
			'devices' => $devices,
			'pages' => $pages,
		]);
	}
	
	/**
	 * 启动记录列表
	 */
	public function actionVisitList()
	{
		$query = DeviceVisitInfo::find()->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$visits = $query->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('visit-list', [
			'visits' => $visits,
			'pages' => $pages,
		]);
	}
}