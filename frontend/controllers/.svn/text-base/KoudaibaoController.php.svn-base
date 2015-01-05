<?php
namespace frontend\controllers;

use common\exceptions\PayException;
use common\models\BankConfig;
use common\models\UserBankCard;
use Yii;
use yii\db\Query;
use yii\base\UserException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\AccessControl;
use common\models\KdbInfo;
use common\services\ProjectService;
use common\models\KdbRolloutLog;
use common\models\KdbInvest;
use common\models\Order;
use common\helpers\StringHelper;
use common\models\UserCaptcha;

/**
 * Koudaibao controller
 */
class KoudaibaoController extends BaseController
{
	protected $projectService;
	
	public function __construct($id, $module, ProjectService $projectService, $config = [])
	{
		$this->projectService = $projectService;
		parent::__construct($id, $module, $config);
	}
	
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				// 除了下面的action其他都需要登录
				'except' => ['info', 'desc-detail', 'invest-list'],
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
	 * 获取口袋宝信息
	 * 
	 * @name 获取口袋宝信息 [kdbInfo]
	 */
	public function actionInfo()
	{
		$model = KdbInfo::findKoudai();
		if (!$model) {
			throw new NotFoundHttpException('访问的数据不存在');
		}
		$info = [
			"title" => $model->title,
			"total_money" => $model->total_money,
			'remain_money' => $model->curAccount ? $model->curAccount['cur_money'] : $model->total_money,
			"apr" => $model->apr,
			"summary" => $model->summary,
			"instruction" => $model->instruction,
			"status" => $model->status,
			"daily_invest_limit" => $model->daily_invest_limit,
			"daily_withdraw_limit" => $model->daily_withdraw_limit,
			"user_invest_limit" => $model->user_invest_limit,
			// 先后台写死吧
			"interest_desc" => '一个工作日后起息',
			"min_invest_money" => $model->min_invest_money,
			"product_type" => $model->product_type,
			"cur_invest_times" => isset($model->curAccount['history_invest_times']) ? $model->curAccount['history_invest_times'] : 0,
			"risk_control_managed" => KdbInfo::RISK_CONTROL_MANAGED,
			"risk_control_warrant" => KdbInfo::RISK_CONTROL_WARRANT,
			"risk_control_repay" => KdbInfo::RISK_CONTROL_REPAY,
			"bank_apr" => Yii::$app->params['bankApr'], // 银行活期利率
		];
		return [
			'code' => 0,
			'info' => $info,
		];
	}
	
	/**
	 * 项目概述内页H5
	 *
	 * @name 项目概述内页H5 [kdbDescDetail]
	 */
	public function actionDescDetail()
	{
		$this->response->format = Response::FORMAT_HTML;
		$model = KdbInfo::findKoudai();
		return $this->render('/page/detail', ['html' => $model->desc]);
	}
	
	/**
	 * 投资下单
	 * 
	 * @name 投资下单 [kdbInvestOrder]
	 * @method post
	 * @param integer $money 金额
	 */
	public function actionInvestOrder()
	{
		$money = intval(bcmul($this->request->post('money'), 100));
		if ($money <= 0) {
			throw new UserException('缺少必要参数');
		}
		
		$curUser = Yii::$app->user->identity;
		
		// 简单验证
		if (!$curUser->real_verify_status) {
			throw new UserException('您还没有实名认证', 1001);
		} else if (!$curUser->card_bind_status) {
			throw new UserException('您还没有绑定银行卡', 1002);
		} else if (!$curUser->getUserPayPassword()) {
			throw new UserException('您还没有设置交易密码', 1003);
		}
		
		$order = new Order();
		$order->order_id = Order::generateOrderId();
		$order->type = Order::TYPE_INVEST_KDB;
		$order->user_id = $curUser->id;
		$order->money = $money;
		if ($order->save()) {
			return [
				'code' => 0,
				'order_id'=> $order->order_id,
			];
		} else {
			throw new UserException('系统繁忙，请重试');
		}
	}
	
	/**
	 * 投资重新发验证码
	 * 
	 * @name 投资重新发验证码 [kdbInvestCaptcha]
	 */
	public function actionInvestCaptcha()
	{
		$curUser = Yii::$app->user->identity;
		$userService = Yii::$container->get('userService');
		if ($userService->generateAndSendCaptcha($curUser->username, UserCaptcha::TYPE_INVEST_KDB)) {
			return [
				'code' => 0,
				'result' => true
			];
		} else {
			throw new UserException('发送验证码失败，请稍后再试');
		}
	}
	
	/**
	 * 投资
	 * 
	 * @name	投资 [kdbInvest]
	 * @method	post
	 * @param	integer $use_remain 是否用余额，1或0
	 * @param	string $money 投资金额
	 * @param	string $pay_password 交易密码
	 * @param	string $order_id 订单ID
	 * @param	string $captcha 验证码
	 * @param	string $sign 签名：所有的post参数（除sign）按key的字母升序组成待签名字符串，比如“money=100&order_id=111&pay_password=123456&use_remain=1”（参数值需要urlencode），再末尾加上私钥，然后调用base64编码即可
	 */
	public function actionInvest()
	{
		$money = intval(bcmul($this->request->post('money'), 100));
		$payPassword = $this->request->post('pay_password');
		$useRemain = intval($this->request->post('use_remain'));
		$captcha = trim($this->request->post('captcha'));
		if ($money <= 0 || !$payPassword || !in_array($useRemain, [0, 1])) {
			throw new UserException('缺少必要参数');
		}
		
		// 判断用户状态：实名认证、银行卡绑定、交易密码
		$curUser = Yii::$app->user->identity;
		$userService = Yii::$container->get('userService');
	 	if (!$curUser->real_verify_status) {
			throw new UserException('您还没有实名认证', 1001);
		} else if (!$curUser->card_bind_status) {
			throw new UserException('您还没有绑定银行卡', 1002);
		} else if (!$curUser->getUserPayPassword()) {
			throw new UserException('您还没有设置交易密码', 1003);
		} else if (!$curUser->validatePayPassword($payPassword)) {
			throw new UserException('交易密码错误', 1004);
		}

		// 获取用户的绑定的银行卡
		$db = Yii::$app->db;

		$sql = "select * from ". UserBankCard::tableName() . " ubc " .
			" where ubc.user_id=\"{$curUser->id}\"";

		$existBindBank = $db->createCommand($sql)->queryOne();

		if( empty($existBindBank) )
		{
			PayException::throwCodeExt(2205);
		}

		// 根据不同平台做不同的操作
		if ($existBindBank['third_platform'] == BankConfig::PLATFORM_UMPAY)
		{
			// 如果是联动支付，看是否需要验证码
			if (!$captcha && $userService->optionNeedCaptcha($curUser, UserCaptcha::TYPE_INVEST_KDB, $this->request->post())) {
				$userService->generateAndSendCaptcha($curUser->username, UserCaptcha::TYPE_INVEST_KDB);
				throw new UserException('需要验证码，已经发送至您的手机', 2001);
			} else if ($captcha && !$userService->validatePhoneCaptcha($curUser->username, $captcha, UserCaptcha::TYPE_INVEST_KDB)) {
				throw new UserException('验证码错误或已过期', 2002);
			}
		}
		else if ($existBindBank['third_platform'] == BankConfig::PLATFORM_LLPAY)
		{
			// 如果是连连支付, 必须使用余额支付
			if( $useRemain == 0 )
			{
				PayException::throwCodeExt(2208);
			}
		}
		else
		{
			PayException::throwCodeExt(2101);
		}

		if (!($this->client->clientType == 'ios' && $this->client->appVersion == '1.0.1')) {
			// 验证签名
			$params = $this->request->post();
			$sign = $this->request->post('sign');
			if (!Order::validateSign($params, $sign)) {
				throw new UserException('请求签名无效，请按正常流程重试', 3001);
			}
			
			// 验证订单
			$order_id = $this->request->post('order_id');
			$order = Order::findOne(['order_id' => $order_id]);
			if (!$order) {
				throw new UserException('请求无效，请按正常流程重试', 3002);
			} else if (!$order->getCanCommit()) {
				throw new UserException('您的请求已处理完成，请勿重复提交', 3003);
			}
		}
		
		// 加锁，如果已经有锁，则抛异常失败
		$curUser->addTradeLock();
		try {
			$model = KdbInfo::findKoudai();
			$kdbAccount = $model->getCurAccount();
			if (!$model) {
				throw new UserException('无此项目');
			} else if ($model->status == KdbInfo::STATUS_OFF) {
				throw new UserException('口袋宝暂停投资');
			} else if ($money < $model->min_invest_money) {
				throw new UserException('投资金额不能小于起购金额：' . ($model->min_invest_money / 100) .'元');
			} else if (isset($kdbAccount['cur_money']) && $kdbAccount['cur_money'] <= 0) {
				throw new UserException('项目已投满，敬请下一期');
			} else if ($money > $model->total_money || (isset($kdbAccount['cur_money']) && $money > $kdbAccount['cur_money'])) {
				throw new UserException('可申购金额不足，请刷新产品重试');
			} else if ($money > $model->daily_invest_limit - $curUser->account->getTodayKdbInvestTotal()) {
				throw new UserException('您已超过单日投资限额，请明天再试');
			} else if ($money > $model->user_invest_limit - $curUser->account->kdb_total_money) {
				throw new UserException('您已超过单个用户可投口袋宝总额');
			}
			
			$result = false;
			if ($useRemain == 0) { // 全用银行卡
				$result = $this->projectService->investKdb($money, $money);
			} else {
				if ($money - $curUser->account->usable_money > 0) {
					$invest_pay_money = $money - $curUser->account->usable_money;
				} else {
					$invest_pay_money = 0;
				}
				$result = $this->projectService->investKdb($money, $invest_pay_money);
			}
			
			if ($result) {
				if (!($this->client->clientType == 'ios' && $this->client->appVersion == '1.0.1')) {
					$order->status = Order::STATUS_SUCCESS;
					$order->save(false);
				}
				// 删除验证码
				UserCaptcha::deleteAll(['phone' => $curUser->username, 'type' => UserCaptcha::TYPE_INVEST_KDB]);
				// 释放锁
				$curUser->releaseTradeLock();
				
				$investInfo = [
					'invest' => [
						'project_type_desc' => $model->title,
						'project_name' => $model->title,
						'apr' => $model->apr,
						'invest_money' => $money,
						'date' => date("Y-m-d H:i", time()),
					],
					'start' => [
						'date' => date('n月j日', time() + 24 * 3600),
						'desc' => "开始计算收益",
					],
					'end' => [
						'date' => date('n月j日', time() + 48 * 3600),
						'desc' => "收益到账",
					],
				];
				
				return [
					'code' => 0,
					'result' => $result,
					'investInfo' => $investInfo
				];
			} else {
				throw new UserException('投资失败，请稍后再试');
			}
		} catch (\Exception $e) {
			if (!($this->client->clientType == 'ios' && $this->client->appVersion == '1.0.1')) {
				$order->status = Order::STATUS_FAILED;
				$order->save(false);
			}
			$curUser->releaseTradeLock();
			throw $e;
		}
	}
	
	/**
	 * 转出
	 * 
	 * @name	转出 [kdbRollout]
	 * @method	post
	 * @param	string $money 转出金额
	 * @param	string $pay_password 交易密码
	 */
	public function actionRollout()
	{
		$money = intval(bcmul($this->request->post('money'), 100));
		$payPassword = $this->request->post('pay_password');
		if ($money <= 0 || !$payPassword) {
			throw new UserException('缺少必要参数');
		}
		
		$curUser = Yii::$app->user->identity;
		
		$curUser->addTradeLock();
		try {
			if (!$curUser->getUserPayPassword()) {
				throw new UserException('您还没有设置交易密码');
			} else if (!$curUser->validatePayPassword($payPassword)) {
				throw new UserException('交易密码错误');
			} else if ($money > $curUser->account->kdb_total_money) {
				throw new UserException('转出金额大于您现有总额');
			} else {
				$kdb = KdbInfo::findKoudai();
				if ($money > $kdb->daily_withdraw_limit - $curUser->account->getTodayRolloutTotal()) {
					throw new UserException('您已超过单日转出限额，请明天再试');
				}
			}
			
			$result = $this->projectService->rollout($money);
			$curUser->releaseTradeLock();
			
			return [
				'code' => 0,
				'result' => $result,
			];
		} catch (\Exception $e) {
			$curUser->releaseTradeLock();
			throw $e;
		}
	}
	
	/**
	 * 转出记录
	 * 
	 * @name 转出记录 [kdbRolloutList]
	 * @param integer $page 第几页
	 * @param integer $pageSize 每页个数
	 */
	public function actionRolloutList($page = 1, $pageSize = 10)
	{
		$curUser = Yii::$app->user->identity;
		
		$page = $page > 1 ? intval($page) : 1;
		$pageSize = intval($pageSize);
		$offset = ($page - 1) * $pageSize;
		$rollouts = (new Query())->from(KdbRolloutLog::tableName())->select([
			'id', 'username', 'money', 'created_at',
		])->where([
			'user_id' => $curUser->id,
		])->orderBy([
			'id' => SORT_DESC,
		])->offset($offset)->limit($pageSize)->all();
		
		return [
			'code' => 0,
			'rollouts' => $rollouts,
		];
	}
	
	/**
	 * 投资记录列表
	 *
	 * @name 投资记录列表 [kdbInvestList]
	 * @param integer $page 第几页
	 * @param integer $pageSize 每页个数
	 */
	public function actionInvestList($page = 1, $pageSize = 10)
	{
		$page = $page > 1 ? intval($page) : 1;
		$pageSize = intval($pageSize);
		$offset = ($page - 1) * $pageSize;
		$invests = (new Query())->from(KdbInvest::tableName())->select([
			'id', 'username', 'invest_money', 'created_at', 'status'
		])->orderBy([
			'id' => SORT_DESC,
		])->offset($offset)->limit($pageSize)->all();
	
		foreach ($invests as $k => $v) {
			$invests[$k]['username'] = StringHelper::blurPhone($v['username']);
			$invests[$k]['statusLabel'] = KdbInvest::$status[$v['status']];
		}
	
		return [
			'code' => 0,
			'page' => $page,
			'pageSize' => $pageSize,
			'invests' => $invests,
		];
	}
	
	/**
	 * 获得口袋宝投资前置信息
	 * 
	 * @name 获得口袋宝投资前置信息 [kdbTodayRemain]
	 */
	public function actionTodayRemain()
	{
		$curUser = Yii::$app->user->identity;
		$kdb = KdbInfo::findKoudai();
		$daily_remain = $kdb->daily_invest_limit - $curUser->account->getTodayKdbInvestTotal();
		$total_remain = $kdb->user_invest_limit - $curUser->account->kdb_total_money;
		$today_remain = min($daily_remain, $total_remain);
		return [
			'code' => 0,
			'today_remain' => $today_remain > 0 ? $today_remain : 0,
			'usable_money' => $curUser->account->usable_money,
		];
	}
}