<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $title
 * @property string $content
 * @property string $create_user
 * @property integer $create_time
 * @property integer $update_time
 */
class AppImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%app_image_info}}';
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
            [['title','type','os_type','app_version','height','width','auditor','img_url'], 'required', 'message' => '不能为空'],
            [['img_url', 'act_url'], 'url', 'message' => 'url格式输入有误'],
            [['title'],'username','match','allowEmpty'=>true, 'pattern'=>'/[a-z]/i','message'=>'必须为字母'],
            // 不需要验证但可以从表单提交保存的字段需要指定为safe
        ];
    }
}
