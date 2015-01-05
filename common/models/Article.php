<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\ArticleType;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property integer $id
 * @property integer $type_id
 * @property string $title
 * @property integer $order
 * @property string $content
 * @property string $create_user
 * @property integer $create_time
 * @property integer $update_time
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article}}';
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
            [['title', 'type_id'], 'required', 'message' => '不能为空'],
            [['title'], 'string', 'max' => 200, 'message' => '名称不能超过200个字符'],
            [['order'], 'number', 'integerOnly' => true, 'message' => '只能为整数'],
            [['order'], 'default', 'value' => 0],
            // 不需要验证但可以从表单提交保存的字段需要指定为safe
            [['summary', 'content'], 'safe']
        ];
    }
    
    /**
     * 获得对应的栏目类型，可以通过$model->articleType
     */
    public function getArticleType()
    {
    	return $this->hasOne(ArticleType::className(), ['id' => 'type_id']);
    }
}