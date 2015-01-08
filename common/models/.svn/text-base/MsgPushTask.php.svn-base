<?php
/**
 * Created by PhpStorm.
 * User: haoyu
 * Date: 14-10-19
 * Time: 下午4:40
 */


namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;


class MsgPushTask extends \yii\db\ActiveRecord
{

    /* --------------------------------------- 短信推送 Starts --------------------------------------- */

    const txtUrl = "http://210.5.158.31/hy/?"; // 短信网关
    const txtEnterpriseUid = "90073"; //  企业UID
    const txtEnterpriseCode = "minjian" ; //  企业代码
    const txtEnterprisePwd = "aaa0001"; // 企业密码

    // 短信操作返回码
    public static $textOpCode = array(
        // 短信网关平台定义错误
        0 => "操作成功",
        -1 => "签权失败",
        -2 => "未检索到被叫号码，请检查号码列表，多个用逗号分隔",
        -3 => "被叫号码过多",
        -4 => "内容未签名",
        -5 => "内容过长",
        -6 => "余额不足",
        -7 => "暂停发送",
        -8 => "保留",
        -9 => "定时发送时间格式错误",
        -10 => "下发内容为空",
        -11 => "账户无效",
        -12 => "IP地址非法",
        -13 => "操作频率快",
        -14 => "操作失败",
        -15 => "拓展码无效",
        -16 => "取消定时,seqid错误",
        -17 => "未开通报告",
        -18 => "暂留",
        -19 => "未开通上行",
        -20 => "暂留",
        -21 => "包含屏蔽词",
    );
 // XinggeApp操作返回码
    public static $xinggeOpCode = array(
        // xingge平台定义错误
        -1 =>"参数错误",
        -2 =>"请求时间戳不在有效期内 ",
        -3 =>"sign校验无效，检查access id和secret key(注意不是access key)",
        2  =>"参数错误",
        7  =>"别名/账号绑定的终端数满了（10个）",
        15 =>"信鸽逻辑服务器繁忙",
        19 =>"操作时序错误 ",
        20 =>"鉴权错误，可能是由于Access ID和Access Key不匹配 ",
        48 =>"推送的账号没有在信鸽中注册 ",
        71 =>"APNS服务器繁忙 ",
        73 =>"消息字符  数超限 ",
        76 =>"请求过于频繁，请稍后再试 ",
        100=>"APNS证书错误。请重新提交正确的证书 ",
        );

    // 消息推送类型
    const TEXT_MSG_PUSH = 1; // 短信推送
    const APP_PUSH_ALL = 2;  // App推送所有用户
    const APP_PUSH_USERS = 3;  // App 指定用户推送
    const APP_PUSH_ANDROID = 4;  // App Android推送
    const APP_PUSH_IOS = 5;  // App IOS 推送

    // 任务状态
    const STATUS_INIT = 0; // 初始
    const STATUS_APPROVAL = 1; // 审核通过
    const STATUS_REJECTED = 2; // 审核不通过
    const STATUS_SENT_SUCCESS = 3; // 发送成功
    const STATUS_SENT_FAILED = 4; // 发送失败

    // 任务类型
    public static $taskTypeDesc = array(
        self::TEXT_MSG_PUSH => "短信推送",
        self::APP_PUSH_ALL => "App推送-所有用户",
        self::APP_PUSH_USERS => "App推送-指定用户",
        self::APP_PUSH_IOS => "App推送-IOS用户",
        self::APP_PUSH_ANDROID => "App推送-Android用户",
    );

    public static $statusDesc = array(
        self::STATUS_INIT => "初始",
        self::STATUS_APPROVAL => "审核通过",
        self::STATUS_REJECTED => "审核不通过",
        self::STATUS_SENT_SUCCESS => "发送成功",
        self::STATUS_SENT_FAILED => "发送失败",
    );

//    public static function primaryKey()
//    {
//        return ['task_id', 'task_type'];
//    }


	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%msg_push_task}}';
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
            [['task_type'], 'required', 'message' => '不能为空'],
            [['task_type'], 'number', 'integerOnly' => true, 'message' => '只能为整数'],
            [['msg_content'], 'string', 'max' => 70 ],
            [['receiver_list'], 'string', 'max' => 65535 ],
            [['expect_time'], 'string', 'max' => 32 ],
        ];
	}
}
