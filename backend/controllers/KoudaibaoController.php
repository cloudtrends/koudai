<?php
namespace backend\controllers;

use yii\data\Pagination;
use yii\helpers\Url;
use backend\controllers\BaseController;
use common\models\KdbInfo;
use common\models\KdbInvest;
use common\models\KdbAccount;
use common\models\KdbRolloutLog;

/**
 * Koudaibao controller
 */
class KoudaibaoController extends BaseController
{
	/**
	 * 全局配置
	 */
	public function actionSetting()
	{
		$model = KdbInfo::findKoudai();
		$model || $model = new KdbInfo();
		// 展示的时候金额需要换算成元
		$model->total_money = $model->total_money / 100;
		$model->daily_invest_limit = $model->daily_invest_limit / 100;
		$model->daily_withdraw_limit = $model->daily_withdraw_limit / 100;
		$model->user_invest_limit = $model->user_invest_limit / 100;
		$model->min_invest_money = $model->min_invest_money / 100;
		
		if ($model->load($this->request->post()) && $model->validate()) {
			$model->total_money = $model->total_money * 100;
			$model->daily_invest_limit = $model->daily_invest_limit * 100;
			$model->daily_withdraw_limit = $model->daily_withdraw_limit * 100;
			$model->user_invest_limit = $model->user_invest_limit * 100;
			$model->min_invest_money = $model->min_invest_money * 100;
			$addMoney = $model->getIsNewRecord() ? $model->total_money : ($model->total_money - $model->getOldAttribute('total_money'));
			if ($model->save()) {
				if (!$model->getCurAccount()) {
					// 创建口袋宝资金信息记录
					$account = new KdbAccount();
					$account->is_current = 1;
					$account->cur_money = $model->total_money;
					$account->history_money = 0;
					$account->history_invest_times = 0;
					$account->history_profits_money = 0;
					$account->today_money = 0;
					$account->today_invest_times = 0;
					$account->today_profits_money = 0;
					$account->save();
				} else {
					$account = $model->getCurAccount();
					$account->cur_money += $addMoney;
					$account->save();
				}
				return $this->redirectMessage('保存成功', self::MSG_SUCCESS, Url::toRoute('koudaibao/setting'));
			} else {
				return $this->redirectMessage('保存失败', self::MSG_ERROR, Url::toRoute('koudaibao/setting'));
			}
		}
		
		return $this->render('setting', [
			'model' => $model,
		]);
	}
	
	/**
	 * 投资记录列表
	 */
	public function actionInvests()
	{
		$query = KdbInvest::find()->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$invests = $query->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('invests', [
			'invests' => $invests,
			'pages' => $pages,
		]);
	}
	
	/**
	 * 转出记录列表
	 */
	public function actionRollouts()
	{
		$query = KdbRolloutLog::find()->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$rollouts = $query->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('rollouts', [
			'rollouts' => $rollouts,
			'pages' => $pages,
		]);
	}
	
	/**
	 * 资金统计
	 */
	public function actionStat()
	{
		$query = KdbAccount::find()->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$accounts = $query->offset($pages->offset)->limit($pages->limit)->all();
		return $this->render('stat', [
			'accounts' => $accounts,
			'pages' => $pages,
		]);
	}
}