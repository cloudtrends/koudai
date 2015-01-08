<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;

class UserContact extends ActiveRecord
{

	/**
	 * @inheritdoc
	 */
	public static function tableName(){
		return '{{%user_contact}}';
	}


    /**
     * 根据OPENID获取用户手机号
     * @param $openid
     * @return static
     */
    public static function findUserByOpenid($openid){
        return self::findOne(['contact_id' => $openid]);
    }


}