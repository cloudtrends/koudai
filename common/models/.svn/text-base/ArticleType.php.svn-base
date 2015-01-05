<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%article_type}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property integer $is_builtin
 */
class ArticleType extends \yii\db\ActiveRecord
{
	// 内置栏目类型，数据库中的类型标识必须与此一一对应
	const TYPE_DEFAULT			= 'default';
	const TYPE_ABOUT			= 'about';
	const TYPE_HELP				= 'help';
	const TYPE_AGREEMENT_USE	= 'agreementuse';
	const TYPE_AGREEMENT_BUY	= 'agreementbuy';
	const TYPE_AGREEMENT_PAY	= 'agreementpay';
	const TYPE_NOTICE_CENTER	= 'notice_center';
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article_type}}';
    }
    
    /**
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
            [['name', 'title'], 'required', 'message' => '不能为空！'],
            [['name', 'title'], 'string', 'max' => 30, 'message' => '不能超过30个字符！'],
            ['name', 'match', 'pattern' => '/^[a-zA-Z0-9]*$/i', 'message' => '只能是数字或字母'],
            // 验证字段值数据库中唯一
            ['name', 'unique', 'message' => '已经存在，不能重复'],
        ];
    }
    
    public static function findAllSelected()
    {
    	$articleTypes = self::find()->orderBy('is_builtin desc')->asArray()->all();
    	
    	$articleTypeItems = array();
    	foreach ($articleTypes as $v) {
    		$articleTypeItems[$v['id']] = $v['title'];
    	}
    	
    	return $articleTypeItems;
    }
}
