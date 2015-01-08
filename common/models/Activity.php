<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * Activity model
 * This is the model class for table "{{%Activity}}".
 *
 * @property integer $id
 * @property string $title
 * @property string $thumbnail
 * @property string $content
 *
 */
class Activity extends \yii\db\ActiveRecord
{
    // 活动状态
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_OVER = 2;

    public static $status = [
        self::STATUS_DELETED => '草稿',
        self::STATUS_ACTIVE => '已发布',
        self::STATUS_OVER => '已结束',
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%activity}}';
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
            ['status', 'default', 'value' => self::STATUS_DELETED],            
            ['title', 'required', 'message' => '不能为空'],
            [['title'], 'string', 'max' => 200, 'message' => '名称不能超过200个字符'], 
            // 不需要验证但可以从表单提交保存的字段需要指定为safe
            [['content','abstract'], 'safe'],
            ['thumbnail', 'file', 'extensions' => 'gif, jpg, png',
            'maxSize'=>1024 * 1024 * 1, // 1MB
            'message'=>'文件最大不超过1MB，请重新上传文件',
            ],
        ];
    }
	
    /**
	 * 获得url绝对地址
     */
    public static function getThumbnailAbsUrl($thumbnail)
    {
    	$url = Yii::$app->getRequest()->getHostInfo() . Yii::$app->getRequest()->getBaseUrl() . '/' . Yii::$app->params['activityImgPath'] . '/' . $thumbnail;
    	return str_replace(
			['backend', 'admin.koudailc.com'],
			['frontend', 'api.koudailc.com'],
			$url
    	);
    }
}
