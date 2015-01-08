<?php
namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use backend\controllers\BaseController;
use common\models\AppConfInfo;

/**
 * AppConfInfo controller
 */
class AppConfInfoController extends BaseController
{
	/**
	 * 图片类型列表
	 */
	public function actionList()
	{
		$query = AppConfInfo::find()->where(['status'=>1])->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$confs = $query->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('list', [
			'confs' => $confs,
			'pages' => $pages,
		]);
	}
	
	/**
	 * 添加图片类型
	 */
	public function actionAdd()
	{
		$model = new AppConfInfo();
		// 有提交则装载post值并验证
		if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
			$model->conf_version = time();
			$model->auditor = 'yakehuang';
			if ($model->save()) {
				return $this->redirectMessage('添加成功', self::MSG_SUCCESS, Url::toRoute('app-conf-info/list'));
			} else {
				return $this->redirectMessage('添加失败', self::MSG_ERROR);
			}
		}
		return $this->render('add', array('model' => $model));
	}
	
	public function actionEdit($id)
	{
		$id = intval($id);
		$model = $this->findModel($id);
		// 有提交则装载post值并验证
		if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
			$model->conf_version = time();
			$model->auditor = 'yakehuang';
			if ($model->save()) {
				return $this->redirectMessage('编辑成功', self::MSG_SUCCESS, Url::toRoute('app-conf-info/list'));
			} else {
				return $this->redirectMessage('编辑失败', self::MSG_ERROR);
			}
		}
		return $this->render('edit', array(
			'model'		=> $model
		));
	}

	public function actionView($id)
	{
		$id = intval($id);
		$model = $this->findModel($id);
		return $this->render('view', array(
			'model'		=> $model
		));
	}

	public function actionDelete($id)
	{
		$id = intval($id);
		$model = $this->findModel($id);
		$model->status = 0;
		$model->update();
		return $this->redirect(['app-conf-info/list']);
	}

	public static function getAppList() {
		$app_list = AppConfInfo::find()->where(["status"=>1])->asArray()->all();
		$app_info_list = array();
		foreach ($app_list as $app_info) {
			$app_info_list[$app_info["id"]] = $app_info["app_name"];
		}
		return $app_info_list;
	}
	
	protected function findModel($id)
	{
		if (($model = AppConfInfo::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}