<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%kdb_account}}".
 */
class KdbAccount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%kdb_account}}';
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
     * @return KdbAccount
     */
    public static function findCurrent()
    {
    	return static::find()->where(['is_current' => 1])->orderBy('id desc')->one();
    }
    
}