<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

class AppConfInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%app_conf_info}}';
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
            [['app_name', 'app_en_name', 'website_url','website_name','tel','address','beian',
              'wx_share_url','wx_share_content'], 
              'required', 'message' => '不能为空'],
            //[['android_newest_version','ios_newest_version'],            'string', 'max' => 8,   'message' => '不能超过8个字符'],
            [['app_name','app_en_name','tel'],                           'string', 'max' => 16,  'message' => '不能超过16个字符'],
            [['website_url','website_name','beian','comment'],           'string', 'max' => 32,  'message' => '不能超过32个字符'],
            [['address'],                                                'string', 'max' => 64,  'message' => '不能超过64个字符'],
            //[['wx_share_url','android_download_url','ios_download_url'], 'string', 'max' => 128, 'message' => '不能超过128个字符'],
            [['wx_share_url'], 'string', 'max' => 128, 'message' => '不能超过128个字符'],
            [['wx_share_content'],                                       'string', 'max' => 256, 'message' => '不能超过256个字符'],
            [['website_url','wx_share_url'], 'url', 'defaultScheme' => 'http', 'message'=> '请输入正确格式的url'],
            [['app_en_name'], 'match', 'pattern' => '/^[a-z]\w*$/i','message' => '请输入正确的英文名称'],
            //[['android_newest_version','ios_newest_version'], 'match', 'pattern' => "/\d{1,2}\.\d{1,2}\.\d{1,2}/", 'message' => '请输入正确的版本号'],
            [['tel'], 'match', 'pattern' => '/^(\d|-)+$/', 'message' => '请输入正确的电话号码'],
            [['app_en_name'], 'string', 'max' => 16, 'message' => '不能超过16个字符']
        ];
    }
}
