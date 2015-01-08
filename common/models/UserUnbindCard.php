<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_unbind_card}}".
 *
 */
class UserUnbindCard extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_unbind_card}}';
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