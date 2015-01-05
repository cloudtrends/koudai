<?php
namespace common\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

/**
 * UserPassword model
 *
 * @property integer $id
 * @property string $user_id
 * @property string $password
 */
class UserPassword extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user_password}}';
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
			['password', 'required', 'message' => '密码不能为空'],
			['password', 'string', 'length' => [6, 16], 'message' => '密码为6-16位字符或数字', 'tooShort'=>'密码为6-16位字符或数字', 'tooLong'=>'密码为6-16位字符或数字'],
		];
	}
}