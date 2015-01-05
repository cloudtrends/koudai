<?php
namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use backend\controllers\BaseController;
use common\models\AppVersionInfo;
use backend\controllers\AppConfInfoController;
/**
 * AppVersionInfo controller
 */
class AppVersionInfoController extends BaseController
{
	/**
	 * 接口列表
	 */
	public function actionList()
	{
		$query = AppVersionInfo::find()->where(['status'=>1])->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$versions = $query->offset($pages->offset)->limit($pages->limit)->all();
		$app_list = AppConfInfoController::getAppList();
		return $this->render('list', [
			'versions' => $versions,
			'apps'  => $app_list,
			'pages' => $pages,
		]);
	}
	
	/**
	 * 添加接口
	 */
	public function actionAdd()
	{
		$model = new AppVersionInfo();
		// 有提交则装载post值并验证
		if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
			$model->auditor = 'yakehuang';
			if ($model->save()) {
				return $this->redirectMessage('添加成功', self::MSG_SUCCESS, Url::toRoute('app-version-info/list'));
			} else {
				return $this->redirectMessage('添加失败', self::MSG_ERROR);
			}
		}
		$app_list = AppConfInfoController::getAppList();
		return $this->render('add', array('model' => $model, 'apps' => $app_list));
	}
	
	public function actionEdit($id)
	{
		$id = intval($id);
		$model = $this->findModel($id);
		// 有提交则装载post值并验证
		if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
			$model->auditor = 'yakehuang';
			if ($model->save()) {
				return $this->redirectMessage('编辑成功', self::MSG_SUCCESS, Url::toRoute('app-version-info/list'));
			} else {
				return $this->redirectMessage('编辑失败', self::MSG_ERROR);
			}
		}
		$app_list = AppConfInfoController::getAppList();
		return $this->render('edit', array(
			'model'		=> $model, 'apps' => $app_list
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
		return $this->redirect(['app-version-info/list']);
	}
	
	protected function findModel($id)
	{
		if (($model = AppVersionInfo::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}