<?php
namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use common\models\ArticleType;
use common\models\Article;

/**
 * ArticleType controller
 */
class ArticleTypeController extends BaseController
{
	public function actionList()
	{
		$query = ArticleType::find()->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$articleTypes = $query->offset($pages->offset)->limit($pages->limit)->all();
		
		return $this->render('list', [
			'articleTypes' => $articleTypes,
			'pages' => $pages,
		]);
	}
	
	public function actionAdd()
	{
		$model = new ArticleType();
		// 有提交则装载post值并验证
		if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
			$model->is_builtin = 0;
			$model->create_user = 'yakehuang';
			if ($model->save()) {
				return $this->redirectMessage('添加成功', self::MSG_SUCCESS, Url::toRoute('article-type/list'));
			} else {
				return $this->redirectMessage('添加失败', self::MSG_ERROR);
			}
		}
		return $this->render('add', array(
			'model' => $model,
		));
	}
	
	public function actionEdit($id)
	{
		$id = intval($id);
		$model = $this->findModel($id);
		
		if ($model->is_builtin) {
			return $this->redirectMessage('内置类型不能编辑', self::MSG_ERROR);
		}
		
		// 有提交则装载post值并验证
		if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
			if ($model->save()) {
				return $this->redirectMessage('编辑成功', self::MSG_SUCCESS, Url::toRoute('article-type/list'));
			} else {
				return $this->redirectMessage('编辑失败', self::MSG_ERROR);
			}
		}
		return $this->render('edit', array(
			'model'		=> $model,
		));
	}
	
	/**
	 * 删除栏目时该栏目对应的文章将归为默认类别
	 */
	public function actionDelete($id)
	{
		$model = $this->findModel(intval($id));
		if (!$model->is_builtin) {
			if ($model->delete()) {
				$defaultTypeModel = ArticleType::findOne(['name' => ArticleType::TYPE_DEFAULT]);
				if ($defaultTypeModel) {
					Article::updateAll(['type_id' => $defaultTypeModel->id], 'type_id = ' . $model->id);
				}
				return $this->redirect(['article-type/list']);
			} else {
				return $this->redirectMessage('删除失败', self::MSG_ERROR);
			}
		} else {
			return $this->redirectMessage('内置类型不能删除', self::MSG_ERROR);
		}
	}
	
	protected function findModel($id)
	{
		if (($model = ArticleType::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}