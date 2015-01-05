<?php
namespace frontend\controllers;


use common\exceptions\PayException;
use common\helpers\TimeHelper;
use common\models\BankConfig;
use common\models\Order;
use common\models\UserContact;
use common\services\LLPayService;
use Yii;
use yii\base\UserException;
use yii\web\ForbiddenHttpException;
use yii\filters\AccessControl;
use yii\db\Query;
use common\services\UserService;

use common\models\User;
use common\models\UserCaptcha;
use common\models\UserLoginLog;
use common\api\external\LLPay;

use common\services\PayService;
use common\models\UserBankCard;
use common\helpers\StringHelper;
use common\models\AppConfig;
use common\models\UserCharge;

/**
 * User controller
 */
class UserController extends BaseController
{
    protected $userService;
    protected $payService;
    protected $llPayService;

    /**
     * 构造函数中注入UserService的实例到自己的成员变量中
     * 也可以通过Yii::$container->get('userService')的方式获得
     */
    public function __construct($id, $module, UserService $userService, PayService $payService, LLPayService $llPayService,$config = [])
    {
        $this->userService = $userService;
        $this->payService = $payService;
        $this->llPayService = $llPayService;

        parent::__construct($id, $module, $config);
    }
    
    public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				// 除了下面的action其他都需要登录
				'except' => ['reg-get-code', 'register', 'login', 'logout', 'support-banks',
							 'reset-pwd-code', 'verify-reset-password', 'reset-password', 'state'],
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}

	/**
	 * 注册步骤一：手机号获取验证码
	 * 
	 * @name	获取注册验证码 [userRegGetCode]
	 * @uses	用户注册是拉取验证码
	 * @method	post
	 * @param	string $phone 手机号
	 * @author	yakehuang
	 */
	public function actionRegGetCode()
	{
		$phone = trim($this->request->post('phone'));
		if (User::findByPhone($phone)) {
			throw new UserException('该手机号已注册', 1001);
		}
		if ($this->userService->generateAndSendCaptcha(trim($phone), UserCaptcha::TYPE_REGISTER)) {
			return [
				'code' => 0,
				'result' => true
			];
		} else {
			throw new UserException('发送验证码失败，请稍后再试');
		}
	}
	
	/**
	 * 注册步骤二：验证手机号获和验证码，并设置登录密码
	 * 
	 * @name 注册 [userRegister]
	 * @method	post
	 * @param string $phone 手机号
	 * @param string $code 验证码
	 * @param string $password 密码
	 */
	public function actionRegister()
	{
		$phone = trim($this->request->post('phone'));
		$code = trim($this->request->post('code'));
		$password = $this->request->post('password');
        $type = $this->request->post('type','');
        $contact_id = $this->request->post('contact_id','');
        $valid_code = $this->request->post('valid_code','');

        if ($type !== '' && empty($contact_id)){
            throw new UserException('OPENID is null ... ');
        }

		if (!$this->userService->validatePhoneCaptcha($phone, $code, UserCaptcha::TYPE_REGISTER)) {
			throw new UserException('验证码错误或已过期');
		} else {
			$user = $this->userService->registerByPhone($phone, $password , $type , $contact_id ,$valid_code);
			if ($user) {
				// 注册成功后即登录
				if (Yii::$app->user->login($user)) {
					// 记录登录日志
					$loginLog = new UserLoginLog();
					$loginLog->user_id = $user->id;
					$loginLog->created_at = time();
					$loginLog->created_ip = $this->request->getUserIP();
					$loginLog->source = $this->client->serialize();
					$loginLog->type = UserLoginLog::TYPE_NORMAL;
					$loginLog->save();
				}
				// 重新查一下，避免很多字段为null
				$user = User::findByPhone($phone);
				UserCaptcha::deleteAll(['phone' => $phone, 'type' => UserCaptcha::TYPE_REGISTER]);
				return [
					'code' => 0,
					'user' => [
						'uid' => $user->id,
						'username' => $user->username,
						'realname' => $user->realname,
						'id_card' => $user->id_card,
						'real_verify_status' => $user->real_verify_status,
						'card_bind_status' => $user->card_bind_status,
						'set_paypwd_status' => $user->userPayPassword ? 1 : 0,
						'is_novice' => $user->is_novice,
					],
					'account' => [
						'lastday_profits_date' => strtotime('-1 day'),
						'lastday_profits' => $user->account->getLastdayProfits(),
						'total_profits' => $user->account->total_profits,
						'total_money' => $user->account->total_money,
						'hold_money' => $user->account->getTotalHoldMoney(),
						'remain_money' => $user->account->usable_money + $user->account->withdrawing_money,
						'trade_count' => $user->getInvestCount(),
					],
					'sessionid' => Yii::$app->session->getId(),
				];
			} else {
				throw new UserException('注册失败，请稍后重试');
			}
		}
	}
	
	/**
	 * 登录
	 * 
	 * @name 登录 [userLogin]
	 * @method post
	 * @param string $username 用户名，手机注册的为手机号
	 * @param string $password 密码
	 */
	public function actionLogin()
	{
		$username = trim($this->request->post('username'));
		$password = $this->request->post('password');
		$openid = $this->request->post('OPENID');

		$user = User::findByUsername($username);
		if (!$user || !$user->validatePassword($password))
        {
			throw new UserException('用户名或密码错误');
		}
        else
        {
			if (Yii::$app->user->login($user))
            {
                $now = TimeHelper::Now();

                // 记录第三方登录
                if(!empty($openid))
                {
                    $userContact = UserContact::findOne([
                        'user_name' => $username,
                        'type' => 0,
                        'contact_id' => $openid
                    ]);

                    if(empty($userContact))
                    {
                        $userContact = new UserContact();
                        $userContact->user_id = $user->id;
                        $userContact->user_name = $user->username;
                        $userContact->type = 0;
                        $userContact->contact_id = $openid;
                        $userContact->created_at = $now;
                        $userContact->updated_at = $now;
                        $userContact->save();
                    }
                    else
                    {
                        $userContact->updated_at = $now;
                        $userContact->save();
                    }

                }

				// 记录登录日志
				$loginLog = new UserLoginLog();
				$loginLog->user_id = $user->id;
				$loginLog->created_at = $now;
				$loginLog->created_ip = $this->request->getUserIP();
				$loginLog->source = $this->client->serialize();
				$loginLog->type = UserLoginLog::TYPE_NORMAL;
				$loginLog->save();

				return [
					'code' => 0,
					'user' => [
						'uid' => $user->id,
						'username' => $user->username,
						'realname' => $user->realname,
						'id_card' => $user->id_card,
						'real_verify_status' => $user->real_verify_status,
						'card_bind_status' => $user->card_bind_status,
						'set_paypwd_status' => $user->userPayPassword ? 1 : 0,
						'is_novice' => $user->is_novice,
					],
					'account' => [
						'lastday_profits_date' => strtotime('-1 day'),
						'lastday_profits' => $user->account->getLastdayProfits(),
						'total_profits' => $user->account->total_profits,
						'total_money' => $user->account->total_money,
						'hold_money' => $user->account->getTotalHoldMoney(),
						'remain_money' => $user->account->usable_money + $user->account->withdrawing_money,
						'trade_count' => $user->getInvestCount(),
					],
					'sessionid' => Yii::$app->session->getId(),
				];
			} else {
				throw new UserException('登录失败，请稍后再试');
			}
		}
	}
	
	/**
	 * 退出
	 * 
	 * @name 退出 [userLogout]
	 */
	public function actionLogout()
	{
		return [
			'code' => 0,
			'result' => Yii::$app->user->logout()
		];
	}
	
	/**
	 * 实名认证
	 * @TODO 限制用户认证请求频率
	 * 
	 * @name 实名认证 [userRealVerify]
	 * @method post
	 * @param string $realname 实名
	 * @param string $id_card 身份证
	 */
	public function actionRealVerify()
	{
		$realname = trim($this->request->post('realname'));
		$id_card = trim($this->request->post('id_card'));
		
		// 已验证通过的无需再验证
		$currentUser = Yii::$app->user->identity;
		if ($currentUser->getIsRealVerify()) {
			throw new UserException('您已认证通过，无需重复认证');
		} else if (User::findOne(['id_card' => $id_card])) {
			throw new UserException('该身份证已使用，如被盗用，请联系客服');
		}
		
		$result = $this->userService->realnameVerify($realname, $id_card);
		
		if ($result === false) {
			throw new UserException('姓名和身份证不对应');
		} else {
			// 更新当前用户信息
			$currentUser->realname = $result['realname'];
			$currentUser->id_card = $result['id_card'];
			$currentUser->sex = $result['sex'];
			$currentUser->birthday = $result['birthday'];
			$currentUser->real_verify_status = User::REAL_STATUS_YES;
			$currentUser->save();
			
			return [
				'code' => 0,
				'result' => $result
			];
		}
	}
	
	/**
	 * 修改登录密码
	 *
	 * @name 修改登录密码 [userChangePwd]
	 * @method post
	 * @param string $old_pwd 原密码
	 * @param string $new_pwd 新密码
	 */
	public function actionChangePwd()
	{
		$oldPwd = $this->request->post('old_pwd');
		$newPwd = $this->request->post('new_pwd');
	
		$curUser = Yii::$app->user->identity;
		if ($curUser->validatePassword($oldPwd)) {
			if ($this->userService->resetPassword($curUser, $newPwd)) {
				return [
					'code' => 0,
					'result' => true
				];
			} else {
				throw new UserException('重设失败，请稍后再试');
			}
		} else {
			throw new UserException('原密码错误');
		}
	}
	
	/**
	 * 初次设置交易密码
	 *
	 * @name 初次设置交易密码 [userSetPaypassword]
	 * @method post
	 * @param string $password 交易密码
	 */
	public function actionSetPaypassword()
	{
		$password = $this->request->post('password');
		$currentUser = Yii::$app->user->identity;
		if (!$currentUser->getIsRealVerify()) {
			throw new UserException('请先实名认证');
		} else if (!$currentUser->getIsBindCard()) {
			throw new UserException('请先绑定银行卡');
		} else if ($currentUser->userPayPassword) {
			throw new UserException('您已经设置了交易密码');
		}
	
		if ($this->userService->setPayPassword($currentUser, $password)) {
			return [
				'code' => 0,
				'result' => true,
			];
		} else {
			throw new UserException('设置失败，请稍后再试');
		}
	}
	
	/**
	 * 修改交易密码
	 *
	 * @name 修改交易密码 [userChangePaypassword]
	 * @method post
	 * @param string $old_pwd 原密码
	 * @param string $new_pwd 新密码
	 */
	public function actionChangePaypassword()
	{
		$oldPwd = $this->request->post('old_pwd');
		$newPwd = $this->request->post('new_pwd');
	
		$curUser = Yii::$app->user->identity;
		if ($curUser->validatePayPassword($oldPwd)) {
			if ($this->userService->setPayPassword($curUser, $newPwd)) {
				return [
					'code' => 0,
					'result' => true
				];
			} else {
				throw new UserException('重设失败，请稍后再试');
			}
		} else {
			throw new UserException('原密码错误');
		}
	}
	
	/**
	 * 获取找回登录密码/交易密码的验证码
	 * 
	 * @name 获取找回登录密码/交易密码的验证码 [userResetPwdCode]
	 * @method post
	 * @param string $phone 手机号
	 * @param string $type 类型：找回登录密码find_pwd，找回交易密码find_pay_pwd
	 */
	public function actionResetPwdCode()
	{
		$phone = trim($this->request->post('phone'));
		$type = trim($this->request->post('type'));
		
		$user = User::findByPhone($phone);
		if (!$user) {
			throw new UserException('无此用户');
		} else if (!in_array($type, [UserCaptcha::TYPE_FIND_PWD, UserCaptcha::TYPE_FIND_PAY_PWD])) {
			throw new UserException('参数错误');
		} else if ($type == UserCaptcha::TYPE_FIND_PAY_PWD && (!$user->getIsRealVerify() || !$user->getIsBindCard())) {
			throw new UserException('请先实名认证和绑定银行卡');
		}
		
		// 找回交易密码需要验证是否登录以及手机号是否一致
		if ($type == UserCaptcha::TYPE_FIND_PAY_PWD) {
			if (Yii::$app->user->getIsGuest()) {
				throw new ForbiddenHttpException();
			} else if (Yii::$app->user->identity->phone != $phone) {
				throw new UserException('您输入的手机号与注册手机号不一致');
			}
		}
		
		if ($this->userService->generateAndSendCaptcha($phone, $type)) {
			return [
				'code' => 0,
				'result' => true,
				'real_verify_status' => $user->real_verify_status,
			];
		} else {
			throw new UserException('发送验证码失败，请稍后再试');
		}
	}
	
	/**
	 * 找回登录密码/交易密码验证用户和手机验证码
	 * 注：实名认证了的用户 还需要提交实名和身份证
	 * 
	 * @name 找回登录密码/交易密码验证用户和手机验证码 [userVerifyResetPassword]
	 * @method post
	 * @param string $phone 手机号
	 * @param string $realname 实名（非实名认证用户可不传）
	 * @param string $id_card 身份证（非实名认证用户可不传）
	 * @param string $code 验证码
	 * @param string $type 类型：找回登录密码find_pwd，找回交易密码find_pay_pwd
	 */
	public function actionVerifyResetPassword()
	{
		$phone = trim($this->request->post('phone'));
		$code = trim($this->request->post('code'));
		$type = trim($this->request->post('type'));
		
		$user = User::findByPhone($phone);
		if (!$user) {
			throw new UserException('无此用户');
		} else if (!in_array($type, [UserCaptcha::TYPE_FIND_PWD, UserCaptcha::TYPE_FIND_PAY_PWD])) {
			throw new UserException('参数错误');
		} else if ($type == UserCaptcha::TYPE_FIND_PAY_PWD && (!$user->getIsRealVerify() || !$user->getIsBindCard())) {
			throw new UserException('请先实名认证和绑定银行卡');
		}
		
		// 找回交易密码需要验证是否登录以及手机号是否一致
		if ($type == UserCaptcha::TYPE_FIND_PAY_PWD) {
			if (Yii::$app->user->getIsGuest()) {
				throw new ForbiddenHttpException();
			} else if (Yii::$app->user->identity->phone != $phone) {
				throw new UserException('您输入的手机号与注册手机号不一致');
			}
		}
		
		if (!$this->userService->validatePhoneCaptcha($phone, $code, $type)) {
			throw new UserException('验证码错误或已过期');
		} else {
			if ($user->getIsRealVerify()) {
				$realname = $this->getRequest()->post('realname');
				$idCard = $this->getRequest()->post('id_card');
				if ($user->realname != $realname || $user->id_card != $idCard) {
					throw new UserException('实名或身份证错误');
				}
			}
			return [
				'code' => 0,
				'result' => true,
			];
		}
	}
	
	/**
	 * 找回登录密码时设置新密码
	 * 注：实名认证了的用户 还需要提交实名和身份证
	 * 
	 * @name 找回登录密码时设置新密码 [userResetPassword]
	 * @method post
	 * @param string $phone 手机号
	 * @param string $realname 实名（非实名认证用户可不传）
	 * @param string $id_card 身份证（非实名认证用户可不传）
	 * @param string $code 验证码
	 * @param string $password 密码
	 */
	public function actionResetPassword()
	{
		$phone = trim($this->request->post('phone'));
		$code = trim($this->request->post('code'));
		$password = $this->request->post('password');
		
		$user = User::findByPhone($phone);
		if (!$user) {
			throw new UserException('无此用户');
		}
		
		if (!$this->userService->validatePhoneCaptcha($phone, $code, UserCaptcha::TYPE_FIND_PWD)) {
			throw new UserException('验证码错误或已过期');
		} else {
			if ($user->getIsRealVerify()) {
				$realname = $this->getRequest()->post('realname');
				$idCard = $this->getRequest()->post('id_card');
				if ($user->realname != $realname || $user->id_card != $idCard) {
					throw new UserException('实名或身份证错误');
				}
			}
			
			if ($this->userService->resetPassword($user, $password)) {
				UserCaptcha::deleteAll(['phone' => $phone, 'type' => UserCaptcha::TYPE_FIND_PWD]);
				return [
					'code' => 0,
					'result' => true
				];
			} else {
				throw new UserException('重设失败，请稍后再试');
			}
		}
	}
	
	/**
	 * 找回交易密码时设置新密码
	 * 注：实名认证了的用户 还需要提交实名和身份证
	 *
	 * @name 找回交易密码时设置新密码 [userResetPaypassword]
	 * @method post
	 * @param string $phone 手机号
	 * @param string $realname 实名
	 * @param string $id_card 身份证
	 * @param string $code 验证码
	 * @param string $password 密码
	 */
	public function actionResetPaypassword()
	{
		$phone = trim($this->request->post('phone'));
		$code = trim($this->request->post('code'));
		$password = $this->request->post('password');
		
		$user = User::findByPhone($phone);
		if (!$user) {
			throw new UserException('无此用户');
		} else if (!$user->getIsRealVerify() || !$user->getIsBindCard()) {
			throw new UserException('请先实名认证和绑定银行卡');
		} else if (Yii::$app->user->identity->phone != $phone) {
			throw new UserException('您输入的手机号与注册手机号不一致');
		}
		
		if (!$this->userService->validatePhoneCaptcha($phone, $code, UserCaptcha::TYPE_FIND_PAY_PWD)) {
			throw new UserException('验证码错误或已过期');
		} else {
			if ($user->getIsRealVerify()) {
				$realname = $this->getRequest()->post('realname');
				$idCard = $this->getRequest()->post('id_card');
				if ($user->realname != $realname || $user->id_card != $idCard) {
					throw new UserException('实名或身份证错误');
				}
			}
				
			if ($this->userService->setPayPassword($user, $password)) {
				UserCaptcha::deleteAll(['phone' => $phone, 'type' => UserCaptcha::TYPE_FIND_PAY_PWD]);
				return [
					'code' => 0,
					'result' => true
				];
			} else {
				throw new UserException('重设失败，请稍后再试');
			}
		}
	}

    /**
     * 用户支付
     *
     * @name 用户支付  [pay]
     * @method post
     * @param string $pay_amount 支付金额
     * @param string $pay_password 支付密码
     */
    public function actionPay()
    {
        // 登录检查
        $curUser = Yii::$app->user->identity;

        // 实名认证检查
        if ( empty($curUser->realname)
            or empty($curUser->username)
            or empty($curUser->id_card))
        {
            throw new UserException("您还没有实名认证");
        }

        // 是否已经绑定银行卡
        if ( !$curUser->card_bind_status ){
            throw new UserException("您还没有绑定银行卡");
        }

        $pay_amount = $this->request->post("pay_amount");
        $pay_amount = floatval($pay_amount);
        if( empty($pay_amount) or $pay_amount < 0.01){
            throw new UserException("您输入的金额有误");
        }

        // “分” 转化成 “元”
        $pay_amount = StringHelper::safeConvertCentToInt($pay_amount);

        $pay_password = $this->request->post("pay_password");
        if( empty($pay_password)){
            throw new UserException("请输入支付密码");
        }

        if(!$curUser->validatePayPassword($pay_password)){
            throw new UserException("支付密码验证错误");
        }

        return $this->payService->pay(
            $pay_amount,
            $curUser->username,
            $this->client->clientType
        );

    }
    

	/**
	 * 绑定银行卡
	 * 
	 * @name 绑定银行卡  [userBindCard]
     * @param string $bank_card 用户银行卡卡号
     * @param string $bank_id
	 * @method post
	 */
	public function actionBindCard()
	{
		$curUser = Yii::$app->user->identity;
		$code = 0;
        $payParams = [];
        $no_order = "";
        $now = TimeHelper::Now();
        $updated_time = TimeHelper::Now();

		if ($curUser->card_bind_status )
        {
            $bank = UserBankCard::findOne(['user_id' => $curUser->id]);
            $msg = "亲，您已经绑定了银行卡(".StringHelper::blurCardNo($bank->card_no).")";
		}
        else
        {
            // 检查是否已经实名认证
            if ( empty($curUser->realname) ) {
                throw new UserException("您还没有实名认证");
            }

            if ( empty($curUser->username) ) {
                throw new UserException("您还没有实名认证");
            }

            if ( empty($curUser->id_card) ) {
                throw new UserException("您还没有实名认证");
            }

            $bank_card = $this->request->post("bank_card");

            if ( empty($bank_card) ) {
                throw new UserException("缺少 bank_card 参数");
            }

            $bank_id = $this->request->post("bank_id");

            $bind_phone = $curUser->username;

            if ( empty($bank_id) ) {
                throw new UserException("缺少 bank_id 参数");
            }

            $bankConfig = BankConfig::findOne([
                'bank_id' => $bank_id,
                'status' => 0,
            ])->toArray();

            if( empty($bankConfig) or empty($bankConfig['third_platform']) ) {
                PayException::throwCodeExt(2100);
            }

            $db = Yii::$app->db;

            $sql = "select * from ". UserBankCard::tableName() . " ubc " .
                " where ubc.user_id={$curUser->id}";

            $existBindBank = $db->createCommand($sql)->queryOne();

            if ( $bankConfig['third_platform'] == BankConfig::PLATFORM_UMPAY )
            {
                // 联动支付，直接绑卡
                $ret = $this->payService->userBindCard(
                    $curUser->id,
                    $curUser->realname,
                    $bank_card,
                    $curUser->id_card,
                    $bind_phone
                );

            }
            else if( $bankConfig['third_platform'] == BankConfig::PLATFORM_LLPAY )
            {
                // 连连支付
                if( empty( $existBindBank ) or empty( $existBindBank['no_order'] )
                    or ( TimeHelper::Now() - $existBindBank['updated_at'] > 60 * ( LLPayService::VALID_ORDER_LIMIT - 1 ) ) // 提前1分钟
                )
                {
                    // 没有绑卡申请，或者申请过期，都需要生成一个订单id，之后客户端要传给连连
                    $no_order = Order::generateOrderId();
                }
                else
                {
                    // 之前有过申请
                    $no_order = $existBindBank['no_order'];
                    $updated_time = $existBindBank['updated_at'];
                }

                $ret = $this->llPayService->userBindCard(
					$curUser->id,
                    $no_order,
                    date("YmdHis",$updated_time)
                );

                $ret['payParams']['user_id'] = strval($curUser->username);
                $ret['payParams']['id_type'] = "0";
                $ret['payParams']['id_no'] = strval($curUser->id_card);
                $ret['payParams']['card_no'] = strval($bank_card);
                $ret['payParams']['acct_name'] = strval($curUser->realname);

            }
            else
            {
                // 不支持的支付平台
                PayException::throwCodeExt(2101);
                return [ 'code' => 2101 ];
            }

            $code = $ret['code'];
            $msg = empty($ret['message']) ? "" : $ret['message'];
            $status = $ret['status'];
            $bindResult = $ret['bindResult'];
            $payParams = $ret['payParams'];
            // 本地DB 保存绑卡数据
            $curUser->card_bind_status = $status;
            $curUser->save();

            if( empty($existBindBank) )
            {
                $db->createCommand()->insert(UserBankCard::tableName(),[
                    "user_id" => $curUser->id,
                    "bank_id" => $bank_id,
                    "bank_name" => $bankConfig['bank_name'],
                    "status" => $status,
                    "third_platform" => $bankConfig['third_platform'],
                    "bind_result" => json_encode($bindResult),
                    "card_no" => $bank_card,
                    "no_order" => $no_order,
                    "bind_phone" => $bind_phone,
                    "updated_at" => $updated_time,
                    "created_at" => $now,
                ])->execute();
            }
            else
            {
                $db->createCommand()->update(UserBankCard::tableName(),[
                    "status" => $status,
                    "third_platform" => $bankConfig['third_platform'],
                    "bind_result" => json_encode($bindResult),
                    "card_no" => $bank_card,
                    "bind_phone" => $bind_phone,
                    "no_order" => $no_order,
                    "updated_at" => $updated_time,
                ],[
                    "user_id" => $curUser->id,
                    "id" => $existBindBank['id']
                ])->execute();
            }
            $bank = $db->createCommand($sql)->queryOne();
        }
		
		return [
			'code' => $code,
            'message' => $msg,
			'bank' => $bank,
			'payParams' => $payParams,
		];
	}


    /**
     *
     * @name 解绑银行卡 [unBindCard]
     * @method post
     * @uses App个人中心，解绑银行卡操作，发起请求
     */
    public function actionUnBindCard()
    {
        $curUser = Yii::$app->user->identity;
        $bank = UserBankCard::findOne(['user_id' => $curUser->id]);

        if( !$curUser->card_bind_status )
        {
            throw new UserException("您尚未绑定银行卡");
        }

        $ret = $this->payService->unBindCard($bank['bind_phone']);

        if($ret['code'] == 0 or $ret['code'] == "00060064" )
        {
            $bank->status = UserBankCard::STATUS_UNBIND;
            $curUser->card_bind_status = UserBankCard::STATUS_UNBIND;
            $curUser->save();
            $bank->save();
        }

        return $ret;
    }

    /**
     *
     * @name 连连支付测试 [LLPayTest]
     * @method post
     * @uses 连连支付测试
     */
    public function actionLLPayTestCard()
    {
        return LLPay::wapPay();
    }
	
	/**
	 * 获得所有支持绑卡的银行
	 * 
	 * @name 获得支持绑卡的银行 [userSupportBanks]
	 */
	public function actionSupportBanks()
	{
        $db = Yii::$app->db;
        $sql = "select "."
                bank_id code,
                bank_name name,
                sml,
                dml,
                dtl,
                third_platform
                from tb_bank_config
                where status = 0;
                ";
        $banks = $db->createCommand($sql)->queryAll();
		return [
			'code' => 0,
			'banks' => $banks,
		];
	}
	
	/**
	 * 获得用户绑定的银行卡
	 *
	 * @name 获得用户绑定的银行卡 [userCards]
	 * @return array
	 */
	public function actionCards()
	{
		$curUser = Yii::$app->user->identity;
		$cards = UserBankCard::find()->select(
			['bank_id', 'bank_name', 'card_no']
		)->where([
			'user_id' => $curUser->id,
			'status' => UserBankCard::STATUS_BIND
		])->asArray()->all();

		foreach ($cards as &$v)
		{
			//$bankInfo = UserBankCard::getBankInfo($v['bank_id']);
			$bankInfo = BankConfig::findOne(['bank_id' => $v['bank_id']]);

			$v['sml'] = isset($bankInfo['sml']) ? $bankInfo['sml'] : '';
			$v['dml'] = isset($bankInfo['dml']) ? $bankInfo['dml'] : '';
			$v['dtl'] = isset($bankInfo['dtl']) ? $bankInfo['dtl'] : '';
		}
		return [
			'code' => 0,
			'unbind_tips' => '为了您的资金安全，解绑银行卡请拨打口袋理财客服热线：' . AppConfig::getConfig('callCenter') . '，我们将会在核实您的身份信息后协助您操作。',
			'cards' => $cards
		];
	}
	
	/**
	 * 获得登录用户基本信息
	 * 
	 * @name 获得登录用户基本信息 [userInfo]
	 */
	public function actionInfo()
	{
		$curUser = Yii::$app->user->identity;
		return [
			'code' => 0,
			'base_info' => [
				'uid' => $curUser->id,
				'username' => $curUser->username,
				'realname' => $curUser->realname,
				'id_card' => $curUser->id_card,
				'real_verify_status' => $curUser->real_verify_status,
				'card_bind_status' => $curUser->card_bind_status,
				'set_paypwd_status' => $curUser->userPayPassword ? 1 : 0,
				'is_novice' => $curUser->is_novice,
			],
		];
	}

	/**
	 * 获得未登录用户信息
	 * 
	 * @name 获得未登录用户信息 [userState]
	 * @method post
	 * @param string $phone 手机号
	 */
	public function actionState()
	{
		$phone = $this->request->post('phone');
		$user = User::findByPhone($phone);
		if (!$user) {
			throw new UserException('该用户不存在');
		} else {
			return [
				'code' => 0,
				'real_verify_status' => $user->real_verify_status,
			];
		}
	}

	/**
	 * 为用户账户充值
	 *
	 * @name 为用户账户充值 [userCharge]
	 * @method post
	 * @param string $amount 充值金额
	 * @param string $pay_password 交易密码
	 * @return array
	 */
	public function actionCharge()
	{
		$code = 0;
		$msg = "";
        $payParams = [];
		$curUser = Yii::$app->user->identity;

		$payPassword = $this->request->post('pay_password');
		$amount = intval($this->request->post('amount'));

		if(empty($amount))
		{
			PayException::throwCodeExt(2221);
		}

		if (!$curUser->real_verify_status) {
			throw new UserException('您还没有实名认证', 1001);
		} else if (!$curUser->card_bind_status) {
			throw new UserException('您还没有绑定银行卡', 1002);
		} else if (!$curUser->getUserPayPassword()) {
			throw new UserException('您还没有设置交易密码', 1003);
		} else if (!$curUser->validatePayPassword($payPassword)) {
			throw new UserException('交易密码错误', 1004);
		}


		$db = Yii::$app->db;

		$sql = "select * from ". UserBankCard::tableName() . " ubc " .
			" where ubc.user_id=\"{$curUser->id}\"";

		$existBindBank = $db->createCommand($sql)->queryOne();

		if( empty($existBindBank) )
		{
			PayException::throwCodeExt(2205);
		}

		// 根据不同平台做不同的操作
		if ( $existBindBank['third_platform'] == BankConfig::PLATFORM_UMPAY )
		{
			PayException::throwCodeExt(2206);
		}
		else if( $existBindBank['third_platform'] == BankConfig::PLATFORM_LLPAY )
		{
			$chargeResult = $this->llPayService->userCharge($curUser, $amount);
			$msg = $chargeResult['msg'];
			$code = $chargeResult['code'];
			$payParams = $chargeResult['payParams'];
		}
        else{
            PayException::throwCodeExt(2101);
        }

		return [
			'code' => $code,
			'message' => $msg,
			'payParams' => $payParams,
		];
	}


	/**
	 * 账户充值结果查询
	 *
	 * @name 账户充值结果 [userChargeQuery]
	 * @method post
	 * @param string $no_order 订单ID
	 * @param string $info_order 订单信息
	 * @return array
	 */
	public function actionChargeQuery()
	{
        $no_order = $this->request->post("no_order");
        if(empty($no_order)){
            PayException::throwCodeExt(2103);
        }

        // $info_order 不一定要非空
        $info_order = $this->request->post("info_order");

        $curUser = Yii::$app->user->identity;

        $chargeQueryResult = $this->llPayService->userChargeQuery($curUser,$no_order,$info_order);

        if( empty($chargeQueryResult) ) {
            PayException::throwCodeExt(2224);
        }

        return [
			'code' => 0,
			'message' => "充值成功",
		];
	}
	
	/**
	 * 充值记录
	 *
	 * @name 充值记录 [userChargeList]
	 * @param integer $page 第几页
	 * @param integer $pageSize 每页个数
	 */
	public function actionChargeList($page = 1, $pageSize = 15)
	{
		$page = $page > 1 ? intval($page) : 1;
		$pageSize = intval($pageSize);
		$offset = ($page - 1) * $pageSize;
		
		// 为保持和提现记录字段一致amount给客户端改为money
		$data = (new Query())->from(UserCharge::tableName())->select([
			'id', 'amount as money', 'status', 'created_at'
		])->where([
			'user_id' => Yii::$app->user->id,
		])->orderBy([
			'id' => SORT_DESC,
		])->offset($offset)->limit($pageSize)->all();
		
		foreach ($data as &$v) {
			$v['statusLabel'] = UserCharge::$status[$v['status']];
		}
		
		return [
			'code' => 0,
			'data' => $data,
		];
	}
}