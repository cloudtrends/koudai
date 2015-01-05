<?php
namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use backend\controllers\BaseController;
use common\models\AppImageType;

/**
 * AppImageType controller
 */
class AppImageTypeController extends BaseController
{
	/**
	 * 图片类型列表
	 */
	public function actionList()
	{
		$query = AppImageType::find()->where(['status'=>1])->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$imageTypes = $query->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('list', [
			'imageTypes' => $imageTypes,
			'pages' => $pages,
		]);
	}
	
	/**
	 * 添加图片类型
	 */
	public function actionAdd()
	{
		$model = new AppImageType();
		// 有提交则装载post值并验证
		if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
			$model->auditor = 'yakehuang';
			if ($model->save()) {
				return $this->redirectMessage('添加成功', self::MSG_SUCCESS, Url::toRoute('app-image-type/list'));
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
			if ($model->save()) {
				return $this->redirectMessage('编辑成功', self::MSG_SUCCESS, Url::toRoute('app-image-type/list'));
			} else {
				return $this->redirectMessage('编辑失败', self::MSG_ERROR);
			}
		}
		return $this->render('edit', array(
			'model'		=> $model
		));
	}

	public function actionDelete($id)
	{
		$id = intval($id);
		$type = $this->findModel($id);
		$type->status = 0;
		$type->update();
		return $this->redirect(['app-image-type/list']);
	}
	
	protected function findModel($id)
	{
		if (($model = AppImageType::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}