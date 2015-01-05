<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

class AppImageType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%app_image_type}}';
    }
    
    /**
     * 加上下面这行，数据库中的created_at和updated_at会自动在创建和修改时设置为当时时间戳
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
            [['name'], 'required', 'message' => '类型名称不能为空'],
            [['name'], 'string', 'max' => 16, 'message' => '类型名称不能超过16个字符'],
            [['comment'], 'string', 'max' => 32, 'message' => '备注信息不能超过32个字符'],
            [['comment'], 'safe']
        ];
    }
}
