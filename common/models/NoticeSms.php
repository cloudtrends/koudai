<?php
namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\helpers\TimeHelper;

class NoticeSms extends ActiveRecord
{
    //记录NoticeSms 关键字搜索

    const SEND_WAIT    = 0 ;     //等待信息发送
    const SEND_SUCCESS = 1 ;     //信息发送成功
    const SEND_FAIL    = 2 ;     //信息发送失败

    const READ_NO      = 0 ;     //信息未读
    const READ_YES     = 1 ;     //信息已读

    const NOTICE_ASSIGNED = 1;      //转让成功
    const NOTICE_DRAWAL   = 2;      //提现成功
    const NOTICE_REPAYED  = 3;      //已还款
    const NOTICE_FULL     = 4;      //满款审核通过
    const NOTICE_CANCEL   = 5;      //未满款作废
    const NOTICE_FULL_FAIL= 6;      //满款审核作废

    public static $status = [
        self::NOTICE_ASSIGNED => '转让成功',
        self::NOTICE_DRAWAL   => '提现成功',
        self::NOTICE_REPAYED  => '已还款',
        self::NOTICE_FULL     => '满款审核通过',
        self::NOTICE_FULL_FAIL=> '满款审核作废',
        self::NOTICE_CANCEL   => '未满款作废',
    ];

    public static $send_status = [
        self::SEND_WAIT    => '等待',
        self::SEND_SUCCESS => '成功',
        self::SEND_FAIL    => '失败',
    ];

    public static $read_status = [
        self::READ_NO    => '未读',
        self::READ_YES   => '已读',
    ];

	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%notice_sms}}';
	}

    /**
     * Created by JohNnY
     * @return Model
     */
    private static $instances = array();
    public static function &instance() {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }
        return self::$instances[$class];
    }
	
	/**
	 * @inheritdoc
	 */
	public function rules(){
		return [];
	}

    /**
     * 返回所有未发送的信息
     * @param $status
     * @return static[]
     */
    public static function findNeedNotice($status){
        return self::findAll(['status' => $status]);
    }

    /**
     * 根据用户ID修改已读状态
     * @param $uid
     */
    public static function UpdateNoticeReadStatus($uid){
        if (empty($uid)){
            return false;
        }
        return self::updateAll(['is_read'=>self::READ_YES],['user_id'=>$uid,'is_read'=>self::READ_NO]);
    }

    /**
     * 根据用户ID显示未读信息
     * @param $uid
     */
    public static function findNewNotice($uid,$page = 1,$pageSize = 15){
        $page = $page > 1 ? intval($page) : 1;
        $pageSize = intval($pageSize);
        $offset = ($page - 1) * $pageSize;
        return self::find()->select(['id','type','remark','created_at'])
            ->where(['user_id' => $uid])
            ->orderBy(['id' => SORT_DESC,])->offset($offset)->limit($pageSize)
            ->asArray()->all();
    }

    /**
     * 根据用户ID显示未读信息总数
     * @param $uid
     */
    public static function findNewNoticeByCount($uid){
        return self::find()->where(['user_id' => $uid,'is_read'=>0])->count();
    }

    public function InsertDate($user_id ,$type , $remark , $now , $status=0){
        if (empty($user_id) || empty($type) || empty($remark)){
            return false;
        }

        $result = Yii::$app->db->createCommand()->insert(self::tableName(), [
            'user_id' => $user_id,
            'type' => $type,
            'status' => $status,
            'remark' => $remark,
            'created_at' => !empty($now) ? $now : time(),
        ])->execute();
        // 插入失败
        if (!$result) {
            Yii::error("Notice_SMS insert failed, user_id:$user_id, type:$type ,remark : $remark,status:$status");
        }
        return $result;
    }

    /**
     * 拼接短信信息字符串
     * @param $user_id
     * @param $type
     * @param array $memo
     * @return bool
     *。



     */
    const STARTSTR = '尊敬的口袋会员，';
    public function init_sms_str($user_id,$type,$memo=array()){
        if (empty($user_id) || empty($type) || empty($memo) ){
            return false;
        }
        $str = '';
        $now = TimeHelper::Now();
        switch ($type){
            case self::NOTICE_ASSIGNED:  //【口袋理财﹒转让成功】 尊敬的口袋会员，您的投资项目【车袋袋9号】已于2014-12-29 16:10 成功转让且转让金额已放入您的口袋余额。需知悉：该项目之后的各项权益将归接手的投资者所有。
                $str .= self::STARTSTR.'您的投资项目【'.$memo['project_name'].'】已于'.date("Y-m-d h:i",$now).' 成功转让且转让金额已放入您的口袋余额。需知悉：该项目之后的各项权益将归接手的投资者所有。';
                break;
            case self::NOTICE_DRAWAL:    //【口袋理财﹒提现成功】  尊敬的口袋会员，您于2014-12-29 16:00提交的1000元提现申请已处理成功，请查看您绑定银行卡的收入明细。
                $str .= self::STARTSTR.'您于'.date("Y-m-d h:i",$now).'提交的'.sprintf('%.2f',$memo['money'] / 100).'元提现申请已处理成功，请查看您绑定银行卡的收入明细。';
                break;
            case self::NOTICE_REPAYED:   //【口袋理财﹒已还款成功】  尊敬的口袋会员，您的投资项目【车袋袋9号】项目期满，已完成还款。请登录口袋理财至余额查看您的本金和收益。
                $str .= self::STARTSTR.'您的投资项目【'.$memo['project_name'].'】项目期满，已完成还款。请登录口袋理财至余额查看您的本金和收益。';
                break;
            case self::NOTICE_FULL:      //【口袋理财﹒满款审核通过】    尊敬的口袋会员，您的投资项目【车袋袋9号】已募集成功且审核通过，2014-12-30开始计算收益，预计2015年1月30号确认并完成还款。
                $str .= self::STARTSTR.'您的投资项目【'.$memo['project_name'].'】已募集成功且审核通过，'.$memo["interest_start_date"].'开始计算收益，预计'.date('Y年m月d号',strtotime($memo["last_repay_date"])).'确认并完成还款。';
                break;
            case self::NOTICE_CANCEL:    //【口袋理财﹒满款审核作废】   尊敬的口袋会员，非常遗憾！您的投资项目【车袋袋9号】未通过口袋理财满款审核，该项目作废。您的投资金额已转入余额，请尝试投资其他项目。
                $str .= self::STARTSTR.'非常遗憾！您的投资项目【'.$memo['project_name'].'】未通过口袋理财满款审核，该项目作废。您的投资金额已转入余额，请尝试投资其他项目。';
                break;
            case self::NOTICE_FULL_FAIL: //【口袋理财﹒未满款作废】  尊敬的口袋会员，非常遗憾！您的投资项目【车袋袋9号】在认购期限内未能募集成功，该项目作废。您的投资金额已转入余额，请尝试投资其他项目。
                $str .= self::STARTSTR.'非常遗憾！您的投资项目【'.$memo['project_name'].'】在认购期限内未能募集成功，该项目作废。您的投资金额已转入余额，请尝试投资其他项目。';
                break;
            default:
                break;
        }
        if ($str){
            $this->InsertDate($user_id,$type,$str,$now);
        }

        return true;
    }

}