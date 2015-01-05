<?php
namespace website\controllers;
use common\models\Project;
use yii\db\Query;
use yii\data\Pagination;
use Yii;

class SiteController extends BaseController
{
	//首页
	public function actionIndex()
	{
		$title = '口袋理财-首页';
		return  $this->render('index', [
			'title' => $title,
		]);
	}

	//登录页
	public function actionLogin()
	{
		$title = '口袋理财-登录';
		return  $this->render('login', [
			'title' => $title,
		]);
	}

	//手机号输入页面
	//ValidCode邀请编码
	public function actionRegister()
	{
		$title = '口袋理财-注册';
		return $this->render('register', [
			'title' => $title,
		]);
	}

		
	//列表页
		public function actionList()
	{
		$title = '口袋理财-列表页';
			 
		 $query = (new Query())->from(Project::tableName())->select([
			'id', 'name', 'status', 'total_money', 'success_money', 'success_number', 'is_novice', 'min_invest_money', 'period', 'is_day', 'apr','summary',
		])->where([
			'status' => [Project::STATUS_PUBLISHED, Project::STATUS_FULL, Project::STATUS_REPAYING, Project::STATUS_REPAYED],
		])->orderBy([
			'status' => SORT_ASC,
			'is_novice' => SORT_DESC,
			'id' => SORT_DESC,
		]);
				$countQuery = clone $query;
				$pages = new Pagination(['totalCount' => $countQuery->count()]);
				$pages->pageSize = 9;
				$projects = $query->offset($pages->offset)->limit($pages->limit)->all();
	//var_dump($projects);exit;
		return  $this->render('list', [
			'title' => $title,
						'projects' => $projects,
						'pages' => $pages,	
					 
		]);
	}
}