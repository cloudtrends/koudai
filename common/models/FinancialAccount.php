<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * FinancialAccount model
 * This is the model class for table "{{%financial_account}}".
 *
 */
class FinancialAccount extends \yii\db\ActiveRecord
{ 
 
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%financial_account}}';
    }

    // /**
    //  * 加上下面这行，数据库中的created_at和updated_at会自动在创建和修改时设置为当时时间戳(无此字段)
    //  * @inheritdoc
    //  */
    // public function behaviors()
    // {
    //     return [
    //         TimestampBehavior::className(),
    //     ];
    // }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [           
            [['site_total_money','usable_money','kdb_total_money','projects_total_money','merchant_number_money','to_total_revenue','third_party_alipay_balance','to_total_repayment','historical_platform_profit','profit'],'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
    	return [
    		'site_total_money' => '当前网站总额（元）',
    		'usable_money' => '用户总余额（元）',
    		'kdb_total_money' => '活期总额（口袋宝）（元）',
    		'projects_total_money' => '定期总额（本金）（元）',
    		'merchant_number_money' => '商户号总额（元）',
    		'to_total_revenue' => '待收益总额（元）',
    		'third_party_alipay_balance' => '第三方支付余额（元）',
    		'to_total_repayment' => '待收还款总额（元）',
    		'historical_platform_profit' => '历史平台盈亏额（元）',
    		'profit' => '盈利额（元）',
    		'date' => '日期',
    	];
    }

}
