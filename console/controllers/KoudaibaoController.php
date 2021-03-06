<?php


namespace console\controllers;

use Yii;
use yii\db\Query;
use common\models\KdbAccount;
use common\models\UserAccount;

/**
 * 口袋宝统计
 */
class KoudaibaoController extends BaseController
{
	/**
	 * 更新口袋宝资金统计
	 */
	public function actionStat()
	{
		$curAccount = KdbAccount::findCurrent();
		if (date('Y-m-d', $curAccount->created_at) == date('Y-m-d')) {
			$this->error("date already stat.", false);
			return self::EXIT_CODE_ERROR;
		}
		
		$db = Yii::$app->db;
		$transaction = $db->beginTransaction();
		try {
			$sql = "insert into `tb_kdb_account` (`is_current`, `cur_money`, `history_money`, `history_invest_times`, `history_profits_money`) 
					select `is_current`, `cur_money`, `history_money`, `history_invest_times`, `history_profits_money`
					from `tb_kdb_account`
					where `is_current`=1 order by `id` desc limit 1";
			$db->createCommand($sql)->execute();
			$id = $db->getLastInsertID();
			if ($id) {
				// 计算历史累计收益
				$history_profits_money = (new Query())->from(UserAccount::tableName())->where('kdb_total_profits > 0')->sum('kdb_total_profits');
				KdbAccount::updateAll([
					'created_at' => time(),
					'updated_at' => time(),
					'today_money' => 0,
					'history_profits_money' => intval($history_profits_money),
					'today_invest_times' => 0,
					'today_profits_money' => 0,
					'today_rollout_money' => 0,
				], 'id = ' . $id);
				KdbAccount::updateAll([
					'is_current' => 0,
					'end_at' => time(),
				], 'is_current = 1 and id != ' . $id);
			}
			$transaction->commit();
		} catch (\Exception $e) {
			$transaction->rollBack();
		}
	}
}
