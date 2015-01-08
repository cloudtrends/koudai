<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * UserLoginLog model
 *
 * @property integer $id
 * @property integer $user_id
 */
class UserLoginLog extends ActiveRecord
{
	//登录类型
	const TYPE_NORMAL = 1;
	const TYPE_QQUNION = 2;
	public static $types = array(
    	self::TYPE_NORMAL => '用户名密码登录',
    	self::TYPE_QQUNION => 'qq联合登录',
    );
	
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user_login_log}}';
	}
}