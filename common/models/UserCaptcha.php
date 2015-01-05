<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * UserCaptcha model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $phone
 * @property string $captcha
 * @property string $type
 * @property integer $generate_time
 * @property integer $expire_time
 */
class UserCaptcha extends ActiveRecord
{
	// 验证码30分钟有效期
	const EXPIRE_SPACE = 1800;
	
	// 验证码类型
	const TYPE_REGISTER = 'register';
	const TYPE_FIND_PWD = 'find_pwd';
	const TYPE_FIND_PAY_PWD = 'find_pay_pwd';
	const TYPE_INVEST_KDB = 'invest_kdb';
	const TYPE_INVEST_PROJ = 'invest_proj';
	const TYPE_INVEST_CREDIT = 'invest_credit';
	
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user_captcha}}';
	}
	
	/**
	 * 获得验证码的短信内容
	 */
	public function getSMS()
	{
		$appName = Yii::$app->name;
		$effective = intval(self::EXPIRE_SPACE / 60);
		return "您的验证码为:{$this->captcha} (此验证码有效期为{$effective}分钟)";
	}
}