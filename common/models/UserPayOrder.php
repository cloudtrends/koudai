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

class UserPayOrder extends ActiveRecord{

    const THIRD_PLATFORM_UMP = 1;
    const THIRD_PLATFORM_LIANLIAN = 2;

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