<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use common\models\ProjectInvest;
use common\models\ProjectProfits;
use common\models\ProjectReviewLog;

/**
 * This is the model class for table "{{%project}}".
 */
class Project extends \yii\db\ActiveRecord
{
	/**
	 * 项目类型
	 */
	const TYPE_P2P = 1;
    const TYPE_TRUST = 2;

	public static $typeList = [
		self::TYPE_P2P => "安稳袋",
		self::TYPE_TRUST => "金融袋",
	];
	
	/**
	 * 项目状态
	 */
	const STATUS_NEW = 1;
	const STATUS_NEW_CANCEL = 2;
	const STATUS_PUBLISHED = 3;
	const STATUS_PUBLISHED_CANCEL = 4;
	const STATUS_FULL = 5;
	const STATUS_FULL_CANCEL = 6;
	const STATUS_REPAYING = 7;
	const STATUS_REPAYED = 8;

	public static $status = [
		self::STATUS_NEW => '待审核',
		self::STATUS_NEW_CANCEL => '初审作废',
		self::STATUS_PUBLISHED => "投资中",
		self::STATUS_PUBLISHED_CANCEL => '未满款作废',
		self::STATUS_FULL => '已满款',
		self::STATUS_FULL_CANCEL => '满款作废',
		self::STATUS_REPAYING => '还款中',
		self::STATUS_REPAYED => '已还款',
	];
	
	/**
	 * 产品类型
	 */
	public static $productTypes = [
		'汽车质押债权' => '汽车质押债权',
		'房产抵押债权' => '房产抵押债权',
		'股票质押债权' => '股票质押债权',
		'信托合同质押债权' => '信托合同质押债权',
	];

    /**
     * 项目操作
     */
    const ACTION_INIT = 1;
    const ACTION_PUBLISH = 2;
    const ACTION_CANCEL = 3;

    public static $action_desc = [
        self::ACTION_INIT => "创建",
        self::ACTION_PUBLISH => "审核通过",
        self::ACTION_CANCEL => "作废",
    ];
	
	/**
	 * 风控
	 */
	const RISK_CONTROL_MANAGED = '帐户资金安全由平安保险100%承保';
	const RISK_CONTROL_WARRANT = '交易由第三方民鑫公司担保';
	const RISK_CONTROL_REPAY = '工商银行监管风险准备金，本息垫付';
	
	/**
	 * 起购金额
	 */
	public static $minInvestMoneys = [
		'50'	=> '50元',
		'100'	=> '100元',
		'200'	=> '200元',
		'300'	=> '300元',
		'500'	=> '500元',
		'1000'	=> '1000元',
		'1500'	=> '1500元',
		'3000'	=> '3000元',
		'5000'	=> '5000元',
		'10000'	=> '10000元',
	];
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%project}}';
    }
    
    public function fields()
    {
    	$fields = parent::fields();
		
	    // remove fields that contain sensitive information
	    unset($fields['created_at'], $fields['updated_at'], $fields['review_at'], $fields['created_username'], $fields['publish_username'], $fields['review_username']);
	    
	    return $fields;
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
        	[['name', 'type', 'product_type', 'period', 'is_day', 'effect_time', 'total_money', 'apr', 'is_novice', 'min_invest_money', 'summary', 'desc'], 'required'],
        	['apr', 'number', 'min' => 0, 'max' => 100, 'message' => '只能0-100的数字', 'tooSmall' => '只能0-100的数字', 'tooBig' => '只能0-100的数字'],
        	[['total_money', 'period', 'effect_time', 'min_invest_money'], 'integer', 'min' => 1],
        	['total_money', function ($attribute, $params) {
        		if ($this->$attribute % $this->min_invest_money > 0) {
        			$this->addError($attribute, '项目金额必须是起投金额的整数倍');
        		}
        	}],
        	['min_invest_money', function ($attribute, $params) {
                if ($this->$attribute > $this->total_money) {
                    $this->addError($attribute, '起购金额不能大于项目金额');
                }
            }],
        	[['interest_date', 'repay_date', 'repayment_remark'], 'safe'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
    	return [
    		'type' => '类型',
    		'product_type' => '产品类型',
    		'name' => '项目名称',
    		'total_money' => '项目金额',
    		'apr' => '年利率',
    		'period' => '借款期限',
    		'is_day' => '借款期限单位',
    		'is_novice' => '是否新手专属',
    		'min_invest_money' => '起购金额',
    		'effect_time' => '认购时间',
    		'interest_date' => '起息日',
    		'repay_date' => '还款日',
    		'repayment_remark' => '还款提示',
    		'summary' => '项目概述',
    		'desc' => '详细描述',
    		'created_at' => '创建时间',
    		'publish_at' => '发布时间',
    		'review_at' => '满款审核时间',
    		'created_username' => '创建人',
    	];
    }
    
    /**
	 * 获得计划还款日
     */
    public function getRepayDate()
    {
    	if ($this->is_day) {
    		return date('Y-m-d', $this->review_at + ($this->period + 1) * 24 * 3600);
    	} else {
    		return date('Y-m-d', strtotime("+{$this->period} month", $this->review_at) + 24 * 3600);
    	}
    }
    
    /**
	 * 获得期限Label
     */
    public function getPeriodLabel()
    {
    	if ($this->is_day) {
    		return "{$this->period}天";
    	} else {
    		return "{$this->period}个月";
    	}
    }
    
    /**
     * 获得本项目预计收益
     */
    public function getProfits()
    {
    	if ($this->is_day) {
			return round($this->period * $this->total_money * ($this->apr / 100) / 365);
		} else {
			return round($this->period * $this->total_money * ($this->apr / 100) / 12);
		}
    }
    
    /**
	 * 项目投资记录列表
     */
    public function getInvests()
    {
    	return $this->hasMany(ProjectInvest::className(), ['project_id' => 'id']);
    }
    
    /**
     * 项目投资收益列表
     */
    public function getProfitses()
    {
    	return $this->hasMany(ProjectProfits::className(), ['project_id' => 'id']);
    }
    
    /**
	 * 项目状态流转记录
     */
    public function getReviewLogs()
    {
    	return $this->hasMany(ProjectReviewLog::className(), ['project_id' => 'id']);
    }
}