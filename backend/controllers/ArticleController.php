<?php
namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use backend\controllers\BaseController;
use common\models\Article;
use common\models\ArticleType;

/**
 * Article controller
 */
class ArticleController extends BaseController
{
	/**
	 * 文章列表
	 */
	public function actionList()
	{
		if ($this->request->post('list_submit')) { // 批量编辑排序
			$orders = $this->request->post('orders', array());
			foreach ($orders as $id => $order) {
				Article::updateAll(['order' => intval($order)], "id = " . intval($id));
			}
		}
		
		$search = array();
		$condition = '1=1';
		if ($this->request->get('search_submit')) { // 过滤
			$search = $this->request->get();
			if ($search['keyword'] != '') {
				$condition .= " AND title LIKE '%" . trim($search['keyword']) . "%'";
			}
			if ($search['type'] != '') {
				$condition .= " AND type_id = " . intval($search['type']);
			}
		}
		
		$query = Article::find()->where($condition)->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$articles = $query->offset($pages->offset)->limit($pages->limit)->all();
		
		$articleTypes = ArticleType::findAllSelected();
		
		return $this->render('list', [
			'articles' => $articles,
			'pages' => $pages,
			'search' => $search,
			'articleTypes' => $articleTypes,
		]);
	}
	
	/**
	 * 添加文章
	 */
	public function actionAdd()
	{
		$model = new Article();
		// 有提交则装载post值并验证
		if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
			$model->create_user = Yii::$app->user->identity->username;
			if ($model->save()) {
				return $this->redirectMessage('添加成功', self::MSG_SUCCESS, Url::toRoute('article/list'));
			} else {
				return $this->redirectMessage('添加失败', self::MSG_ERROR);
			}
		}
		// 文章栏目
		$articleTypes = ArticleType::find()->orderBy('is_builtin desc')->asArray()->all();
		$articleTypeItems = array();
		foreach ($articleTypes as $v) {
			$articleTypeItems[$v['id']] = $v['title'];
		}
		return $this->render('add', array(
			'model' => $model,
			'articleTypeItems' => $articleTypeItems,
		));
	}
	
	/**
	 * 编辑文章
	 */
	public function actionEdit($id)
	{
		$id = intval($id);
		$model = $this->findModel($id);
		// 有提交则装载post值并验证
		if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
			if ($model->save()) {
				return $this->redirectMessage('编辑成功', self::MSG_SUCCESS, Url::toRoute('article/list'));
			} else {
				return $this->redirectMessage('编辑失败', self::MSG_ERROR);
			}
		}
		// 文章栏目
		$articleTypes = ArticleType::find()->orderBy('is_builtin desc')->asArray()->all();
		$articleTypeItems = array();
		foreach ($articleTypes as $v) {
			$articleTypeItems[$v['id']] = $v['title'];
		}
		return $this->render('edit', array(
			'model'		=> $model,
			'articleTypeItems' => $articleTypeItems,
		));
	}
	
	/**
	 * 删除文章
	 */
	public function actionDelete($id)
	{
		$this->findModel($id)->delete();
		return $this->redirect(['article/list']);
	}
	
	protected function findModel($id)
	{
		if (($model = Article::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}