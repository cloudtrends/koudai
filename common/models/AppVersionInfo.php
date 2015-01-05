<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

class AppVersionInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%app_version_info}}';
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
            [['app_id','version','os_type', 'update_record'], 'required', 'message' => '不能为空'],
            [['version'],       'string', 'max' => 8,   'message' => '不能超过8个字符'],
            [['comment'],       'string', 'max' => 32,  'message' => '不能超过32个字符'],
            [['url'],           'string', 'max' => 128, 'message' => '不能超过128个字符'],
            [['update_record'], 'string', 'max' => 256, 'message' => '不能超过256个字符'],
            [['url'], 'url', 'defaultScheme' => 'http', 'message'=> '请输入正确格式的url'],
            [['version'], 'match', 'pattern' => "/\d{1,2}\.\d{1,2}\.\d{1,2}/", 'message' => '请输入正确的版本号'],
            [['app_id','os_type'], 'number', 'message' => '只能为数字'],
        ];
    }
}
