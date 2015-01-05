<?php

namespace console\controllers;

use Yii;
use yii\db\Query;
use common\models\FinancialAccount;
use common\models\Financial;
use common\models\Project;
use common\models\KdbInfo;
use common\models\KdbAccount;
use common\models\UserAccount;
use common\models\UserDailyProfits;
use common\models\UserPayOrder;

class FinancialAccountController extends BaseController
{

	/**
	*后台财务每日对账记录表 每日凌晨3点跑
	*
	*/
	public function actionStat()
	{
		$model = new FinancialAccount();
		if (date('Y-m-d', $model->date) == date('Y-m-d')) {
			$this->error("date already stat.", false);
			return self::EXIT_CODE_ERROR;
		}
		
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {
				$countlist = [];
				$projects_total_money = [];
				$usable_money = [];
				$kdbaccounts = KdbAccount::findCurrent();
				$kdbinfos = KdbInfo::findKoudai();
				//用户资金账户
				$useraccounts = UserAccount::find()->orderBy('id desc')->asArray()->all();
				//项目信息
				$projects = Project::find()->where(['status'=>[Project::STATUS_PUBLISHED,Project::STATUS_REPAYING]])->orderBy('id desc')->asArray()->all();
				foreach ($useraccounts as $v) {
					$usable_money[$v['id']] = $v['usable_money'];
				}
				foreach ($projects as $v) {
					$projects_total_money[$v['id']] = $v['success_money'];
				}
				$kdb_total_money = $kdbinfos->total_money - $kdbaccounts->cur_money;//当前网站口袋宝总额
				$projects_total_money = array_sum($projects_total_money);//当前网站所有正在进行中项目总额
				$usable_money = array_sum($usable_money);//用户总余额
				$payService = Yii::$container->get('payService');//第三方账户,在本地测试无效
				$model->date = time();
				$model->kdb_total_money = intval($kdb_total_money);
				$model->projects_total_money = intval($projects_total_money);
				$model->usable_money = intval($usable_money);
				$model->site_total_money = intval($kdb_total_money+$projects_total_money+$usable_money);
				$model->merchant_number_money = intval($payService->getRemainMoney()+0+0);
				$model->to_total_revenue = 0;
				$model->third_party_alipay_balance = $payService->getRemainMoney();
				$model->to_total_repayment = 0;
				$model->historical_platform_profit = 0;
				$model->profit = 0;
				if ($model->validate()) {
					$model->save();
				}
			$transaction->commit();
		} catch (\Exception $e) {
			$transaction->rollBack();
		}
	}

	/**
	*后台财务收益更新 每日凌晨2点3刻跑
	*
	*/
	public function actionUpdate()
	{
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {
		//用户资金账户
		$financials = Financial::find()->orderBy('id desc')->asArray()->all();
		$financialList = [];
		foreach ($financials as $v) {
			$financialList['id'] =  $v['id'];
			$financialList['platform_revenue'] =  $v['total_amount_financing'] * $v['borrower_rate']/100/365;
			$financialList['investor_revenue'] =  $v['total_amount_financing'] * $v['user_rate']/100/365;
			$financialList['total_revenue'] =  $v['total_amount_financing'] * ($v['borrower_rate'] - $v['user_rate'])/100/365;
			$model = Financial::findOne($financialList['id']);
			if($model->project_type == Financial::TYPE_CURRENT && $model->status== Financial::STATUS_REPAYMENT){
				$model->platform_revenue = floor((strtotime(date('Y-m-d',time()))-strtotime($model->loan_time)) / (24 * 3600)) * $financialList['platform_revenue'];
				$model->investor_revenue = floor((strtotime(date('Y-m-d',time()))-strtotime($model->loan_time)) / (24 * 3600)) * $financialList['investor_revenue'];
				$model->total_revenue = floor((strtotime(date('Y-m-d',time()))-strtotime($model->loan_time)) / (24 * 3600)) * $financialList['total_revenue'];
				$model->save();
			}
		}
			$transaction->commit();
		} catch (\Exception $e) {
			$transaction->rollBack();
		}
	}

}
