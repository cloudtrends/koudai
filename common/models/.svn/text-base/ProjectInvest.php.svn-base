<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use common\models\Project;
use common\models\CreditBaseInfo;
use common\models\ProjectProfits;

/**
 * This is the model class for table "{{%project_invest}}".
 */
class ProjectInvest extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_CANCELED = 3;
    const STATUS_ASSIGNING = 4;
    const STATUS_PARTLY_ASSIGNED = 5; // 此状态暂时不需要
    const STATUS_FULLY_ASSIGNED = 6;
    const STATUS_REPAYED = 7;

    public static $status = [
        self::STATUS_PENDING => '申购中',
        self::STATUS_SUCCESS => '投资成功',
        self::STATUS_CANCELED => '作废',
        self::STATUS_ASSIGNING => '转让中',
        self::STATUS_PARTLY_ASSIGNED => '部分转让',
        self::STATUS_FULLY_ASSIGNED => '成功转让',
        self::STATUS_REPAYED => '已还款',
    ];

    const YES = 1;
    const NO = 0;

    public static $is_transfer = [
        self::YES=>'是',
        self::NO=>'否',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%project_invest}}';
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
     * 计算收益
     * 返回单位为分
     */
    public function getDueinProfits(Project $project)
    {
        if ($project->is_day) {
            return round($project->period * $this->invest_money * ($project->apr / 100) / 365);
        } else {
            return round($project->period * $this->invest_money * ($project->apr / 100) / 12);
        }
    }

    public function getProject()
    {
        // id 是 Project 表中的字段
        // project_id 是 project_invest 中的字段
        return $this->hasOne(Project::className(),['id' => 'project_id']);
    }

    public function getProfits()
    {
        // invest_id 是 ProjectProfits 表中的字段
        // id 是 project_invest 中的字段
        return $this->hasOne(ProjectProfits::className(),['invest_id' => 'id']);
    }
    public function getCabaseinfo()
    {
        // invest_id 是 Ca_base_info 表中的字段
        // id 是 project_invest 中的字段
        return $this->hasOne(CreditBaseInfo::className(),['id' => 'ca_base_id']);
    }

    public static function getDetailById( $invest_id )
    {
        $sql = "select ".
            "pi.id invest_id,
             pi.project_id pi_project_id,
             pi.project_name pi_project_name,
             pi.user_id invest_uid,
             pi.username pi_username,
             pi.status pi_status,
             pi.invest_money pi_invest_money,
             pi.transfer_money pi_transfer_money,
             pi.created_at pi_created_at,
             pi.updated_at pi_updated_at,
             pi.remark pi_remark,
             pi.created_ip pi_created_ip,
             pi.type pi_type,
             pi.is_statistics pi_is_statistics,
             pi.is_transfer pi_is_transfer,
             pi.former_invest_id pi_former_invest_id,
             pi.latter_invest_id pi_latter_invest_id,
             pi.ca_base_id pi_ca_base_id,
             pp.id profit_id,
             pp.invest_id pp_invest_id,
             pp.project_id pp_project_id,
             pp.project_name pp_project_name,
             pp.project_apr pp_project_apr,
             pp.invest_uid pp_invest_uid,
             pp.is_transfer pp_is_transfer,
             pp.profits_uid pp_profits_uid,
             pp.duein_money pp_duein_money,
             pp.duein_capital pp_duein_capital,
             pp.duein_profits pp_duein_profits,
             pp.interest_start_date pp_interest_start_date,
             pp.last_repay_date pp_last_repay_date,
             pp.status pp_status,
             pp.ca_base_id pp_ca_base_id,
             pp.created_at pp_created_at,
             pp.updated_at pp_updated_at,
             p.id project_id,
             p.is_day p_is_day,
             p.period p_period,
             p.status p_status,
             p.review_at p_review_at
        from (tb_project_invest pi left join tb_project_profits pp
             on pi.id = pp.invest_id)
             left join tb_project p on p.id = pi.project_id
        where pi.id = {$invest_id};";

        return self::findBySql($sql)->asArray()->one();
    }

}