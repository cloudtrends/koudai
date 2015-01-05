<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use common\models\CreditBaseInfo;

/**
 * This is the model class for table "{{%project_invest}}".
 */
class ProjectProfits extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_CANCELED = 3;
    const STATUS_ASSIGNING = 4;
    const STATUS_PARTLY_ASSIGNED = 5; // 此状态暂时不需要
    const STATUS_FULLY_ASSIGNED = 6;
    const STATUS_REPAYED = 7;

    public static $status = [
        self::STATUS_PENDING => '申购中',
        self::STATUS_SUCCESS => '投资成功',
        self::STATUS_CANCELED => '作废',
        self::STATUS_ASSIGNING => '转让中',
        self::STATUS_PARTLY_ASSIGNED => '部分转让',
        self::STATUS_FULLY_ASSIGNED => '成功转让',
        self::STATUS_REPAYED => '已还款',
    ];

    /**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%project_profits}}';
	}
	
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			TimestampBehavior::className(),
		];
	}
	
	/**
	 * 获得日收益
	 */
	public function getDayProfits()
	{
		return round($this->duein_capital * ($this->project_apr / 100) / 365);
	}
	
	/**
	 * 获得当前的累计收益
	 */
	public function getCurTotalProfits()
	{
		if ($this->status == self::STATUS_REPAYED) {
			return $this->duein_profits;
		} else if ($this->status == self::STATUS_SUCCESS) {
			if (time() >= strtotime($this->last_repay_date)) {
				return $this->duein_profits;
			} else {
				$days = intval((strtotime(date('Y-m-d')) - strtotime($this->interest_start_date)) / (24 * 3600));
				return $days * $this->getDayProfits();
			}
		} else if ($this->status == self::STATUS_ASSIGNING) {
			// 获得转让记录
			$caInfo = CreditBaseInfo::findOne($this->ca_base_id);
			if ($caInfo) {
				$days = intval(($caInfo->assign_start_date - strtotime($this->interest_start_date)) / (24 * 3600));
				return $days * $this->getDayProfits();
			} else {
				return 0;
			}
		} else if ($this->status == self::STATUS_FULLY_ASSIGNED) {
			return $this->duein_profits;
		} else {
			return 0;
		}
	}

    public function getProject()
    {
        // id 是 Project 表中的字段
        // project_id 是 project_invest 中的字段
        return $this->hasOne(Project::className(),['id' => 'project_id']);
    }
    
    public function getInvest()
    {
    	return $this->hasOne(ProjectInvest::className(),['id' => 'invest_id']);
    }
}