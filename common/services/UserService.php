<?php

namespace common\services;

use Yii;
use yii\base\Object;
use yii\base\UserException;
use yii\base\Exception;
use common\services\AccountService;
use common\models\User;
use common\models\UserPassword;
use common\models\UserCaptcha;
use common\models\UserAccount;
use common\helpers\MessageHelper;
use common\models\UserPayPassword;
use common\models\UserContact;
use common\models\UserInvite;
use common\helpers\TimeHelper;
use common\activity\ExperienceMoneyAct;
use common\models\UserDetail;

/**
 * 用户基本模块service
 */
class UserService extends Object
{
	protected $accountService;
	
	public function __construct(AccountService $accountService, $config = [])
	{
		$this->accountService = $accountService;
		parent::__construct($config);
	}
	
	/**
	 * 生成验证码，并发送短信
	 * @return boolean
	 */
	public function generateAndSendCaptcha($phone, $type)
	{
		if (!preg_match(User::PHONE_PATTERN, $phone)) {
			throw new UserException('手机号格式错误');
		}
		$captcha = UserCaptcha::find()->where("phone = '{$phone}' and type = '{$type}'")->one();
		if ($captcha) { // 存在但是过期了，重新生成
			if ($captcha['expire_time'] < time()) {
				$captcha->captcha = rand(100000, 999999);
				$captcha->generate_time = time();
				$captcha->expire_time = $captcha->generate_time + UserCaptcha::EXPIRE_SPACE;
				$captcha->save();
			}
		} else { // 第一次生成
			$user = User::findByPhone($phone);
						
			$captcha = new UserCaptcha();
			$captcha->phone = $phone;
			$captcha->captcha = rand(100000, 999999);
			$captcha->type = $type;
			$captcha->user_id = $user ? $user->id : 0;
			$captcha->generate_time = time();
			$captcha->expire_time = $captcha->generate_time + UserCaptcha::EXPIRE_SPACE;
			$captcha->save();
		}
		
		if ($captcha && $captcha->captcha) {
			if (MessageHelper::sendSMS($phone, $captcha->getSMS())) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * 验证手机验证码
	 * @param string $phone
	 * @param string $code
	 * @param string $type
	 * @return boolean
	 */
	public function validatePhoneCaptcha($phone, $code, $type)
	{
		$result = UserCaptcha::findOne([
			'phone' => $phone,
			'captcha' => $code,
			'type' => $type,
		]);
		if ($result) {
			return time() <= $result->expire_time;
		}
		return false;
	}
	
	/**
	 * 注册
     * type = 0
	 */
	public function registerByPhone($phone, $password,$type = '',$contact_id = '' ,$valid_code = '')
	{
		$user = new User();
		$user->phone = $phone;
		$user->username = $phone;
		$user->created_at = time();
		$user->created_ip = Yii::$app->getRequest()->getUserIP();
		
		$userToken = new UserPassword();
		$userToken->password = $password;
        $now = TimeHelper::Now();
		// 先做完所有验证再save保证两个能同时保存成功
		if (!$user->validate()) {
			throw new UserException(array_shift($user->getFirstErrors()));
		} else if (!$userToken->validate()) {
			throw new UserException(array_shift($userToken->getFirstErrors()));
		} else {
			$user->generateAuthKey();
			$user->save();
			
			$userToken->user_id = $user->id;
			$userToken->password = Yii::$app->security->generatePasswordHash($userToken->password);
			$userToken->save(false);
			
			// 创建资金数据
			$account = new UserAccount();
			$account->user_id = $user->id;
			// 注册送体验金，先不上这个功能
// 			$account->kdb_experience_money = ExperienceMoneyAct::$config['money'];
// 			$account->total_money = ExperienceMoneyAct::$config['money'];
			$account->created_at = time();
			$account->updated_at = $account->created_at;
			$account->save();
			
			// 记录日志，并把计息开始时间和结束时间记录在remark字段
			if (!empty($account->kdb_experience_money)) {
				$startDate = date('Y-m-d', strtotime('+1 day'));
				$endDate = date('Y-m-d', strtotime('+' . ExperienceMoneyAct::$config['profits_time'] . ' day'));
				$isExtend = 0;
				$remark = json_encode(['startDate' => $startDate, 'endDate' => $endDate, 'isExtend' => $isExtend]);
				UserAccount::addLog($user->id, UserAccount::TRADE_TYPE_KDB_EXP_MONEY_IN, $account->kdb_experience_money, $remark);
			}
			
			// 保存用户详细信息
			$request = Yii::$app->getRequest();
			$userDetail = new UserDetail();
			$userDetail->user_id = $user->id;
			$userDetail->username = $user->username;
			$userDetail->reg_client_type = $request->client->clientType;
			$userDetail->reg_device_name = $request->client->deviceName;
			$userDetail->reg_app_version = $request->client->appVersion;
			$userDetail->reg_os_version = $request->client->osVersion;
			$userDetail->reg_app_market = $request->client->appMarket;
			$userDetail->save();

            // 中间关联表 0为关联微信账户
            if (!empty($contact_id) ){
                //UserContact::instance()->InsertDate($phone,$contact_id);
                $userContact = new UserContact();
                $userContact->user_id = $user->id;
                $userContact->user_name = $phone;
                $userContact->type = $type;
                $userContact->contact_id = $contact_id;
                $userContact->created_at = $now;
                $userContact->updated_at = $now;
                $userContact->save();
            }

            //如果有邀请码，保存关系数据表
            if (!empty($valid_code)){
                //解密邀请码
                $UserID = UserInvite::decrypt($valid_code);

                //保存数据
                $userInvite = new UserInvite();
                $userInvite->invite_uid = $UserID;
                $userInvite->new_uid    = $user->id;
                $userInvite->created_at = $now;
                $userInvite->save();
            }
			
			return $user;
		}
	}
	
	/**
	 * 实名认证
	 * @return boolean|array 成功返回用户信息，失败返回false
	 */
	public function realnameVerify($realname, $idCard)
	{
		$client = new \SoapClient('http://service.sfxxrz.com/IdentifierService.svc?wsdl');
		$request = array(
			'IDNumber' => $idCard,
			'Name' => $realname,
		);
		$cred = array(
			'UserName' => 'wcrz_admin',
			'Password' => 'I56pL8w2',
		);
		
		$resultJson = $client->SimpleCheckByJson(array(
			"request" => json_encode($request),
			'cred' => json_encode($cred),
		))->SimpleCheckByJsonResult;
		$result = json_decode($resultJson, true);
		
		if ($result && $result['ResponseText'] == '成功') {
			if ($result['Identifier']['Result'] == '一致') {
				// 返回用户数据
				$sex = User::SEX_NOSET;
				if ($result['Identifier']['Sex'] == '男性') {
					$sex = User::SEX_MALE;
				} else if ($result['Identifier']['Sex'] == '女性') {
					$sex = User::SEX_FEMALE;
				}
				$return = array(
					'realname' => $result['Identifier']['Name'],
					'id_card' => $result['Identifier']['IDNumber'],
					'sex' => $sex,
					'birthday' => $result['Identifier']['Birthday'],
				);
				return $return;
			} else {
				throw new Exception("认证失败，{$result['Identifier']['Result']}，如有疑问请联系客服");
			}
		} else {
			throw new Exception('认证失败，请稍后再试');
		}
	}
	
	/**
	 * 重置密码
	 */
	public function resetPassword(User $user, $password)
	{
		$userPassword = $user->userPassword;
		$userPassword->password = $password;
		if (!$userPassword->validate()) {
			throw new UserException(array_shift($userPassword->getFirstErrors()));
		} else {
			$userPassword->password = Yii::$app->security->generatePasswordHash($password);
			return $userPassword->save(false);
		}
	}
	
	/**
	 * 设置和修改交易密码
	 */
	public function setPayPassword(User $user, $password)
	{
		$userPayPwd = $user->userPayPassword;
		if (!$userPayPwd) {
			$userPayPwd = new UserPayPassword();
			$userPayPwd->user_id = $user->id;
		}
		$userPayPwd->password = $password;
		if (!$userPayPwd->validate()) {
			throw new UserException(array_shift($userPayPwd->getFirstErrors()));
		} else {
			$userPayPwd->password = Yii::$app->security->generatePasswordHash($password);
			return $userPayPwd->save(false);
		}
	}
	
	/**
	 * 操作是否需要验证码
	 * 暂定为用银行卡支付则需要验证码，否则不需要，后续还可以根据用户纬度来做
	 */
	public function optionNeedCaptcha(User $user, $type, $params)
	{
		$money = intval(bcmul($params['money'], 100));
		if ($params['use_remain'] == 0 || $money - $user->account->usable_money > 0) {
			return true;
		} else {
			return false;
		}
	}
}