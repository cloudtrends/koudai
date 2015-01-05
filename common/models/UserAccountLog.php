<?php
namespace common\models;

use yii\db\ActiveRecord;

/**
 * UserAccountLog model
 */
class UserAccountLog extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_account_log}}';
    }

    /**
    * 获得对应的用户名，
    */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}