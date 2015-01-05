<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use common\models\KdbAccount;

/**
 * This is the model class for table "{{%kdb_info}}".
 */
class KdbInfo extends \yii\db\ActiveRecord
{
	const STATUS_ON = 1;
	const STATUS_OFF = 0;
	public static $status = [
		self::STATUS_ON => '开启',
		self::STATUS_OFF => '关闭',
	];
	
	/**
	 * 风控
	 */
	const RISK_CONTROL_MANAGED = '帐户资金安全由平安保险100%承保';
	const RISK_CONTROL_WARRANT = '交易由第三方民鑫公司担保';
	const RISK_CONTROL_REPAY = '工商银行监管风险准备金，本息垫付';
	
	private $_curAccount;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%kdb_info}}';
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
     * @inheritdoc
     */
    public function rules()
    {
    	return [
    		[['title', 'total_money', 'apr', 'summary', 'instruction', 'desc', 'status', 'daily_invest_limit', 'daily_withdraw_limit', 'user_invest_limit', 'min_invest_money', 'product_type'], 'required', 'message' => '不能为空'],
    		[['total_money', 'daily_invest_limit', 'daily_withdraw_limit', 'user_invest_limit', 'min_invest_money'], 'integer', 'min' => 1, 'message' => '只能大于0的整数', 'tooSmall' => '只能大于0的整数'],
    		['apr', 'number', 'min' => 0, 'max' => 100, 'message' => '只能0-100的数字', 'tooSmall' => '只能0-100的数字', 'tooBig' => '只能0-100的数字'],
    		['min_invest_money', function ($attribute, $params) {
    			if ($this->$attribute > $this->total_money) {
    				$this->addError($attribute, '起购金额不能大于总金额');
    			}
    		}],
    		['total_money', function ($attribute, $params) {
    			if (!$this->getIsNewRecord() && $this->$attribute < $this->getOldAttribute($attribute) / 100) {
    				$this->addError($attribute, '总金额不能小于之前总金额');
    			}
    		}],
    	];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
    	return [
    		'title' => '标题',
    		'total_money' => '总金额',
    		'apr' => '年化利率',
    		'product_type' => '产品类型',
    		'status' => '投资状态',
    		'daily_invest_limit' => '每日每人投资限额',
    		'daily_withdraw_limit' => '每日每人转出限额',
    		'user_invest_limit' => '每人投资总额限额',
    		'min_invest_money' => '起购金额',
    		'summary' => '项目概述',
    		'instruction' => '相关说明',
    		'desc' => '详细描述',
    	];
    }
    
    /**
     * @return KdbInfo
     */
    public static function findKoudai()
    {
    	return static::find()->orderBy('id desc')->one();
    }
    
    /**
	 * 计算日收益
     */
    public function calculateProfits($money)
    {
    	return round($money * ($this->apr / 100) / 365);
    }
    
    public function getCurAccount()
    {
    	if (!$this->_curAccount) {
    		$this->_curAccount = KdbAccount::findCurrent();
    	}
    	return $this->_curAccount;
    }
}