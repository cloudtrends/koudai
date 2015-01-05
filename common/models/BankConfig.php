<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class BankConfig extends ActiveRecord
{
    const PLATFORM_UMPAY = 1; // 联动支付
    const PLATFORM_LLPAY = 2; // 连连支付
    /**
     * @inheritdoc
     */
    public static function tableName(){
        return '{{%bank_config}}';
    }

}