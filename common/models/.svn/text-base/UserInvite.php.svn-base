<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\helpers\StringHelper;

class UserInvite extends ActiveRecord
{

    const ENCRYPT_KEY = 'invite';
	/**
	 * @inheritdoc
	 */
	public static function tableName(){
		return '{{%user_invite}}';
	}

    /**
     * 解密
     * @param $str 邀请CODE
     * 解密规则 ： USERID_加密字符串
     */
    static public function decrypt($str){
        if (empty($str)){
            return false;
        }
        return StringHelper::encrypt($str,'D',self::ENCRYPT_KEY);
    }

    /**
     * 加密
     * @param $userid
     */
    static public function ecrypt($userid){
        if (empty($userid)){
            return false;
        }
        return StringHelper::encrypt($userid,'E',self::ENCRYPT_KEY);
    }


}