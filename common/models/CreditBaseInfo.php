<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

class CreditBaseInfo extends \yii\db\ActiveRecord
{
    // 状态
    const STATUS_ASSIGN_CANCEL = 2;
    const STATUS_ASSIGNING = 4;
    const STATUS_PARTLY_ASSIGNED = 5; // 此状态暂时不需要
    const STATUS_FULLY_ASSIGNED = 6;
    const STATUS_REPAYED = 7;

    public static $status = [
        self::STATUS_ASSIGN_CANCEL => '用户取消转让',
        self::STATUS_ASSIGNING => '转让中',
        self::STATUS_PARTLY_ASSIGNED => '部分转让',
        self::STATUS_FULLY_ASSIGNED => '成功转让',
        self::STATUS_REPAYED => '已还款',
    ];

    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ca_base_info}}';
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

        ];
    }

    // 关联获取投资项目的 project 信息
    public function getProject()
    {
        // id 是 Project 表中的字段
        // project_id 是 CreditBaseInfo 中的字段
        return $this->hasOne(Project::className(),['id' => 'project_id']);
    }

    // 获取转让投资的详情，包括 invest_id 对应的 project, ca_base , profits
    public static function getDetailByInvestId( $invest_id )
    {
        $sql = "select "."
            pi.id invest_id,
            pi.user_id invest_uid,
            pi.username user_name,
            pi.status pi_status,
            pi.invest_money invest_money,
            pi.transfer_money transfer_money,
            pi.type pi_type,
            pi.former_invest_id former_invest_id,
            pi.latter_invest_id latter_invest_id,
            cbi.id ca_base_id,
            cbi.project_type cbi_project_type,
            cbi.invest_id cbi_invest_id ,
            cbi.assign_start_date cbi_assign_start_date,
            cbi.assign_end_date cbi_assign_end_date,
            cbi.assign_fee cbi_assign_fee,
            cbi.assign_rate cbi_assign_rate,
            cbi.commission_rate cbi_commission_rate,
            cbi.status cbi_status,
            p.id project_id,
            p.name project_name,
            p.type p_type,
            p.product_type p_product_type,
            p.status p_status,
            p.is_day p_is_day,
            p.period p_period,
            p.publish_at p_publish_at,
            p.effect_time p_effect_time,
            p.review_at p_review_at,
            p.apr p_apr,
            pp.id profit_id,
            pp.is_transfer pp_is_transfer,
            pp.profits_uid pp_profits_uid,
            pp.duein_money pp_duein_money,
            pp.duein_capital pp_duein_capital,
            pp.duein_profits pp_duein_profits,
            pp.interest_start_date pp_interest_start_date,
            pp.last_repay_date pp_last_repay_date,
            pp.status pp_status
        from ( (tb_project_invest pi left join tb_ca_base_info cbi on pi.ca_base_id = cbi.id)
            left join tb_project p on  p.id = pi.project_id )
            left join tb_project_profits pp on pp.invest_id = pi.id
        where pi.id = {$invest_id};";

        return self::findBySql($sql)->asArray()->one();
    }
}