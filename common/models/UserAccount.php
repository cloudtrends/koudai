<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\db\Transaction;
use common\models\User;
use common\exceptions\InvestException;

/**
 * UserAccount model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $total_money
 * @property integer $usable_money
 * @property integer $withdrawing_money
 * @property integer $investing_money
 * @property integer $duein_capital
 * @property integer $duein_profits
 * @property integer $total_profits
 * @property integer $last_profits
 * @property integer $kdb_total_money
 * @property integer $kdb_total_profits
 */
class UserAccount extends ActiveRecord
{
	const ACCOUNT_TYPE_P2P = 1;
	const ACCOUNT_TYPE_FUND = 2;
	
	const TRADE_TYPE_RECHARGE = 1;
	const TRADE_TYPE_ROLLOUT = 2;
	const TRADE_TYPE_WITHDRAW = 3;
	const TRADE_TYPE_INVEST_KDB = 4;
	const TRADE_TYPE_INVEST_PROJ = 5;
	const TRADE_TYPE_NOFULL_CANCLE = 6;
	const TRADE_TYPE_FULL_SUCCESS = 7;
	const TRADE_TYPE_FULL_CANCLE = 8;
	const TRADE_TYPE_REPAY = 9;
    const TRADE_TYPE_TRANSFER_USABLE_IN = 10;
    const TRADE_TYPE_TRANSFER_USABLE_OUT = 11;
    const TRADE_TYPE_KDB_DAILY_PROFITS = 12;
    const TRADE_TYPE_PROJ_DAILY_PROFITS = 13;
    const TRADE_TYPE_APPLY_WITHDRAW = 14;
    const TRADE_TYPE_REJECT_WITHDRAW = 15;
    const TRADE_TYPE_INVEST_KDB_BY_CARD = 16;
    const TRADE_TYPE_INVEST_PROJ_BY_CARD = 17;
    const TRADE_TYPE_TRANSFER_CARD_OUT = 18;
    const TRADE_TYPE_TOTAL_PROFITS_CORRECT = 19;
    const TRADE_TYPE_BACKEND_RECHARGE = 20;
    const TRADE_TYPE_KDB_EXP_MONEY_IN = 21;
    const TRADE_TYPE_KDB_EXP_MONEY_OUT = 22;
	public static $tradeTypes = [
		self::TRADE_TYPE_RECHARGE => '充值',
		self::TRADE_TYPE_ROLLOUT => '口袋宝转出',
		self::TRADE_TYPE_WITHDRAW => '提现成功',
		self::TRADE_TYPE_INVEST_KDB => '口袋宝投资', // 余额口袋宝投资
		self::TRADE_TYPE_INVEST_PROJ => '项目投资',	// 余额项目投资
		self::TRADE_TYPE_NOFULL_CANCLE => '项目未满款作废',
		self::TRADE_TYPE_FULL_SUCCESS => '项目满款审核通过',
		self::TRADE_TYPE_FULL_CANCLE => '项目满款作废',
		self::TRADE_TYPE_REPAY => '项目还款',
		self::TRADE_TYPE_TRANSFER_USABLE_IN => '债权转让余额收入',
		self::TRADE_TYPE_TRANSFER_USABLE_OUT => '债权转让余额支出', // 余额买转让
		self::TRADE_TYPE_KDB_DAILY_PROFITS => '口袋宝每日收益',
		self::TRADE_TYPE_PROJ_DAILY_PROFITS => '项目每日收益',
		self::TRADE_TYPE_APPLY_WITHDRAW => '申请提现',
		self::TRADE_TYPE_REJECT_WITHDRAW => '提现驳回',
		self::TRADE_TYPE_INVEST_KDB_BY_CARD => '银行卡口袋宝投资',
		self::TRADE_TYPE_INVEST_PROJ_BY_CARD => '银行卡项目投资',
		self::TRADE_TYPE_TRANSFER_CARD_OUT => '债权转让银行卡支出', // 银行卡扣款买转让
		self::TRADE_TYPE_TOTAL_PROFITS_CORRECT => '累计收益纠正', // 每日脚本重新计算，如有误差则更新
		self::TRADE_TYPE_BACKEND_RECHARGE => '后台直付',
		self::TRADE_TYPE_KDB_EXP_MONEY_IN => '口袋宝体验金充入',
		self::TRADE_TYPE_KDB_EXP_MONEY_OUT => '口袋宝体验金扣除',
	];
	// 余额收入状态集合
	public static $remainInTypes = [
		self::TRADE_TYPE_RECHARGE,
		self::TRADE_TYPE_ROLLOUT,
		self::TRADE_TYPE_NOFULL_CANCLE,
		self::TRADE_TYPE_FULL_CANCLE,
		self::TRADE_TYPE_REPAY,
		self::TRADE_TYPE_TRANSFER_USABLE_IN,
		self::TRADE_TYPE_BACKEND_RECHARGE,
	];
	// 余额支出状态集合
	public static $remainOutTypes = [
		self::TRADE_TYPE_WITHDRAW,
		self::TRADE_TYPE_INVEST_KDB,
		self::TRADE_TYPE_INVEST_PROJ,
		self::TRADE_TYPE_TRANSFER_USABLE_OUT,
	];
	
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user_account}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			['type_id', 'default', 'value' => self::ACCOUNT_TYPE_P2P],
		];
	}
	
	/**
	 * 更新账户资金
	 * @param integer $user_id
	 * @param array $column 例如：[['kdb_total_money', '-', $money], ['usable_money', '+', $money]]
	 * @param bool $throw_exception 更新的sql语句如果返回0的话是否抛异常，建议如果存在更新字段前后相等且可以接受继续往下走的时候设为false
	 */
	public static function updateAccount($user_id, $columns, $throw_exception = true)
	{
		if (!is_array($columns)) return false;
		$sets = 'updated_at = ' . time() . ',';
		foreach ($columns as $column) {
			if (isset($column[0], $column[1], $column[2])) {
				if ($column[1] == '=') {
					$sets .= "{$column[0]} = $column[2],";
				} else {
					$sets .= "{$column[0]} = {$column[0]} {$column[1]} {$column[2]},";
				}
			}
		}
		$sets = trim($sets, ',');
		$sql = "update {{%user_account}}
				set {$sets}
				where user_id = {$user_id}";
		$result = Yii::$app->db->createCommand($sql)->execute();
		if ($throw_exception && !$result) {
			throw new \Exception("update user:{$user_id} account failed.");
		}
		return $result;
	}


    /**
     * 转让账户余额
     * @param integer $source_uid 转账方
     * @param integer $destination_uid 收款方
     * @param integer $transfer_money 转账金额
     * @param Transaction $transaction 事务对象
     * @param bool $throw_exception 是否抛出异常
     */
    public static function transferUsableMoney($source_uid, $destination_uid, $transfer_money, $transaction = null,$throw_exception = false)
    {
        $db = Yii::$app->db;
        $isNeedCommit = false;
        if( empty($transaction) )
        {
            $transaction = $db->beginTransaction();
            $isNeedCommit = true;
        }

        // 1. 转载方扣款
        $sql = "update ". self::tableName() .
            " SET usable_money = usable_money - {$transfer_money} " .
            " WHERE usable_money >= {$transfer_money}
              AND user_id = {$source_uid}";

        $affected_rows = $db->createCommand($sql)->execute();
        if( $affected_rows != 1 )
        {
            $transaction->rollBack();
            if($throw_exception)
            {
                InvestException::throwCodeExt(1201);
            }
            return false;
        }

        // 2. 收款方收款
        $sql = "update ". self::tableName() .
            " SET usable_money = usable_money + {$transfer_money} " .
            " WHERE user_id = {$destination_uid}";

        $affected_rows = $db->createCommand($sql)->execute();
        if( $affected_rows != 1 )
        {
            $transaction->rollBack();
            if($throw_exception)
            {
                InvestException::throwCodeExt(1201);
            }
            return false;
        }

        // 成功提交，并添加记录流水
        if($isNeedCommit)
            $transaction->commit();

        self::addLog($source_uid,self::TRADE_TYPE_TRANSFER_USABLE_OUT,$transfer_money,"转账给{$destination_uid}");
        self::addLog($destination_uid,self::TRADE_TYPE_TRANSFER_USABLE_IN,$transfer_money,"从{$source_uid}收账");
        return true;
    }

	/**
	 * 静态方法记录资金流水
	 * 先查出资金信息再插入记录，无UserAccount实例时用这个
	 * 
	 * @param integer $user_id
	 * @param integer $trade_type UserAccount::TRADE_TYPE_*
	 * @param integer $money
	 * @param string $remark
	 */
	public static function addLog($user_id, $trade_type, $money, $remark = '')
	{
		$account = (new Query())->from(self::tableName())->where(['user_id' => $user_id])->one();
		$result = Yii::$app->db->createCommand()->insert(UserAccountLog::tableName(), [
			'user_id' => $user_id,
			'type' => $trade_type,
			'operate_money' => $money,
			'total_money' => $account['total_money'],
			'usable_money' => $account['usable_money'],
			'investing_money' => $account['investing_money'],
			'withdrawing_money' => $account['withdrawing_money'],
			'duein_capital' => $account['duein_capital'],
			'duein_profits' => $account['duein_profits'],
			'kdb_total_money' => $account['kdb_total_money'],
			'remark' => $remark,
			'created_at' => time(),
			'created_ip' => Yii::$app instanceof yii\web\Application ? Yii::$app->getRequest()->getUserIP() : '',
		])->execute();
		// 流水插入失败记录日志
		if (!$result) {
			Yii::error("Log account change failed, user_id:$user_id, type:$trade_type, money:$money, remark:$remark");
		}
		return $result;
	}
	
	/**
	 * 实例方法记录资金流水
	 * 如果已有UserAccount实例，优先用这个，减少sql查询
	 * 
	 * @param integer $trade_type UserAccount::TRADE_TYPE_*
	 * @param integer $money
	 * @param string $remark
	 */
	public function log($trade_type, $money, $remark = '')
	{
		$result = Yii::$app->db->createCommand()->insert(UserAccountLog::tableName(), [
			'user_id' => $this->user_id,
			'type' => $trade_type,
			'operate_money' => $money,
			'total_money' => $this->total_money,
			'usable_money' => $this->usable_money,
			'investing_money' => $this->investing_money,
			'withdrawing_money' => $this->withdrawing_money,
			'duein_capital' => $this->duein_capital,
			'duein_profits' => $this->duein_profits,
			'kdb_total_money' => $this->kdb_total_money,
			'remark' => $remark,
			'created_at' => time(),
			'created_ip' => Yii::$app instanceof yii\web\Application ? Yii::$app->getRequest()->getUserIP() : '',
		])->execute();
		// 流水插入失败记录日志
		if (!$result) {
			Yii::error("Log account change failed, user_id:$this->user_id, type:$trade_type, money:$money, remark:$remark");
		}
		return $result;
	}
	
	/**
	 * 获得所有持有资产
	 */
	public function getTotalHoldMoney()
	{
		return $this->kdb_total_money + $this->kdb_experience_money + $this->duein_capital + $this->duein_profits + $this->investing_money;
	}
	
	/**
	 * 获得上一天收益总额
	 */
	public function getLastdayProfits()
	{
		return $this->lastday_proj_profits + $this->lastday_kdb_profits;
	}
	
	/**
	 * 获取当日口袋宝转出总额
	 */
	public function getTodayRolloutTotal()
	{
		$today = strtotime(date('Y-m-d'));
        $tomorrow = $today + 24 * 3600;
		return (new Query())->from(KdbRolloutLog::tableName())->where(
			"user_id = {$this->user_id} and created_at >= {$today} and created_at <= {$tomorrow}"
		)->sum('money');
	}
	
	/**
	 * 获取当日口袋宝投资总额
	 */
	public function getTodayKdbInvestTotal()
	{
		$today = strtotime(date('Y-m-d'));
        $tomorrow = $today + 24 * 3600;
		return (new Query())->from(KdbInvest::tableName())->where(
			"user_id = {$this->user_id} and created_at >= {$today} and created_at <= {$tomorrow} and status = " . KdbInvest::STATUS_SUCCESS
		)->sum('invest_money');
	}
	
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}