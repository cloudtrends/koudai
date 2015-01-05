<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_bank_card}}".
 */
class UserBankCard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const STATUS_UNBIND = 0; //
    const STATUS_BIND = 1;

    public static $status_desc = array(
        self::STATUS_UNBIND => "未绑定",
        self::STATUS_BIND => "已绑定",
    );

    public static function tableName()
    {
        return '{{%user_bank_card}}';
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
    
    public static function getBankInfo($id)
    {
    	foreach (Yii::$app->params['supportBanks'] as $bank) {
    		if ($bank['code'] == $id) {
    			return $bank;
    		}
    	}
    	return [];
    }
}