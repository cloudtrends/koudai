<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2014/12/8
 * Time: 20:44
 */

namespace common\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class UserPayOrder extends ActiveRecord
{
    const ACTION_INVEST_PAY = 1; // 投资支付
    const ACTION_CHARGE_PAY = 2; // 充值支付


    // 连连充值
    const STATUS_CHARGE_INIT = 0;
    const STATUS_CHARGE_SUCCESS = 1;
    const STATUS_CHARGE_FAILED = 2;
    const STATUS_CHARGE_EXPIRED = 3;

    public static $charge_status = [
        self::STATUS_CHARGE_INIT => '充值未完成',
        self::STATUS_CHARGE_SUCCESS => '充值成功',
        self::STATUS_CHARGE_FAILED => '充值失败',
        self::STATUS_CHARGE_EXPIRED => '充值订单过期',
    ];


    // 联动支付
    const STATUS_UMP_PAY_INIT = 10;
    const STATUS_UMP_PAY_SUCCESS = 11;
    const STATUS_UMP_PAY_FAILED = 12;
    const STATUS_UMP_PAY_EXPIRED = 13;

    public static $ump_pay_status = [
        self::STATUS_UMP_PAY_INIT => '支付未完成',
        self::STATUS_UMP_PAY_SUCCESS => '支付成功',
        self::STATUS_UMP_PAY_FAILED => '支付失败',
        self::STATUS_UMP_PAY_EXPIRED => '支付订单过期',
    ];


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_pay_order}}';
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

} 