<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%project_repayment}}".
 */
class ProjectRepayment extends \yii\db\ActiveRecord
{
	const STATUS_PLATFORM_FULL_REPAY = 1;
	const STATUS_LOANER_PARTLY_REPAY = 2;
	const STATUS_LOANER_FULL_REPAY = 3;
	public static $status = [
		self::STATUS_PLATFORM_FULL_REPAY => '平台完全还款',
		self::STATUS_LOANER_PARTLY_REPAY => '借款人部分还款',
		self::STATUS_LOANER_FULL_REPAY => '借款人完全还款',
	];
	
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%project_repayment}}';
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
			['status', 'required'],
			[['loaner_repay_money', 'overdue_money'], 'number', 'min' => 0],
			['loaner_repay_time', 'safe'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'status' => '目前真实状态',
			'loaner_repay_money' => '借款方已还款总额',
			'loaner_repay_time' => '借款方还款时间',
			'overdue_money' => '滞纳金'
		];
	}
}