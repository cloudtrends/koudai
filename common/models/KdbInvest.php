<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%kdb_invest}}".
 */
class KdbInvest extends \yii\db\ActiveRecord
{
	const STATUS_SUCCESS = 1;
	const STATUS_CANCLED = 2;
	public static $status = [
		self::STATUS_SUCCESS => '投资成功',
		self::STATUS_CANCLED => '作废',
	];
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%kdb_invest}}';
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
    		['invest_money', 'required', 'message' => '投资金额不能为空'],
    		['invest_money', 'integer', 'min' => 1, 'message' => '投资金额必须大于1元', 'tooSmall' => '投资金额必须大于1元'],
    	];
    }
}