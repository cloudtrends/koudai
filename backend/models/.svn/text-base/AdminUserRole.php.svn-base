<?php
namespace backend\models;

use yii\behaviors\TimestampBehavior;

/**
 * AdminUserRole model
 */
class AdminUserRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_user_role}}';
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
    		[['name', 'title'], 'required'],
    		['name', 'match', 'pattern' => '/^[0-9A-Za-z_]{1,30}$/i', 'message' => '标识只能是1-30位字母、数字或下划线'],
    		['name', 'unique'],
    		[['desc', 'permissions'], 'safe'],
    	];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
    	return [
    		'name' => '标识',
    		'title' => '名称',
    		'desc' => '描述',
    		'permissions' => '权限',
    	];
    }
    
    public static function findAllSelected()
    {
    	$roles = self::find()->asArray()->all();
    	 
    	$rolesItems = array();
    	foreach ($roles as $v) {
    		$rolesItems[$v['name']] = $v['title'];
    	}
    	 
    	return $rolesItems;
    }
}