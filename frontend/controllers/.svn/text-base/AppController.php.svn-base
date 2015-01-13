<?php
namespace frontend\controllers;


use common\services\PayService;
use Yii;
use yii\base\UserException;
use common\models\Setting;
use common\models\KdbInfo;
use common\models\Project;
use common\models\DeviceInfo;
use common\models\DeviceVisitInfo;
use common\models\UserWithdraw;
use common\models\User;

/**
 * App controller
 */
class AppController extends BaseController
{
	/**
	 * 下发配置
	 * 
	 * @name 下发配置 [getConfig]
	 * @param string $configVersion 配置版本号
	 * @uses 用于客户端获取url配置
	 */
    public function actionConfig($configVersion)
    {
        $confVer = strtotime('2015-01-13 20:00:01');
        if ($configVersion == $confVer) {
            throw new UserException('配置无更新');
        }
        $baseUrl = $this->getRequest()->getHostInfo() . $this->getRequest()->getBaseUrl();
        $config = [
            'code'				=> 0,
            'name'				=> '口袋理财',
            'configVersion'		=> $confVer,
            'iosVersion'		=> Yii::$app->params['appConfig']['iosVersion'],
            'androidVersion'	=> Yii::$app->params['appConfig']['androidVersion'],
            'siteUrl'			=> 'www.koudailc.com',
            'callCenter'		=> '400-002-0802',
            'callQQGroup'		=> '421985497',
            'companyAddress'	=> '上海市杨浦区淞沪路303号',
            'companyEmail'		=> 'hr@koudailc.com',
            'companyAbout'		=> '口袋理财成立于2014年12月，注册资金500万元。公司以改善普通大众理财习惯为己任，以丰富普通大众理财渠道及保障投资人债权权益为企业核心价值观，旨在通过互联网技术和严格的风险控制管理技术为广大投资者带来更高、更稳健的投资收益。让普惠金融惠及更多的人民群众，实现金融民主化的目标。',
            'warrantWords'		=> '工商银行监管风险准备金',
            // ios是否展示启动广告
            'showLaunchImg'		=> 1,
            // android是否展示启动广告
            'showLaunchImgArd'	=> 0,
            'dataUrl'			=> [
                'getIndex' => "{$baseUrl}/app/index",
                'getLaunchImg' => "{$baseUrl}/app/launch-img",
                'appDeviceReport' => "{$baseUrl}/app/device-report",
                'appUpgrade' => "{$baseUrl}/app/upgrade",
                'appMarketList' => "{$baseUrl}/app/market-list",
                'pageAddSuggest' => "{$baseUrl}/page/add-suggest",
                'pageHelpList' => "{$baseUrl}/page/help-list",
                'pageNoticeList' => "{$baseUrl}/page/notice-list",
                'pageDetail' => "{$baseUrl}/page/detail",
                'pageFxbzj' => "{$baseUrl}/page/fxbzj",
                'pageActivityList' => "{$baseUrl}/page/list-activity",
                'pageActivityDetail' => "{$baseUrl}/page/activity-detail",
                'pageShareInfo' => "{$baseUrl}/page/share-info",
                'pageAgreement' => "{$baseUrl}/page/agreement",
                'pageReddotInfo' => "{$baseUrl}/page/reddot-info",
                'kdbInfo' => "{$baseUrl}/koudaibao/info",
                'kdbInvestOrder' => "{$baseUrl}/koudaibao/invest-order",
                'kdbInvestCaptcha' => "{$baseUrl}/koudaibao/invest-captcha",
                'kdbInvest' => "{$baseUrl}/koudaibao/invest",
                'kdbRollout' => "{$baseUrl}/koudaibao/rollout",
                'kdbRolloutList' => "{$baseUrl}/koudaibao/rollout-list",
                'kdbInvestList' => "{$baseUrl}/koudaibao/invest-list",
                'kdbDescDetail' => "{$baseUrl}/koudaibao/desc-detail",
                'kdbTodayRemain' => "{$baseUrl}/koudaibao/today-remain",
                'projectP2pList' => "{$baseUrl}/project/p2p-list",
                'projectTrustList' => "{$baseUrl}/project/trust-list",
                'projectDetail' => "{$baseUrl}/project/detail",
                'projectInvestList' => "{$baseUrl}/project/invest-list",
                'projectInvestOrder' => "{$baseUrl}/project/invest-order",
                'projectInvestCaptcha' => "{$baseUrl}/project/invest-captcha",
                'projectInvest' => "{$baseUrl}/project/invest",
                'projectDescDetail' => "{$baseUrl}/project/desc-detail",
                'userRegGetCode' => "{$baseUrl}/user/reg-get-code",
                'userRegister' => "{$baseUrl}/user/register",
                'userLogin' => "{$baseUrl}/user/login",
                'userLogout' => "{$baseUrl}/user/logout",
                'userRealVerify' => "{$baseUrl}/user/real-verify",
                'userChangePwd' => "{$baseUrl}/user/change-pwd",
                'userSetPaypassword' => "{$baseUrl}/user/set-paypassword",
                'userChangePaypassword' => "{$baseUrl}/user/change-paypassword",
                'userResetPwdCode' => "{$baseUrl}/user/reset-pwd-code",
                'userVerifyResetPassword' => "{$baseUrl}/user/verify-reset-password",
                'userResetPassword' => "{$baseUrl}/user/reset-password",
                'userResetPaypassword' => "{$baseUrl}/user/reset-paypassword",
                'userSupportBanks' => "{$baseUrl}/user/support-banks",
                'userBindCard' => "{$baseUrl}/user/bind-card",
                'userCards' => "{$baseUrl}/user/cards",
                'userInfo' => "{$baseUrl}/user/info",
                'userState' => "{$baseUrl}/user/state",
                'userCharge' => "{$baseUrl}/user/charge",
                'userChargeQuery' => "{$baseUrl}/user/charge-query",
                'userChargeList' => "{$baseUrl}/user/charge-list",
                'accountHome' => "{$baseUrl}/account/home",
                'accountFinishedProj' => "{$baseUrl}/account/finished-proj",
                'accountLastdayProfits' => "{$baseUrl}/account/lastday-profits",
                'accountTotalProfits' => "{$baseUrl}/account/total-profits",
                'accountProfitsDetail' => "{$baseUrl}/account/profits-detail",
                'accountHold' => "{$baseUrl}/account/hold",
                'accountRemain' => "{$baseUrl}/account/remain",
                'accountRemainList' => "{$baseUrl}/account/remain-list",
                'accountKdbTrades' => "{$baseUrl}/account/kdb-trades",
                'accountProjectTrades' => "{$baseUrl}/account/project-trades",
                'accountGet' => "{$baseUrl}/account/get",
                'accountNoticeList' => "{$baseUrl}/account/notice-list",
                'accountWithdraw' => "{$baseUrl}/account/withdraw",
                'accountWithdrawOrder' => "{$baseUrl}/account/withdraw-order",
                'accountWithdrawLog' => "{$baseUrl}/account/withdraw-log",
                'accountUserDailyProfits' => "{$baseUrl}/account/user-daily-profits",
                "creditMarketForApp" => "{$baseUrl}/credit/market-for-app",
                "creditStatistics" => "{$baseUrl}/credit/statistics",
                "creditAssignableItemsCount" => "{$baseUrl}/credit/assignable-items-count",
                "creditAllItems" => "{$baseUrl}/credit/all-items",
                "creditItemById" => "{$baseUrl}/credit/item-by-id",
                "creditInvestById" => "{$baseUrl}/credit/invest-by-id",
                "creditRecentlyPublishedAssignableItems" => "{$baseUrl}/credit/recently-published-assignable-items",
                "creditRecentlyAppliedAssignableItems" => "{$baseUrl}/credit/recently-applied-assignable-items",
                "creditUserPublishedAssignableItems" => "{$baseUrl}/credit/user-published-assignable-items",
                "creditUserAppliedAssignableItems" => "{$baseUrl}/credit/user-applied-assignable-items",
                "creditAssign" => "{$baseUrl}/credit/assign",
                "creditApplyAssignment" => "{$baseUrl}/credit/apply-assignment",
                "creditCancelAssignment" => "{$baseUrl}/credit/cancel-assignment",
            ],
        ];
        return $config;
    }
	
	/**
	 * 首页
	 *
	 * @name 首页 [getIndex]
	 */
	public function actionIndex()
	{
		$data = [];
		$setting = Setting::findByKey('project_index');
		if ($setting) {
			$settingValue = unserialize($setting->svalue);
			foreach ($settingValue as $v) {
				if ($v['id'] == 0) {
					$kdb = KdbInfo::findKoudai();
					$success_money = $kdb->total_money - $kdb->curAccount['cur_money'];
					$data[] = [
						'id' => 0,
						'name' => $v['title'],
						'apr' => $kdb->apr,
						'total_money' => $kdb->total_money,
						'success_money' => $success_money,
						'success_percent' => intval(100 * $success_money / $kdb->total_money),
						'min_invest_money' => $kdb->min_invest_money,
						'words' => '随取随存',
						'is_novice' => 0,
					];
				} else {
					$project = Project::findOne(intval($v['id']));
					$data[] = [
						'id' => $project->id,
						'name' => $v['title'],
						'apr' => $project->apr,
						'total_money' => $project->total_money,
						'success_money' => $project->success_money,
						'success_percent' => intval(100 * $project->success_money / $project->total_money),
						'min_invest_money' => $project->min_invest_money,
						'words' => $project->getPeriodLabel(),
						'is_novice' => $project->is_novice,
					];
				}
			}
		}
		return [
			'code' => 0,
			'data' => $data,
		];
	}
	
	/**
	 * 启动图片
	 * 
	 * @name 启动图片 [getLaunchImg]
	 */
	public function actionLaunchImg()
	{
		$baseUrl = $this->getRequest()->getHostInfo() . $this->getRequest()->getBaseUrl();
		return [
			'code' => 0,
			//'url' => $baseUrl . '/attachment/launch2.jpg',
			'url' => '',
			'version' => strtotime('2015-01-08 20:00:01'),
		];
	}
	
	/**
	 * 设备上报
	 * 
	 * @name 设备上报 [appDeviceReport]
	 * @method post
	 * @param string $device_id 设备唯一标识
	 * @param string $installed_time 安装时间，建议首次安装启动或升级时传否则传空，格式：2014-12-03 10:00:00
	 * @param string $uid 用户ID，客户端有缓存就传
	 * @param string $username 用户名，客户端有缓存就传
	 * @param string $net_type 网络类型：[2G, 3G, 4G, WIFI]
	 */
	public function actionDeviceReport()
	{
		// 如果是ios模拟器，则直接忽略
		if ($this->client->deviceName == 'iPhone Simulator') {
			return ['code' => 0];
		}
		
		$device_id = trim($this->request->post('device_id'));
		$installed_time = trim($this->request->post('installed_time'));
		$uid = intval($this->request->post('uid'));
		$username = trim($this->request->post('username'));
		$net_type = strtoupper(trim($this->request->post('net_type')));
		
		// 新增或更新设备信息
		$info = DeviceInfo::findOne(['device_id' => $device_id]);
		if (!$info) {
			$info = new DeviceInfo();
		}
		$info->device_id = $device_id;
		$info->device_info = $this->client->deviceName;
		$info->os_type = $this->client->clientType;
		$info->os_version = $this->client->osVersion;
		$info->app_version = $this->client->appVersion;
		if ($installed_time) {
			$info->installed_time = strtotime($installed_time);
		}
		if ($username) {
			$info->last_login_user = $username;
			$info->last_login_time = time();
		}
		$info->save();
		
		// 新增上报记录
		$visit = new DeviceVisitInfo();
		$visit->device_id = $device_id;
		$visit->uid = $uid;
		$visit->username = $username;
		$visit->visit_time = time();
		$visit->net_type = $net_type;
		$visit->save();
		
		return ['code' => 0];
	}
	
	/**
	 * 检查版本更新
	 *
	 * @name 检查版本更新 [appUpgrade]
	 */
	public function actionUpgrade()
	{
		if ($this->client->clientType == 'ios') {
			$has_upgrade = version_compare(Yii::$app->params['appConfig']['iosVersion'], $this->client->appVersion) > 0 ? 1 : 0;
			$is_force_upgrade = Yii::$app->params['appConfig']['iosForceUpgrade'];
			$new_version = Yii::$app->params['appConfig']['iosVersion'];
			$new_features = '1.此版本针对iphone6，iphone6 Plus 做了界面适配
2.修复ios低版本闪退问题
3.银行卡支付增加至13家
4.增加公告中心和消息中心，用户资金变更实时触达';
		} else {
			$has_upgrade = version_compare(Yii::$app->params['appConfig']['androidVersion'], $this->client->appVersion) > 0 ? 1 : 0;
			$is_force_upgrade = Yii::$app->params['appConfig']['androidForceUpgrade'];
			$new_version = Yii::$app->params['appConfig']['androidVersion'];
			$new_features = '1、新特点1斯蒂芬
2、新特点2斯蒂字多字多字多斯蒂芬斯蒂芬斯蒂芬芬
3、新特点3斯蒂芬';
		}
		return [
			'code' => 0,
			'has_upgrade' => $has_upgrade,
			'is_force_upgrade' => $is_force_upgrade,
			'new_version' => $new_version,
			'new_features' => $new_features,
		];
	}
	
	/**
	 * APP市场列表
	 * 
	 * @name APP市场列表 [appMarketList]
	 */
	public function actionMarketList()
	{
		return [
			'code' => 0,
			'data' => [
				'com.wandoujia.phoenix2',
				'com.qihoo.appstore',
				'com.baidu.appsearch',
				'com.tencent.android.qqdownloader',
				'com.dragon.android.pandaspace',
				'com.xiaomi.market',
				'com.hiapk.marketpho',
			]
		];
	}
	
	/**
	 * 提现（付款）回调
	 * 
	 * @name 提现（付款）回调
	 */
	public function actionPayNotify()
	{
        $get = Yii::$app->getRequest()->get();
        Yii::info("\nPayNotify get:" . var_export($get,true), PayService::LOG_CATEGORY);
        
        $payService = Yii::$container->get('payService');
        $result = $payService->withdrawHandleNotify($get);
        
        // 验签通过
        if ($result['code'] == '0') {
        	// 记录通知参数到提现记录表中，追加一个获取通知的时间
        	$get['notify_time'] = time();
        	UserWithdraw::updateAll(
        		['notify_result' => json_encode($get)],
        		['order_id' => $get['order_id']]
			);
        	
        	// 如果是支付成功则处理
        	if ($get['trade_state'] == '4') {
        		$accountService = Yii::$container->get('accountService');
        		$accountService->withdrawHandleSuccess($get['order_id']);
        	}
        }
        
        // 输出返回字符串给联动
        echo $result['return'];
	}

// 	public function actionPayTest()
// 	{
// 		$user = User::findOne(71);
// 		$payService = Yii::$container->get('payService');
// 		$ret = $payService->pay($user, 130500, 'api');
// 		print_r($ret);
// 	}
}

