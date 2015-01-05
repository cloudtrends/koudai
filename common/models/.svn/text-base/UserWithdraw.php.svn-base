<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use common\models\User;

/**
 * This is the model class for table "{{%user_withdraw}}".
 */
class UserWithdraw extends \yii\db\ActiveRecord
{
	const STATUS_PENDING = 1;
	const STATUS_SUCCESS = 2;
	const STATUS_FAILED = 3;
	public static $status = array(
		self::STATUS_PENDING => '提现中',
		self::STATUS_SUCCESS => '提现成功',
		self::STATUS_FAILED => '提现失败',
	);



    /*
        联动支付接口状态
        1-支付中
        3-失败
        4-成功
     */
    const UMP_PAYING = 1;
    const UMP_PAY_FAILED = 3;
    const UMP_PAY_SUCCESS = 4;

    public static $ump_pay_status = array(
        self::UMP_PAYING => '提现中',
        self::UMP_PAY_FAILED => '提现失败',
        self::UMP_PAY_SUCCESS => '提现成功',
    );
    
    /**
     * 审核状态
     */
    const REVIEW_STATUS_NO = 0;
    const REVIEW_STATUS_APPROVE = 1;
    const REVIEW_STATUS_REJECT = 2;
    public static $review_status = array(
		self::REVIEW_STATUS_NO => '未审核',
		self::REVIEW_STATUS_APPROVE => '审核通过',
		self::REVIEW_STATUS_REJECT => '审核驳回',
    );
    
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user_withdraw}}';
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
	 * 关联对象：用户
	 */
	public function getUser()
	{
    	return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}