<?php

namespace common\models;

/**
 * This is the model class for table "{{%user_detail}}".
 */
class UserDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_detail}}';
    }
}