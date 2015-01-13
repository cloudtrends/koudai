<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * Financial model
 * This is the model class for table "{{%Financial}}".
 *
 */
class Financial extends \yii\db\ActiveRecord
{ 
 // 项目类型
    const TYPE_REGULAR = 1;
    const TYPE_CURRENT = 2;

    public static $type = [
        self::TYPE_REGULAR => "定期项目",
        self::TYPE_CURRENT => "活期",
    ]; 
// 状态 注：此状态值须按流程顺序从小到大，且只多1，否则后台财务管理状态编辑有变动
    const PENDING_AUDIT = 4;
    const STATUS_CREATE_RAISED = 0;
    const STATUS_REPAYMENT = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_INVALID = 3;

    public static $status = [
        self::PENDING_AUDIT => '待审核',//活期
        self::STATUS_CREATE_RAISED => '创建筹集中',//定期
        self::STATUS_REPAYMENT => '待还款',
        self::STATUS_COMPLETED => '完成还款',
        self::STATUS_INVALID => '作废',
    ];
    //current_date 日期函数 获取日期格式如2015-01-10
    const HAS_EXPIRED = "0 AND (UNIX_TIMESTAMP(current_date)-86400*1)";
    const TODAY = "UNIX_TIMESTAMP(current_date) AND UNIX_TIMESTAMP(current_date)";
    const IN_THREE_DAYS = "UNIX_TIMESTAMP(current_date) AND (UNIX_TIMESTAMP(current_date)+86400*3)";
    const WITHIN_A_WEEK = "UNIX_TIMESTAMP(current_date) AND (UNIX_TIMESTAMP(current_date)+86400*7)";
    const WITHIN_A_MONTH = "UNIX_TIMESTAMP(current_date) AND (UNIX_TIMESTAMP(current_date)+86400*30)";
    //放款时间 范围
    public static $loan_time_ranger = [
        self::HAS_EXPIRED => '已过期',
        self::TODAY => '今日到期',
        self::IN_THREE_DAYS => '三天内',
        self::WITHIN_A_WEEK => '一周内',
        self::WITHIN_A_MONTH => '一个月内',
    ];
    //还款时间 范围
    public static $borrower_repayment_time_ranger = [
        self::HAS_EXPIRED => '已过期',
        self::TODAY => '今日到期',
        self::IN_THREE_DAYS => '三天内',
        self::WITHIN_A_WEEK => '一周内',
        self::WITHIN_A_MONTH => '一个月内',
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%financial}}';
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
            [['project_type','project_id','project_name','total_amount_financing','borrower_rate','user_rate','total_revenue','platform_revenue','investor_revenue','project_start_time','project_end_time','status','loan_time','borrower_repayment_time','created_at','updated_at','remarks'],'safe'],
            [['total_amount_financing', 'borrower_rate','loan_time','project_name','borrower_repayment_time'], 'required', 'message' => '不能为空'],
            ['project_id','required', 'message' => '请选择项目名称'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
    	return [
    		'project_type' => '项目类型',
    		'project_id' => '项目ID',
    		'project_name' => '项目名称',
    		'total_amount_financing' => '总融资额（元）',
    		'borrower_rate' => '借款人利率（%）',
    		'user_rate' => '用户利率（%）',
    		'total_revenue' => '实际收益（元）',
    		'platform_revenue' => '借款收益（元）',
    		'investor_revenue' => '用户收益（元）',
    		'project_start_time' => '项目开始时间',
    		'project_end_time' => '项目结束时间',
    		'status' => '状态',
    		'loan_time' => '放款时间',
    		'borrower_repayment_time' => '还款时间',
    		'created_at' => '创建时间',
    		'updated_at' => '更新时间',
    		'remarks' => '备注',
    	];
    }

    /**
     * 添加额外字段
     * @inheritdoc
     */
    //期限
    public function getTerm()  
    {
        if(!empty($_POST['term'])){
            return $_POST['term'];
        }
            return;        
    }
    //借款期限
    public function getLoan()  
    {
        if(!empty($_POST['loan'])){
            return $_POST['loan'];
        }
        if(!empty($_GET['id'])){
            $model = Financial::findOne($_GET['id']);
            $date=floor((strtotime($model->borrower_repayment_time)-strtotime($model->loan_time)));//时间差的毫秒数
            //计算相差的年数
            $years = floor($date / (12 * 30 * 24 * 3600));
            //计算相差的月数
            $leave = $date % (12 * 30 * 24 * 3600);
            $months = floor($leave / (30 * 24 * 3600));
            //计算出相差天数
            $leave0 = $leave % (30 * 24 * 3600);
            $days = floor($leave0 / (24 * 3600));
            $loan = ($years >0 ? $years.'年' : '').($months >0 ? $months.'月' : '').($days >0 ? $days.'天' : '');
            return '（倒计时'.$loan.'）';
        }
        return;
    }

}
