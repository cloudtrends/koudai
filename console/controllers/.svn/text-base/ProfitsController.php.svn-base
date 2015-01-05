<?php

namespace console\controllers;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\models\UserAccount;
use common\models\UserDailyProfits;
use common\models\KdbInfo;
use common\models\KdbInvest;
use common\models\KdbAccount;
use common\models\User;
use common\models\KdbRolloutLog;
use common\models\ProjectProfits;
use common\models\UserRedis;
use common\models\UserAccountLog;
use common\models\ProjectInvest;
use common\activity\ExperienceMoneyAct;

/**
 * 收益相关
 */
class ProfitsController extends BaseController
{
	/**
	 * 计算口袋宝每日收益和累计收益
	 *
	 * @param string $date 默认null为昨日，否则必须为2014-11-22这样的格式
	 */
	public function actionDailyKdb($date = null)
	{
		if ($date === null) {
			$date = date('Y-m-d', strtotime('-1 day'));
		} else if (!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $date)) {
			$this->error("date format error.", false);
			return self::EXIT_CODE_ERROR;
		}
		
		$page = 1;
		$pageSize = 500;
		$sleep = 2;
		$kdb = KdbInfo::findKoudai();
		
		while (true) {
			$offset = ($page - 1) * $pageSize;
			$users = (new Query())->from(User::tableName())->select([
				'id',
			])->offset($offset)->limit($pageSize)->all();
			
			foreach ($users as $user) {
				$uid = $user['id'];
				
				$model = UserDailyProfits::findOne(['user_id' => $uid, 'date' => $date, 'project_type' => UserDailyProfits::PROJECT_TYPE_KDB]);
				// 获得结算金额
				$settleMoney = $this->_getKdbSettleMoney($uid, $date);
				$profits = $kdb->calculateProfits($settleMoney);
				/**
				 * 资金变更金额
				 * 如果该日期没有计算过收益，则变更金额=收益金额
				 * 如果计算过，则变更金额=重新计算的收益-之前计算的收益
				 */
				$money = 0;
				if (!$model) {
					$money = $profits;
					
					$model = new UserDailyProfits();
					$model->today_settle_money = $settleMoney;
					$model->lastday_profits = $profits;
					$model->user_id = $uid;
					$model->project_type = UserDailyProfits::PROJECT_TYPE_KDB;
					$model->project_id = 0;
					$model->project_name = $kdb->title;
					$model->invest_id = 0;
					$model->date = $date;
					$model->created_at = time();
					$model->updated_at = time();
					// 该字段主要记录第一次跑的时候的历史累计收益，仅供参考
					$account = UserAccount::findOne(['user_id' => $uid]);
					$model->total_profits = $account->kdb_total_profits;
				} else {
					$money = $profits - $model->lastday_profits;
					
					$model->today_settle_money = $settleMoney;
					$model->lastday_profits = $profits;
					$model->updated_at = time();
				}
				// 只有结算金额和收益不为空才存储
				if ($model->lastday_profits > 0 || $model->today_settle_money > 0) {
					$transaction = Yii::$app->db->beginTransaction();
					try {
						if ($model->save()) {
							if ($money != 0) {
								UserAccount::updateAccount($uid, [
									['kdb_total_profits', '+', $money],
									['total_profits', '+', $money],
									['kdb_total_money', '+', $money],
									['total_money', '+', $money],
								], false);
								// 如果重复计算则有多条日志记录
								if ($model->lastday_profits > 0) {
									UserAccount::addLog($uid, UserAccount::TRADE_TYPE_KDB_DAILY_PROFITS, $model->lastday_profits);
								}
							}
						} else {
							throw new \Exception(array_shift($model->getFirstErrors()));
						}
						$transaction->commit();
					} catch (\Exception $e) {
						$transaction->rollBack();
						$this->error("save user:{$uid} data occurs error:{$e}");
					}
				}
				
				// 如果日期是昨日则更新昨日收益即可，就不添加流水了
				if (date('Y-m-d', strtotime('-1 day')) == $date) {
					UserAccount::updateAccount($uid, [
						['lastday_kdb_profits', '=', $model->lastday_profits],
					], false);
				}
			}
			
			$count = count($users);
			if ($count < $pageSize) {
				$this->message("finished page:{$page}, count:{$count}.");
				break;
			} else {
				$this->message("finished page:{$page}, count:{$count}, sleep:{$sleep}.");
				sleep($sleep);
				$page++;
			}
		}
	}
	
	/**
	 * 计算口袋宝每日收益和累计收益
	 * 注：此种老计算方式暂时弃用
	 * 
	 * @param string $date 默认null为昨日，否则必须为2014-11-22这样的格式
	 */
	public function actionDailyKdbOld($date = null)
	{
		if ($date === null) {
			$date = date('Y-m-d', strtotime('-1 day'));
		} else if (!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $date)) {
			$this->error("date format error.", false);
			return self::EXIT_CODE_ERROR;
		}
		
		$page = 1;
		$pageSize = 500;
		$sleep = 2;
		$kdb = KdbInfo::findKoudai();
		
		while (true) {
			$offset = ($page - 1) * $pageSize;
			$users = (new Query())->from(User::tableName())->select([
				'id',
			])->offset($offset)->limit($pageSize)->all();
			
			foreach ($users as $user) {
				$uid = $user['id'];
				// 计算过了不重复计算
				if (UserDailyProfits::findOne(['date' => $date, 'user_id' => $uid, 'project_type' => UserDailyProfits::PROJECT_TYPE_KDB])) {
					continue;
				}
				
				$preModel = UserDailyProfits::find()->where(
					"date < '{$date}' and user_id = {$uid} and project_type = " . UserDailyProfits::PROJECT_TYPE_KDB
				)->orderBy('date desc')->one();
				$model = new UserDailyProfits();
				
				// 昨日收益
				if ($preModel && $preModel->today_settle_money > 0) {
					$model->lastday_profits = $kdb->calculateProfits($preModel->today_settle_money);
				} else {
					$model->lastday_profits = 0;
				}
				// 今日结算金额和累计收益
				if ($preModel) {
					$model->today_settle_money = $preModel->today_settle_money + $preModel->lastday_profits + $this->_getKdbChangeMoney($uid, $date);
					$model->total_profits = $preModel->total_profits + $model->lastday_profits;
				} else {
					$model->today_settle_money = $this->_getKdbChangeMoney($uid, $date);
					$model->total_profits = $model->lastday_profits;
				}
				// 上面两个金额不全为0才存储
				if ($model->lastday_profits != 0 || $model->today_settle_money != 0) {
					$model->user_id = $uid;
					$model->project_type = UserDailyProfits::PROJECT_TYPE_KDB;
					$model->project_id = 0;
					$model->project_name = $kdb->title;
					$model->date = $date;
					$model->created_at = time();
					$transaction = Yii::$app->db->beginTransaction();
					try {
						if ($model->save()) {
							if ($model->lastday_profits > 0) {
								UserAccount::updateAccount($uid, [
									['lastday_kdb_profits', '=', $model->lastday_profits],
									['kdb_total_profits', '=', $model->total_profits],
									['total_profits', '+', $model->lastday_profits],
									['kdb_total_money', '+', $model->lastday_profits],
									['total_money', '+', $model->lastday_profits],
								]);
								UserAccount::addLog($uid, UserAccount::TRADE_TYPE_KDB_DAILY_PROFITS, $model->lastday_profits);
								// 更新口袋宝资金统计
								$time = time();
								$sql = "update " . KdbAccount::tableName() . "
										set history_profits_money = history_profits_money + {$model->lastday_profits},
											today_profits_money = today_profits_money + {$model->lastday_profits}, updated_at = {$time}
										where is_current = 1";
								Yii::$app->db->createCommand($sql)->execute();
							}
						} else {
							throw new \Exception(array_shift($model->getFirstErrors()));
						}
						$transaction->commit();
					} catch (\Exception $e) {
						$transaction->rollBack();
						$this->error("save user:{$uid} data occurs error:{$e}");
					}
				}
				// 如果收益为0则更新昨日收益即可，就不添加流水了
				if ($model->lastday_profits == 0) {
					UserAccount::updateAccount($uid, [
						['lastday_kdb_profits', '=', $model->lastday_profits],
					], false);
				}
			}
			
			$count = count($users);
			if ($count < $pageSize) {
				$this->message("finished page:{$page}, count:{$count}.");
				break;
			} else {
				$this->message("finished page:{$page}, count:{$count}, sleep:{$sleep}.");
				sleep($sleep);
				$page++;
			}
		}
	}
	
	/**
	 * 计算项目的昨日收益，如果项目累计收益有误差则纠正
	 * 
	 * @param string $date 默认null为昨日，否则必须为2014-11-22这样的格式
	 */
	public function actionDailyProject($date = null)
	{
		if ($date === null) {
			$date = date('Y-m-d', strtotime('-1 day'));
		} else if (!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $date)) {
			$this->error("date format error.", false);
			return self::EXIT_CODE_ERROR;
		}
		
		$page = 1;
		$pageSize = 500;
		$sleep = 2;
		
		while (true) {
			$offset = ($page - 1) * $pageSize;
			$users = (new Query())->from(User::tableName())->select([
				'id',
			])->offset($offset)->limit($pageSize)->all();
			
			foreach ($users as $user) {
				$uid = $user['id'];
				// 所有项目昨日累计收益
				$projLastdayProfits = (new Query())->from(UserDailyProfits::tableName())->where(
					"user_id = {$uid} and date = '{$date}' and project_type = " . UserDailyProfits::PROJECT_TYPE_PROJ
				)->sum('lastday_profits');
				$projLastdayProfits = intval($projLastdayProfits);
				
				UserAccount::updateAccount($uid, [
					['lastday_proj_profits', '=', $projLastdayProfits],
				], false);
				
				$accountLog = UserAccountLog::findOne([
					'user_id' => $uid,
					'type' => UserAccount::TRADE_TYPE_PROJ_DAILY_PROFITS,
				]);
				if ($accountLog) { // 已经计算过的话就更新
					$accountLog->operate_money = $projLastdayProfits;
					$accountLog->save();
				} else {
					if ($projLastdayProfits > 0) {
						UserAccount::addLog($uid, UserAccount::TRADE_TYPE_PROJ_DAILY_PROFITS, $projLastdayProfits);
					}
				}
				
				// 所有项目历史累计收益
				$projTotalProfits = ProjectProfits::find()->where([
					'profits_uid' => $uid,
					'status' => [ProjectProfits::STATUS_REPAYED, ProjectProfits::STATUS_FULLY_ASSIGNED],
				])->sum('duein_profits');
				$projTotalProfits = intval($projTotalProfits);
				
				// 更新累计收益
				$account = UserAccount::findOne(['user_id' => $uid]);
				if ($account) {
					$transaction = Yii::$app->db->beginTransaction();
					try {
						$totalProfits = $projTotalProfits + $account->kdb_total_profits;
						// 有误差则纠正
						if ($totalProfits != $account->total_profits) {
							UserAccount::updateAccount($uid, [
								['total_profits', '=', $totalProfits],
							]);
							UserAccount::addLog($uid, UserAccount::TRADE_TYPE_TOTAL_PROFITS_CORRECT, $totalProfits);
						}
						$transaction->commit();
					} catch (\Exception $e) {
						$transaction->rollBack();
						$this->error("save user:{$uid} total profits data occurs error:{$e}");
					}
				}
			}
			
			$count = count($users);
			if ($count < $pageSize) {
				$this->message("finished page:{$page}, count:{$count}.");
				break;
			} else {
				$this->message("finished page:{$page}, count:{$count}, sleep:{$sleep}.");
				sleep($sleep);
				$page++;
			}
		}
	}
	
	/**
	 * 注：此种计算方式暂时弃用，累计收益只在还款的时候累加
	 * 计算项目每日收益和累计收益
	 * 
	 * @param string $date 默认null为昨日，否则必须为2014-11-22这样的格式
	 */
	public function actionDailyProjectOld($date = null)
	{
		return ;
		if ($date === null) {
			$date = date('Y-m-d', strtotime('-1 day'));
		} else if (!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $date)) {
			$this->error("date format error.", false);
			return self::EXIT_CODE_ERROR;
		}
		
		$page = 1;
		$pageSize = 500;
		$sleep = 2;
		
		while (true) {
			$offset = ($page - 1) * $pageSize;
			$users = (new Query())->from(User::tableName())->select([
				'id',
			])->offset($offset)->limit($pageSize)->all();
			
			foreach ($users as $user) {
				$uid = $user['id'];
				// 获取用户所有产生收益的项目
				$profitses = ProjectProfits::find()->where([
					'profits_uid' => $uid,
					'status' => [ProjectProfits::STATUS_SUCCESS, ProjectProfits::STATUS_ASSIGNING, ProjectProfits::STATUS_FULLY_ASSIGNED, ProjectProfits::STATUS_REPAYED],
				])->with('project')->all();
				
				$projTotalProfits = 0; // 所有项目历史累计收益
				$projLastdayProfits = 0; // 所有项目昨日累计收益
				$profitsList = [];
				foreach ($profitses as $profits) {
					// 历史累计收益和列表
					$curTotalProfits = $profits->getCurTotalProfits();
					$projTotalProfits += $curTotalProfits;
					if ($curTotalProfits > 0) {
						$profitsList[] = [
							'id' => $profits->id,
							'project_name' => $profits->project_name,
							'profits' => $curTotalProfits,
							'status' => $profits->status,
						];
					}
					
					// 昨日累计收益
					if ($profits->status == ProjectProfits::STATUS_SUCCESS) {
						$projLastdayProfits += $profits->getDayProfits();
					}
					
					// 计算过了不重复计算
					if ($profits->status != ProjectProfits::STATUS_SUCCESS || $date >= $profits->last_repay_date
							|| UserDailyProfits::findOne([
								'date' => $date,
								'user_id' => $uid,
								'project_id' => $profits->project_id,
								'project_type' => UserDailyProfits::PROJECT_TYPE_PROJ,
								'invest_id' => $profits->invest_id,
							])) {
						continue;
					}
					
					// 每日收益相关
					$model = new UserDailyProfits();
					$model->user_id = $uid;
					$model->project_type = UserDailyProfits::PROJECT_TYPE_PROJ;
					$model->project_id = $profits->project_id;
					$model->project_name = $profits->project_name;
					$model->invest_id = $profits->invest_id;
					$model->date = $date;
					$model->created_at = time();
					$model->today_settle_money = $profits->duein_capital;
					$model->lastday_profits = $profits->getDayProfits();
					$model->total_profits = $curTotalProfits;
					if (!$model->save()) {
						$message = array_shift($model->getFirstErrors());
						$this->error("save user:{$uid} daily profits data occurs error:{$message}");
					}
				}
				
				// 如果收益为0则更新昨日收益即可，就不添加流水了
				if ($projLastdayProfits == 0) {
					UserAccount::updateAccount($uid, [
						['lastday_proj_profits', '=', $projLastdayProfits],
					], false);
				} else {
					$transaction = Yii::$app->db->beginTransaction();
					try {
						// 更新昨日收益
						UserAccount::updateAccount($uid, [
							['lastday_proj_profits', '=', $projLastdayProfits],
						], false);
						UserAccount::addLog($uid, UserAccount::TRADE_TYPE_PROJ_DAILY_PROFITS, $projLastdayProfits);
						$transaction->commit();
					} catch (\Exception $e) {
						$transaction->rollBack();
						$this->error("save user:{$uid} lastday_proj_profits data occurs error:{$e}");
					}
				}
				
				// 更新累计收益
				$account = UserAccount::findOne(['user_id' => $uid]);
				if ($account) {
					$transaction = Yii::$app->db->beginTransaction();
					try {
						$totalProfits = $projTotalProfits + $account->kdb_total_profits;
						if ($totalProfits != $account->total_profits) {
							UserAccount::updateAccount($uid, [
								['total_profits', '=', $totalProfits],
							]);
							UserAccount::addLog($uid, UserAccount::TRADE_TYPE_PROJ_DAILY_PROFITS, $totalProfits);
						}
						$transaction->commit();
					} catch (\Exception $e) {
						$transaction->rollBack();
						$this->error("save user:{$uid} total profits data occurs error:{$e}");
					}
				}
				
				// 更新累计收益详情到缓存：口袋宝和项目一起，口袋宝有则加到第一个
				if ($account && $account->kdb_total_profits > 0) {
					$kdbProfits = [
						'id' => 0,
						'project_name' => '口袋宝',
						'profits' => $account->kdb_total_profits,
						'status' => 0,
					];
					array_unshift($profitsList, $kdbProfits);
				}
				UserRedis::HSET($uid, 'profits_list', serialize($profitsList));
			}
			
			$count = count($users);
			if ($count < $pageSize) {
				$this->message("finished page:{$page}, count:{$count}.");
				break;
			} else {
				$this->message("finished page:{$page}, count:{$count}, sleep:{$sleep}.");
				sleep($sleep);
				$page++;
			}
		}
	}
	
	/**
	 * 获得口袋宝某日的变更金额
	 * 某日变更金额=某日投资总额-某日转出总额
	 */
	private function _getKdbChangeMoney($uid, $date)
	{
		$startTime = strtotime($date);
		$endTime = $startTime + 24 * 3600;
		$investTotal = (new Query())->from(KdbInvest::tableName())->where(
			"user_id = {$uid} and created_at >= {$startTime} and created_at < {$endTime} and status = " . KdbInvest::STATUS_SUCCESS
		)->sum('invest_money');
		$rollOutTotal = (new Query())->from(KdbRolloutLog::tableName())->where(
			"user_id = {$uid} and created_at >= {$startTime} and created_at < {$endTime}"
		)->sum('money');
		return $investTotal - $rollOutTotal;
	}
	
	/**
	 * 获得口袋宝某日的结算金额
	 * 结算金额=目前口袋宝总额-时间段投资总额-时间段收益总额
	 */
	private function _getKdbSettleMoney($uid, $date)
	{
// 		$startTime = strtotime($date);
// 		$investTotal = (new Query())->from(KdbInvest::tableName())->where(
// 			"user_id = {$uid} and created_at >= {$startTime} and status = " . KdbInvest::STATUS_SUCCESS
// 		)->sum('invest_money');
// 		$profitsTotal = (new Query())->from(UserDailyProfits::tableName())->where(
// 			"user_id = {$uid} and date >= '{$date}' and project_type = " . UserDailyProfits::PROJECT_TYPE_KDB
// 		)->sum('lastday_profits');
// 		$account = UserAccount::findOne(['user_id' => $uid]);
// 		$settleMoney = $account->kdb_total_money - intval($investTotal) - intval($profitsTotal);
// 		return $settleMoney > 0 ? $settleMoney : 0;
		$startTime = strtotime($date);
		$invests = (new Query())->from(KdbInvest::tableName())->where(
			"user_id = {$uid} and created_at >= {$startTime} and status = " . KdbInvest::STATUS_SUCCESS
		)->all();
		$rollouts = (new Query())->from(KdbRolloutLog::tableName())->where(
			"user_id = {$uid} and created_at >= {$startTime}"
		)->all();
		$profitses = (new Query())->from(UserDailyProfits::tableName())->where(
			"user_id = {$uid} and date >= '{$date}' and project_type = " . UserDailyProfits::PROJECT_TYPE_KDB
		)->all();
		
		$changes = [];
		// 收益
		foreach ($profitses as $profits) {
			$changes[] = [
				'money' => $profits['lastday_profits'],
				'type' => 'profits',
				'created_at' => $profits['created_at'],
			];
		}
		// 投资
		foreach ($invests as $invest) {
			$changes[] = [
				'money' => $invest['invest_money'],
				'type' => 'invest',
				'created_at' => $invest['created_at'],
			];
		}
		// 转出
		foreach ($rollouts as $rollout) {
			$changes[] = [
				'money' => $rollout['money'],
				'type' => 'rollout',
				'created_at' => $rollout['created_at'],
			];
		}
		ArrayHelper::multisort($changes, 'created_at', SORT_DESC);
		
		$account = UserAccount::findOne(['user_id' => $uid]);
		$settleMoney = $account->kdb_total_money;
		// 如果有体验金，判断是否需要删除或者延期
		// TODO:日期计算还有需要重新review
		if ($account->kdb_experience_money > 0) {
			$accountLog = UserAccountLog::findOne(['user_id' => $uid, 'type' => UserAccount::TRADE_TYPE_KDB_EXP_MONEY_IN]);
			$remark = json_decode($accountLog->remark, true);
			if (isset($remark['startDate'], $remark['endDate'])
				&& $remark['startDate'] <= $date && $remark['endDate'] >= $date) {
				$settleMoney += $account->kdb_experience_money;
			}
			
			// 判断是否需要删除或者延期
			if (isset($remark['endDate']) && $date == $remark['endDate']) {
				$sum = (new Query())->from(ProjectInvest::tableName())->where(
					"user_id = {$uid}"
				)->sum('invest_money');
				if (!$remark['isExtend'] && $sum && $sum >= ExperienceMoneyAct::$config['extend_invest_money']) {
					$remark['endDate'] = date(strtotime('+' . ExperienceMoneyAct::$config['extend_profits_time'] . ' day', strtotime($remark['endDate'])));
					$remark['isExtend'] = 1;
					UserAccountLog::updateAll(
						['remark' => json_encode($remark)],
						['user_id' => $uid, 'type' => UserAccount::TRADE_TYPE_KDB_EXP_MONEY_IN]
					);
				} else {
					UserAccount::updateAccount($uid, [
						['kdb_experience_money', '-', $account->kdb_experience_money],
						['total_money', '-', $account->kdb_experience_money],
					], false);
					UserAccount::addLog($uid, UserAccount::TRADE_TYPE_KDB_EXP_MONEY_OUT, $account->kdb_experience_money);
				}
			}
		}
		$tempMoney = $settleMoney;
		foreach ($changes as $change) {
			if ($change['type'] == 'rollout') {
				$tempMoney += $change['money'];
			} else {
				$tempMoney -= $change['money'];
			}
			if ($tempMoney < $settleMoney) {
				$settleMoney = $tempMoney;
			}
		}
		
		return $settleMoney > 0 ? $settleMoney : 0;
	}
}