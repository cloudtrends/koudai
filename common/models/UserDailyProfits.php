<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * UserDailyProfits model
 */
class UserDailyProfits extends ActiveRecord
{
	const PROJECT_TYPE_KDB = 1;
	const PROJECT_TYPE_PROJ = 2;
	
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user_daily_profits}}';
	}
	
	public function getUser()
	{
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}
}