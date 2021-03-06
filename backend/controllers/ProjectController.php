<?php
namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use common\models\Project;
use common\models\ProjectReviewLog;
use common\models\ProjectRepayment;
use common\models\Setting;
use common\services\ProjectService;
use common\models\ProjectInvest;
use common\models\NoticeSms;
use yii\db\Query;

/**
 * Project controller
 */
class ProjectController extends BaseController
{
	protected $projectService;
	
	public function __construct($id, $module, ProjectService $projectService, $config = [])
	{
		$this->projectService = $projectService;
		parent::__construct($id, $module, $config);
	}
	
	/**
	 * 首页项目配置
	 */
	public function actionSettingIndex()
	{
		$setting = Setting::findByKey('project_index');
		if ($setting) {
			$data = unserialize($setting->svalue);
		} else {
			$data = [
				['id' => '', 'title' => ''],
				['id' => '', 'title' => ''],
				['id' => '', 'title' => ''],
			];
		}
		
		if ($this->request->getIsPost()) {
			$setting = $this->request->post('setting');
			// 验证项目id是否存在和状态问题
			foreach ($setting as $v) {
				$id = intval($v['id']);
				if ($id != 0) {
					$project = Project::findOne($id);
					if (!$project || !in_array($project->status, [
                            Project::STATUS_PUBLISHED,
                            Project::STATUS_FULL,
                            Project::STATUS_REPAYING
                        ]))
                    {
						return $this->redirectMessage("保存失败，项目({$v['id']})所处状态不能设置为首页展示", self::MSG_ERROR, Url::toRoute('project/setting-index'));
					}
				}
			}
			$result = Setting::updateSetting('project_index', serialize($this->request->post('setting')));
			if ($result) {
				return $this->redirectMessage('保存成功', self::MSG_SUCCESS, Url::toRoute('project/setting-index'));
			} else {
				return $this->redirectMessage('保存失败', self::MSG_ERROR, Url::toRoute('project/setting-index'));
			}
		}
		
		return $this->render('setting-index', [
			'data' => $data
		]);
	}
	
	/**
	 * 所有项目列表
	 */
	public function actionList()
	{
		$condition = $this->getFilterCondition();
		$query = Project::find()->where($condition)->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$projects = $query->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('list', [
			'projects' => $projects,
			'pages' => $pages,
			'type' => 'all',
		]);
	}
	
	/**
	 * 新项目列表
	 */
	public function actionNewList()
	{
		$condition = $this->getFilterCondition();
		$query = Project::find()->where(['status' => [Project::STATUS_NEW, Project::STATUS_NEW_CANCEL]])
								->andWhere($condition)->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$projects = $query->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('list', [
			'projects' => $projects,
			'pages' => $pages,
			'type' => 'review-new',
		]);
	}
	
	/**
	 * 投资中项目列表
	 */
	public function actionInvestingList()
	{
		$condition = $this->getFilterCondition();
		$query = Project::find()->where(['status' => [Project::STATUS_PUBLISHED]])
		->andWhere($condition)->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$projects = $query->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('list', [
			'projects' => $projects,
			'pages' => $pages,
			'type' => 'investing',
		]);
	}
	
	/**
	 * 满款项目列表
	 */
	public function actionFullList()
	{
		$condition = $this->getFilterCondition();
		$query = Project::find()->where(['status' => [Project::STATUS_FULL, Project::STATUS_FULL_CANCEL]])
								->andWhere($condition)->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$projects = $query->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('list', [
			'projects' => $projects,
			'pages' => $pages,
			'type' => 'review-full',
		]);
	}
	
	/**
	 * 还款管理列表
	 */
	public function actionRepayList()
	{
		$condition = $this->getFilterCondition();
		$query = Project::find()->where(['status' => [Project::STATUS_REPAYING, Project::STATUS_REPAYED]])
								->andWhere($condition)->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$projects = $query->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('list', [
			'projects' => $projects,
			'pages' => $pages,
			'type' => 'repay-list',
		]);
	}
	
	/**
	 * 审核新项目
	 */
	public function actionReviewNew($id)
	{
		$model = $this->findModel(intval($id));
		if ($model->status != Project::STATUS_NEW) {
			return $this->redirectMessage('不是新项目', self::MSG_ERROR);
		}
		
		if ($this->request->getIsPost()) {
			$operation = $this->request->post('operation');
			$remark = $this->request->post('remark');
			
			try {
				$log = new ProjectReviewLog();
				$log->username = Yii::$app->user->identity->username;
				$log->pre_status = $model->status;
				$log->remark = $remark;
				$log->created_at = time();
				$log->project_id = $model->id;

				if ($operation == '1') {
					$model->status = Project::STATUS_PUBLISHED;
					$model->publish_username = Yii::$app->user->identity->username;
					$model->publish_at = time();
					$model->save();
					$log->cur_status = $model->status;
					$log->save();
				} else if ($operation == '2') {
					$model->status = Project::STATUS_NEW_CANCEL;
					$model->save();
					$log->cur_status = $model->status;
					$log->save();
				}
				return $this->redirectMessage('操作成功', self::MSG_SUCCESS, Url::toRoute('project/new-list'));
			} catch (\Exception $e) {
				return $this->redirectMessage('操作出现异常：' . $e->getMessage(), self::MSG_ERROR);
			}
		}
		
		return $this->render('review-new', [
			'model' => $model,
		]);
	}
	
	/**
	 * 投资中未满款项目作废
	 */
	public function actionCancle($id)
	{
		$model = $this->findModel(intval($id));
		if ($model->status != Project::STATUS_PUBLISHED) {
			return $this->redirectMessage('不是投资中项目', self::MSG_ERROR);
		}
		
		if ($this->request->getIsPost()) {
			$remark = $this->request->post('remark');
			try {
				$this->projectService->noFullCancle($model, $remark);
				return $this->redirectMessage('操作成功', self::MSG_SUCCESS, Url::toRoute('project/investing-list'));
			} catch (\Exception $e) {
				return $this->redirectMessage('操作出现异常：' . $e->getMessage(), self::MSG_ERROR);
			}
		}
		
		return $this->render('cancle', [
			'model' => $model,
		]);
	}

	/**
	 * 审核满款项目
	 */
	public function actionReviewFull($id)
	{
		$model = $this->findModel(intval($id));
		if ($model->status != Project::STATUS_FULL) {
			return $this->redirectMessage('不是满款项目', self::MSG_ERROR);
		}
	
		if ($this->request->getIsPost()) {
			$operation = $this->request->post('operation');
			$remark = $this->request->post('remark');
			//try {
				if ($operation == '1') { // 满款通过
					$this->projectService->fullAdopt($model, $remark);
				} else if ($operation == '2') { // 满款作废
					$this->projectService->fullCancle($model, $remark);

                    /**  记录NoticeSms 满款审核作废 JohnnyLin */
                    $userArr = (new Query())->select(['user_id'])->from(ProjectInvest::tableName())->where(['project_id'=>$model->id])->column();
                    if (!empty($userArr)){
                        $userArr = array_unique($userArr);
                        foreach($userArr as $userArrKey => $userArrVal){
                            NoticeSms::instance()->init_sms_str($userArrVal,NoticeSms::NOTICE_FULL_FAIL,array('project_name'=>$model->name));
                        }
                    }
				}
				return $this->redirectMessage('操作成功', self::MSG_SUCCESS, Url::toRoute('project/full-list'));
			//} catch (\Exception $e) {
			//	return $this->redirectMessage('操作出现异常：' . $e->getMessage(), self::MSG_ERROR);
			//}
		}
	
		return $this->render('review-full', [
			'model' => $model,
		]);
	}
	
	/**
	 * 新建项目
	 */
	public function actionCreate()
	{
		$model = new Project();
		$model->is_day = 0;
		$model->is_novice = 0;
		$model->interest_date = '募集成功次日开始计息';
		$model->repay_date = '到期后两个工作日内处理';
		$model->desc = $this->renderPartial('/template_detail.html');
		
		if ($model->load($this->request->post()) && $model->validate()) {
			$model->total_money = $model->total_money * 100;
			$model->min_invest_money = $model->min_invest_money * 100;
			$model->status = Project::STATUS_NEW;
			$model->created_username = Yii::$app->user->identity->username;
			if ($model->save()) {
				return $this->redirectMessage('新建项目成功', self::MSG_SUCCESS, Url::toRoute('project/list'));
			} else {
				return $this->redirectMessage('新建项目失败', self::MSG_ERROR);
			}
		}
		
		return $this->render('create', [
			'model' => $model,
		]);
	}
	
	/**
	 * 编辑项目
	 */
	public function actionEdit($id)
	{
		$model = $this->findModel(intval($id));
// 		if ($model->status != Project::STATUS_NEW) {
// 			return $this->redirectMessage('只有未审核项目才可以编辑', self::MSG_ERROR);
// 		}
		$model->total_money = $model->total_money / 100;
		$model->min_invest_money = $model->min_invest_money / 100;
		
		if ($model->load($this->request->post()) && $model->validate()) {
			$model->total_money = $model->total_money * 100;
			$model->min_invest_money = $model->min_invest_money * 100;
			if ($model->save()) {
				return $this->redirect(['edit', 'id' => $model->id]);
// 				return $this->redirectMessage('编辑项目成功', self::MSG_SUCCESS, Url::toRoute('project/list'));
			} else {
				return $this->redirectMessage('编辑项目失败', self::MSG_ERROR);
			}
		}
		
		return $this->render('edit', [
			'model' => $model,
		]);
	}
	
	/**
	 * 查看详情
	 * 包括项目信息、投资信息、状态流转信息
	 */
	public function actionView($id)
	{
		$model = $this->findModel(intval($id));
		
		return $this->render('view', [
			'model' => $model,
		]);
	}
	
	/**
	 * 还款
	 */
	public function actionRepay($id)
	{
		$model = $this->findModel(intval($id));
		if ($model->status != Project::STATUS_REPAYING) {
			return $this->redirectMessage('不是还款中项目', self::MSG_ERROR);
		}
		
		if ($this->request->getIsPost() && date('Y-m-d') < $model->getRepayDate()) {
			return $this->redirectMessage('还没到期不能还款', self::MSG_ERROR);
		}
		$repayment = new ProjectRepayment();
		if ($repayment->load($this->request->post()) && $repayment->validate()) {
			$repayment->project_id = $model->id;
			$repayment->platform_repay_time = time();
			$repayment->oporate_username = Yii::$app->user->identity->username;
			$repayment->repay_money = $model->getProfits() + $model->total_money;
			$repayment->capital_money = $model->total_money;
			$repayment->interest_money = $model->getProfits();
			$repayment->loaner_repay_money = $repayment->loaner_repay_money * 100;
			$repayment->loaner_repay_time = $repayment->loaner_repay_time ? $repayment->loaner_repay_time : '0000-00-00';
			
			if ($repayment->save()) {
				try {
					$remark = $this->request->post('remark', '');
					$this->projectService->repay($model, $remark);
					return $this->redirectMessage('还款成功', self::MSG_SUCCESS, Url::toRoute('project/repay-list'));
				} catch (\Exception $e) {
					return $this->redirectMessage('还款失败：' . $e->getMessage(), self::MSG_ERROR);
				}
			} else {
				return $this->redirectMessage('还款失败', self::MSG_ERROR);
			}
		}
		
		return $this->render('repay', [
			'model' => $model,
			'repayment' => $repayment,
		]);
	}
	
	/**
	 * 更新还款记录
	 */
	public function actionEditRepayment($id)
	{
		$project = $this->findModel(intval($id));
		
		$model = ProjectRepayment::findOne(['project_id' => intval($id)]);
		$model->loaner_repay_money = $model->loaner_repay_money / 100;
		$model->overdue_money = $model->overdue_money / 100;
		
		if ($model->load($this->request->post()) && $model->validate()) {
			$model->loaner_repay_money = $model->loaner_repay_money * 100;
			$model->overdue_money = $model->overdue_money * 100;
			if ($model->save()) {
				return $this->redirectMessage('更新成功', self::MSG_SUCCESS, Url::toRoute('project/repay-list'));
			} else {
				return $this->redirectMessage('更新失败', self::MSG_ERROR);
			}
		}
		
		return $this->render('edit-repayment', [
			'model' => $model,
			'project' => $project
		]);
	}
	
	protected function getFilterCondition()
	{
		$search = array();
		$condition = '1=1';
		if ($this->request->get('search_submit')) {
			$search = $this->request->get();
			if ($search['id'] != '') {
				$condition .= " AND id = " . intval($search['id']);
			}
			if ($search['name'] != '') {
				$condition .= " AND name LIKE '%" . trim($search['name']) . "%'";
			}
			if ($search['status'] != '') {
				$condition .= " AND status = " . intval($search['status']);
			}
		}
		return $condition;
	}
	
	protected function findModel($id)
	{
		if (($model = Project::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}
