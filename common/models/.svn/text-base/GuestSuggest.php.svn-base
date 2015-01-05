<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%guest_suggest}}".
 *
 */
class GuestSuggest extends \yii\db\ActiveRecord
{
	const TYPE_FUN = 1;
	const TYPE_EXP = 2;
	const TYPE_DEMAND = 3;
	const TYPE_OTHER = 4;
	public static $types = [
		self::TYPE_FUN => '功能问题',
		self::TYPE_EXP => '用户体验',
		self::TYPE_DEMAND => '您的需求',
		self::TYPE_OTHER => '其他',
	];
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%guest_suggest}}';
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
            [['type', 'user_info', 'content'], 'required'],
        ];
    }
    
    public function attributeLabels()
    {
    	return [
    		'type' => '反馈类型',
    		'user_info' => '联系方式',
    		'content' => '反馈内容',
    	];
    }
}
