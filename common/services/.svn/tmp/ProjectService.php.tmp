<?php

namespace common\services;

use common\helpers\TimeHelper;
use common\models\CreditBaseInfo;
use Yii;
use yii\db\Query;
use yii\base\Object;
use yii\base\UserException;
use common\exceptions\ProjectException;
use common\models\Project;
use common\models\ProjectInvest;
use common\models\ProjectReviewLog;
use common\models\ProjectProfits;
use common\models\KdbInvest;
use common\models\KdbAccount;
use common\models\KdbRolloutLog;
use common\models\UserAccount;
use common\models\UserDailyProfits;
use common\models\NoticeSms;

/**
 * 项目模块service
 */
class ProjectService extends Object
{
	/**
	 * 口袋宝投资
	 * 
	 * @param integer $money 投资总额
	 * @param integer $pay_money 银行卡支付金额
	 */
	public function investKdb($money, $invest_pay_money)
	{
		// 使用余额投资的金额
		$invest_usable_money = $money - $invest_pay_money;
		
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {
			$curUser = Yii::$app->user->identity;
			$time = time();
			
			// 添加投资记录
			$rows = $db->createCommand()->insert(KdbInvest::tableName(), [
				'user_id' => $curUser->id,
				'username' => $curUser->username,
				'status' => KdbInvest::STATUS_SUCCESS,
				'invest_money' => $money,
				'created_at' => $time,
				'updated_at' => $time,
				'created_ip' => Yii::$app->getRequest()->getUserIP(),
				'source' => Yii::$app->getRequest()->getClient()->clientType,
			])->execute();
			if (!$rows) {
				throw new ProjectException('添加口袋宝投资记录失败');
			}
			// 更新口袋宝资金统计
			$sql = "update " . KdbAccount::tableName() . "
					set history_money = history_money + {$money}, history_invest_times = history_invest_times + 1,
						today_money = today_money + {$money}, today_invest_times = today_invest_times + 1,
						cur_money = cur_money - {$money}, updated_at = {$time}
					where is_current = 1";
			$rows = $db->createCommand($sql)->execute();
			if (!$rows) {
				throw new ProjectException('更新口袋宝资金失败');
			}
			// 更新账户资金
			UserAccount::updateAccount($curUser->id, [
				['total_money', '+', $invest_pay_money],
				['kdb_total_money', '+', $money],
				['usable_money', '-', $invest_usable_money],
			]);
			
			// 银行卡扣款
			if ($invest_pay_money > 0) {
				$payService = Yii::$container->get('payService');
				$ret = $payService->pay($curUser,$invest_pay_money, Yii::$app->getRequest()->client->clientType);
				if ($ret['code'] != '0') {
					throw new UserException("银行卡支付失败：{$ret['message']}({$ret['code']})", $ret['code']);
				}
			}
			
			// 添加资金流水，余额投资和银行卡投资分开记
			if ($invest_usable_money) {
				UserAccount::addLog($curUser->id, UserAccount::TRADE_TYPE_INVEST_KDB, $invest_usable_money);
			}
			if ($invest_pay_money) {
				UserAccount::addLog($curUser->id, UserAccount::TRADE_TYPE_INVEST_KDB_BY_CARD, $invest_pay_money);
			}
			
			$transaction->commit();
			return true;
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
	
	/**
	 * 口袋宝转出
	 */
	public function rollout($money)
	{
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {
			$curUser = Yii::$app->user->identity;
			$time = time();
			
			// 添加转出记录
			$rows = $db->createCommand()->insert(KdbRolloutLog::tableName(), [
				'user_id' => $curUser->id,
				'username' => $curUser->username,
				'money' => $money,
				'created_at' => $time,
			])->execute();
			if (!$rows) {
				throw new ProjectException('添加转出记录失败');
			}
			// 增加口袋宝剩余可投金额
			$sql = "update " . KdbAccount::tableName() . "
					set today_rollout_money = today_rollout_money + {$money},
						cur_money = cur_money + {$money}, updated_at = {$time}
					where is_current = 1";
			$rows = $db->createCommand($sql)->execute();
			if (!$rows) {
				throw new ProjectException('修改口袋宝剩余可投金额失败');
			}
			// 更新账户资金
			UserAccount::updateAccount($curUser->id, [
				['kdb_total_money', '-', $money],
				['usable_money', '+', $money],
			]);
			// 添加资金流水
			UserAccount::addLog($curUser->id, UserAccount::TRADE_TYPE_ROLLOUT, $money);
			$transaction->commit();
			return [
				'money' => $money,
				'created_at' => time(),
			];
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
	
	/**
	 * 通用项目投资
	 * 
	 * @param Project $project
	 * @param integer $money
	 * @param integer $invest_pay_money
	 * @throws UserException
	 * @throws Exception
	 */
	public function investProject( Project $project, $money, $invest_pay_money)
	{
		// 使用余额投资的金额
		$invest_usable_money = $money - $invest_pay_money;
		
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {

			$curUser = Yii::$app->user->identity;
			
			// 添加投资记录
			$rows = $db->createCommand()->insert(ProjectInvest::tableName(), [
				'project_id' => $project->id,
				'project_name' => $project->name,
				'user_id' => $curUser->id,
				'username' => $curUser->username,
				'status' => ProjectInvest::STATUS_PENDING,
				'invest_money' => $money,
				'created_at' => time(),
				'updated_at' => time(),
				'created_ip' => Yii::$app->getRequest()->getUserIP(),
				'type' => $project->type,
				'is_statistics' => 0,
			])->execute();
			if (!$rows) {
				throw new ProjectException('添加投资记录失败');
			}
			// 更新用户新手问题
			if ($curUser->is_novice) {
				$curUser->is_novice = 0;
				$curUser->save();
			}

            //$invest_id = $db->getLastInsertID();
			// 更新项目成功金额
			$sql = "update " . Project::tableName() . "
					set success_money = success_money + {$money}, success_number = success_number + 1
					where id = {$project->id}";
			$rows = $db->createCommand($sql)->execute();
			if (!$rows) {
				throw new ProjectException('更新项目成功金额失败');
			}
			// 如果满款则更新状态
			$sql = "update " . Project::tableName() . "
					set status = " . Project::STATUS_FULL . "
					where success_money = total_money and id = {$project->id}";
			$db->createCommand($sql)->execute();

			// 更新账户资金
			UserAccount::updateAccount($curUser->id, [
				['total_money', '+', $invest_pay_money],
				['investing_money', '+', $money],
				['usable_money', '-', $invest_usable_money],
			]);
			
			// 银行卡扣款
			if ($invest_pay_money > 0) {
				$payService = Yii::$container->get('payService');
				$ret = $payService->pay($curUser, $invest_pay_money, Yii::$app->getRequest()->client->clientType);
				if ($ret['code'] != '0') {
					throw new UserException("银行卡支付失败：{$ret['message']}({$ret['code']})", $ret['code']);
				}
			}
			
			// 添加资金流水，余额投资和银行卡投资分开记
			if ($invest_usable_money) {
				UserAccount::addLog($curUser->id, UserAccount::TRADE_TYPE_INVEST_PROJ, $invest_usable_money);
			}
			if ($invest_pay_money) {
				UserAccount::addLog($curUser->id, UserAccount::TRADE_TYPE_INVEST_PROJ_BY_CARD, $invest_pay_money);
			}
			
			$transaction->commit();
			return true;
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
	
	/**
	 * 未满款作废
	 * @param Project $project
	 * @param string $remark
	 */
	public function noFullCancle(Project $model, $remark)
	{
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {
			$log = new ProjectReviewLog();
			$log->username = Yii::$app->user->identity->username;
			$log->pre_status = $model->status;
			$log->remark = $remark;
			$log->created_at = time();
			$log->project_id = $model->id;
			$log->cur_status = Project::STATUS_PUBLISHED_CANCEL;
			if (!$log->save()) {
				throw new \Exception('保存审核日志失败');
			}
			
			$model->status = Project::STATUS_PUBLISHED_CANCEL;
			if (!$model->save()) {
				throw new \Exception('修改项目状态失败');
			}

            /**  记录NoticeSms 未滿款 JohnnyLin */
            $userArr = (new Query())->select(['user_id'])->from(ProjectInvest::tableName())->where(['project_id'=>$model->id])->column();
            if (!empty($userArr)){
                $userArr = array_unique($userArr);
                foreach($userArr as $userArrKey => $userArrVal){
                    NoticeSms::instance()->init_sms_str($userArrVal,NoticeSms::NOTICE_CANCEL,array('project_name'=>$model->name));
                }
            }
			
			// 返还用户投资冻结金额
			foreach ($model->invests as $invest) {
				// 只返还状态为申购中的记录，避免后面要做单独对投资记录的作废
				if ($invest->status == ProjectInvest::STATUS_PENDING) {
					UserAccount::updateAccount($invest->user_id, [
						['investing_money', '-', $invest->invest_money],
						['usable_money', '+', $invest->invest_money],
					]);
					UserAccount::addLog($invest->user_id, UserAccount::TRADE_TYPE_NOFULL_CANCLE, $invest->invest_money);
				}
			}
			
			// 更改项目所有投资记录状态
			ProjectInvest::updateAll(
				['status' => ProjectInvest::STATUS_CANCELED],
				['project_id' => $model->id]
			);
			
			$transaction->commit();
			return true;
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
	
	/**
	 * 满款通过
	 * @param Project $project
	 * @param string $remark
	 */
	public function fullAdopt(Project $model, $remark)
	{
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {
			$log = new ProjectReviewLog();
			$log->username = Yii::$app->user->identity->username;
			$log->pre_status = $model->status;
			$log->remark = $remark;
			$log->created_at = time();
			$log->project_id = $model->id;

			$model->status = Project::STATUS_REPAYING;
			$model->review_username = Yii::$app->user->identity->username;

            $interest_start_time = strtotime("+1 day");
			$model->review_at = $interest_start_time - TimeHelper::DAY;

			if (!$model->save()) {
				throw new \Exception('修改项目状态失败');
			}
			
			$log->cur_status = $model->status;
			if (!$log->save()) {
				throw new \Exception('保存审核日志失败');
			}

            $last_repay_date = $model->is_day
                ? date("Y-m-d", strtotime("+{$model->period} day",$interest_start_time + 24 * 3600))
                : date("Y-m-d", strtotime("+{$model->period} month", $interest_start_time + 24 * 3600));
			
			// 插入投资对应的收益记录
			$profits = $columns = [];
			foreach ($model->invests as $invest) {
				$duein_profits = $invest->getDueinProfits($model);

				$p = [
					'invest_id' => $invest->id,
					'project_id' => $invest->project_id,
					'project_name' => $model->name,
					'project_apr' => $model->apr,
					'invest_uid' => $invest->user_id,
					'is_transfer' => 0,
					'profits_uid' => $invest->user_id,
					'duein_money' => $invest->invest_money + $duein_profits,
					'duein_capital' => $invest->invest_money,
					'duein_profits' => $duein_profits,
					'interest_start_date' => date("Y-m-d", $interest_start_time),
					'last_repay_date' => $last_repay_date,
					'status' => ProjectProfits::STATUS_SUCCESS,
					'created_at' => time(),
					'updated_at' => time(),
				];
				$profits[] = array_values($p);
				$columns || $columns = array_keys($p);
			}

			$affectedRows = Yii::$app->db->createCommand()->batchInsert(ProjectProfits::tableName(), $columns, $profits)->execute();
			if ($affectedRows != count($profits)) {
				throw new \Exception('插入投资收益失败');
			}

            /**  记录NoticeSms 满款审核通过 JohnnyLin */
            $userArr = (new Query())->select(['user_id'])->from(ProjectInvest::tableName())->where(['project_id'=>$model->id])->column();
            if (!empty($userArr)){
                $userArr = array_unique($userArr);
                foreach($userArr as $userArrKey => $userArrVal){
                    NoticeSms::instance()->init_sms_str($userArrVal,NoticeSms::NOTICE_FULL,array('project_name'=>$model->name,'interest_start_date'=>date("Y-m-d", $interest_start_time),'last_repay_date'=>$last_repay_date));
                }
            }
			
			// 增加投资待收收益
			foreach ($model->invests as $invest) {
				if ($invest->status == ProjectInvest::STATUS_PENDING) {
					$duein_profits = $invest->getDueinProfits($model);
					UserAccount::updateAccount($invest->user_id, [
						['investing_money', '-', $invest->invest_money],
						['duein_capital', '+', $invest->invest_money],
						['duein_profits', '+', $duein_profits],
						['total_money', '+', $duein_profits]
					]);
					UserAccount::addLog($invest->user_id, UserAccount::TRADE_TYPE_FULL_SUCCESS, $duein_profits);
				}
			}
			
			// 更新投资记录的状态
			ProjectInvest::updateAll(
				['status' => ProjectInvest::STATUS_SUCCESS],
				['project_id' => $model->id, 'status' => ProjectInvest::STATUS_PENDING]
			);

			$transaction->commit();
			return true;
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
	
	/**
	 * 满款作废
	 * @param Project $project
	 * @param string $remark
	 */
	public function fullCancle(Project $model, $remark)
	{
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {
			$log = new ProjectReviewLog();
			$log->username = Yii::$app->user->identity->username;
			$log->pre_status = $model->status;
			$log->remark = $remark;
			$log->created_at = time();
			$log->project_id = $model->id;
			
			$model->status = Project::STATUS_FULL_CANCEL;
			if (!$model->save()) {
				throw new \Exception('修改项目状态失败');
			}
			
			$log->cur_status = $model->status;
			if (!$log->save()) {
				throw new \Exception('保存审核日志失败');
			}
			
			// 返还用户投资冻结金额
			foreach ($model->invests as $invest) {
				if ($invest->status == ProjectInvest::STATUS_PENDING) {
					UserAccount::updateAccount($invest->user_id, [
						['investing_money', '-', $invest->invest_money],
						['usable_money', '+', $invest->invest_money],
					]);
					UserAccount::addLog($invest->user_id, UserAccount::TRADE_TYPE_FULL_CANCLE, $invest->invest_money);
				}
			}
			
			// 更新投资记录的状态
			ProjectInvest::updateAll(
				['status' => ProjectInvest::STATUS_CANCELED],
				['project_id' => $model->id, 'status' => ProjectInvest::STATUS_PENDING]
			);
			
			$transaction->commit();
			return true;
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
	
	/**
	 * 还款
	 * @param Project $project
	 * @param string $remark
	 */
	public function repay(Project $model, $remark)
	{
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {
			$log = new ProjectReviewLog();
			$log->username = Yii::$app->user->identity->username;
			$log->pre_status = $model->status;
			$log->remark = $remark;
			$log->created_at = time();
			$log->project_id = $model->id;
		
			$model->status = Project::STATUS_REPAYED;
			if (!$model->save()) {
				throw new \Exception('修改项目状态失败');
			}

            /**  记录NoticeSms 已还款 JohnnyLin */
            $userArr = (new Query())->select(['user_id','invest_money'])->from(ProjectInvest::tableName())->where(['project_id'=>$model->id,'status'=>array(ProjectInvest::STATUS_SUCCESS,ProjectInvest::STATUS_ASSIGNING)])->all();
            if (!empty($userArr)){
                foreach($userArr as $userArrKey => $userArrVal){
                    NoticeSms::instance()->init_sms_str($userArrVal['user_id'],NoticeSms::NOTICE_REPAYED,array('project_name'=>$model->name,'invest_money'=>$userArrVal['invest_money']));
                }
            }

		
			$log->cur_status = $model->status;
			if (!$log->save()) {
				throw new \Exception('保存审核日志失败');
			}
		
			// 更新用户资金：只需更新投资成功和转让中收益
			foreach ($model->profitses as $profits) {
				if ($profits->status == ProjectProfits::STATUS_SUCCESS || $profits->status == ProjectProfits::STATUS_ASSIGNING) {
					UserAccount::updateAccount($profits->profits_uid, [
						['duein_capital', '-', $profits->duein_capital],
						['duein_profits', '-', $profits->duein_profits],
						['usable_money', '+', $profits->duein_capital + $profits->duein_profits],
						['total_profits', '+', $profits->duein_profits],
					]);
					UserAccount::addLog($profits->profits_uid, UserAccount::TRADE_TYPE_REPAY, $profits->duein_capital + $profits->duein_profits);
					// 添加一条项目日收益
					$dailyProfits = new UserDailyProfits();
					$dailyProfits->date = date('Y-m-d');
					$dailyProfits->user_id = $profits->profits_uid;
					$dailyProfits->today_settle_money = $profits->duein_capital;
					$dailyProfits->lastday_profits = $profits->duein_profits;
					$dailyProfits->total_profits = $profits->duein_profits;
					$dailyProfits->project_type = UserDailyProfits::PROJECT_TYPE_PROJ;
					$dailyProfits->project_id = $profits->project_id;
					$dailyProfits->project_name = $profits->project_name;
					$dailyProfits->invest_id = $profits->invest_id;
					$dailyProfits->created_at = time();
					$dailyProfits->save();
				}
			}
			
			// 更新投资记录的状态
			ProjectInvest::updateAll(
				['status' => ProjectInvest::STATUS_REPAYED],
				['project_id' => $model->id, 'status' => [ProjectInvest::STATUS_SUCCESS, ProjectInvest::STATUS_ASSIGNING]]
			);
				
			// 更新投资收益的状态
			ProjectProfits::updateAll(
				['status' => ProjectProfits::STATUS_REPAYED],
				['project_id' => $model->id, 'status' => [ProjectProfits::STATUS_SUCCESS, ProjectProfits::STATUS_ASSIGNING]]
			);

            // 更新转让专区的状态
            CreditBaseInfo::updateAll(
                ['status' => CreditBaseInfo::STATUS_REPAYED],
                ['project_id' => $model->id, 'status' => [CreditBaseInfo::STATUS_ASSIGNING]]
            );

			$transaction->commit();
			return true;
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
}
