<?php
/**
 * Created by PhpStorm.
 * User: haoyu
 * Date: 2014/11/7
 * Time: 16:47
 */

namespace common\models;

use yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;




class InvestCor extends ActiveRecord
{
    const STATUS_OK = 0;

    public static function primaryKey()
    {
        return ['id'];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ca_invest_corresponding}}';
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
        return [];
    }

    public function getCreditBaseInfo()
    {
        // id 是 CreditBaseInfo 表中的字段
        // ca_base_info_id 是 InvestCor 中的字段
        return $this->hasOne(CreditBaseInfo::className(), ['id' => 'ca_base_info_id']);
    }

    // 关联获取投资项目的 project 信息
    public function getProject()
    {
        // id 是 Project 表中的字段
        // project_id 是 CreditBaseInfo 中的字段
        return $this->hasOne(Project::className(),['id' => 'former_invest_id']);
    }
}