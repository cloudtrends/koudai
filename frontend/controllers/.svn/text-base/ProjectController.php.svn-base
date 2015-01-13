<?php

namespace frontend\controllers;

use common\exceptions\PayException;
use common\models\BankConfig;
use common\models\UserBankCard;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\base\UserException;
use yii\web\Response;
use yii\filters\AccessControl;
use common\helpers\StringHelper;
use common\models\Project;
use common\models\ProjectInvest;
use common\models\KdbInfo;
use common\services\ProjectService;
use common\models\Order;
use common\models\UserCaptcha;
use yii\data\Pagination;
use common\models\User;

/**
 * Project controller
 */
class ProjectController extends BaseController
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
				// 只有下面的action需要登录
				'only' => ['invest'],
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
	 * 安稳袋项目列表
	 * 展示下面状态的项目：发布成功投资中、满款、还款中、还款完成
	 * 排序：先按上面状态顺序排序，再按新手专属优先排，再按创建时间排
	 * 
	 * @name 安稳袋项目列表 [projectP2pList]
	 * @param integer $page 第几页
	 * @param integer $pageSize 每页个数
	 */
	public function actionP2pList($page = 1, $pageSize = 10)
	{
		$page = $page > 1 ? intval($page) : 1;
		$pageSize = intval($pageSize);
		$offset = ($page - 1) * $pageSize;
		$projects = (new Query())->from(Project::tableName())->select([
			'id', 'name', 'status', 'total_money', 'success_money', 'success_number', 'is_novice', 'min_invest_money', 'period', 'is_day', 'apr', 'summary',
		])->where([
			'type' => Project::TYPE_P2P,
			'status' => [Project::STATUS_PUBLISHED, Project::STATUS_FULL, Project::STATUS_REPAYING, Project::STATUS_REPAYED],
		])->orderBy([
			'status' => SORT_ASC,
			'is_novice' => SORT_DESC,
			'id' => SORT_DESC,
		])->offset($offset)->limit($pageSize)->all();
		
		foreach ($projects as &$project) {
			$project['success_percent'] = intval(100 * $project['success_money'] / $project['total_money']);
		}
		
		// 第一页才拉口袋宝
		$koudaibao = [];
		if ($page == 1) {
			$model = KdbInfo::findKoudai();
			if ($model) {
				$koudaibao = [
					"title" => $model->title,
					"total_money" => $model->total_money,
					"apr" => $model->apr,
					"summary" => $model->summary,
					"status" => $model->status,
					"daily_invest_limit" => $model->daily_invest_limit,
					"daily_withdraw_limit" => $model->daily_withdraw_limit,
					"interest_desc" => $model->interest_desc,
					"min_invest_money" => $model->min_invest_money,
					"remain_money" => $model->curAccount['cur_money'],
					"cur_invest_times" => isset($model->curAccount['history_invest_times']) ? $model->curAccount['history_invest_times'] : 0,
				];
			}
		}
		
		return [
			'code' => 0,
			'page' => $page,
			'pageSize' => $pageSize,
			'projects' => $projects,
			'koudaibao' => $koudaibao,
		];
	}
	
	/**
	 * 金融袋项目列表
	 * 展示下面状态的项目：发布成功投资中、满款、还款中、还款完成
	 * 排序：先按上面状态顺序排序，再按新手专属优先排，再按创建时间排
	 *
	 * @name 金融袋项目列表 [projectTrustList]
	 * @param integer $page 第几页
	 * @param integer $pageSize 每页个数
	 */
	public function actionTrustList($page = 1, $pageSize = 10)
	{
		$page = $page > 1 ? intval($page) : 1;
		$pageSize = intval($pageSize);
		$offset = ($page - 1) * $pageSize;
		$projects = (new Query())->from(Project::tableName())->select([
			'id', 'name', 'status', 'total_money', 'success_money', 'success_number', 'is_novice', 'min_invest_money', 'period', 'is_day', 'apr', 'summary',
		])->where([
			'type' => Project::TYPE_TRUST,
			'status' => [Project::STATUS_PUBLISHED, Project::STATUS_FULL, Project::STATUS_REPAYING, Project::STATUS_REPAYED],
		])->orderBy([
			'status' => SORT_ASC,
			'is_novice' => SORT_DESC,
			'id' => SORT_DESC,
		])->offset($offset)->limit($pageSize)->all();
		
		foreach ($projects as &$project) {
			$project['success_percent'] = intval(100 * $project['success_money'] / $project['total_money']);
		}
		
		return [
			'code' => 0,
			'page' => $page,
			'pageSize' => $pageSize,
			'projects' => $projects,
		];
	}
	
	/**
	 * 项目详情
	 * 
	 * @name 项目详情 [projectDetail]
	 * @param integer $id 项目id
	 */
	public function actionDetail($id)
	{
		$model = Project::findOne([
			'id' => intval($id),
			'status' => [Project::STATUS_PUBLISHED, Project::STATUS_FULL, Project::STATUS_REPAYING, Project::STATUS_REPAYED],
		]);
		if (!$model) {
			throw new NotFoundHttpException('不存在该项目');
		}

		$project = $model->toArray();
		// 此接口不返回desc字段
		unset($project['desc']);
		$project['risk_control_managed'] = Project::RISK_CONTROL_MANAGED;
		$project['risk_control_warrant'] = Project::RISK_CONTROL_WARRANT;
		$project['risk_control_repay'] = Project::RISK_CONTROL_REPAY;
		$project['bank_apr'] = Yii::$app->params['bankApr'];
		if ($project['status'] == Project::STATUS_REPAYING || $project['status'] == Project::STATUS_REPAYED) {
			$project['last_repay_date'] = strtotime($model->getRepayDate());
		} else {
			$project['last_repay_date'] = 0;
		}
        $project['success_percent'] = intval(100 * $project['success_money'] / $project['total_money']);

		
		return [
			'code' => 0,
			'project' => $project,
		];
	}
	
	/**
	 * 项目概述内页H5
	 * 
	 * @name 项目概述内页H5 [projectDescDetail]
	 * @param integer $id 项目id
	 */
	public function actionDescDetail($id)
	{
		$this->response->format = Response::FORMAT_HTML;
		$model = Project::findOne(intval($id));
		return $this->render('/page/detail', ['html' => $model->desc]);
	}
	
	/**
	 * 投资下单
	 *
	 * @name 投资下单 [projectInvestOrder]
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
		$order->type = Order::TYPE_INVEST_PROJ;
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
	 * @name 投资重新发验证码 [projectInvestCaptcha]
	 */
	public function actionInvestCaptcha()
	{
		$curUser = Yii::$app->user->identity;
		$userService = Yii::$container->get('userService');
		if ($userService->generateAndSendCaptcha($curUser->username, UserCaptcha::TYPE_INVEST_PROJ)) {
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
	 * @name 投资 [projectInvest]
	 * @method post
	 * @param integer $id 项目id
	 * @param integer $use_remain 是否用余额，1或0
	 * @param integer $money 投资金额
	 * @param string $pay_password 交易密码
	 * @param string $order_id 订单ID
	 * @param string $captcha 验证码
	 * @param string $sign 签名：签名方式同kdbInvest接口
	 */
	public function actionInvest()
	{
		$id = intval($this->request->post('id'));
		$money = intval(bcmul($this->request->post('money'), 100));
		$payPassword = $this->request->post('pay_password');
		$useRemain = intval($this->request->post('use_remain'));
		$captcha = trim($this->request->post('captcha'));
		if (!$id || $money <= 0 || !$payPassword || !in_array($useRemain, [0, 1])) {
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


        if ($existBindBank['third_platform'] == BankConfig::PLATFORM_UMPAY){

            if (!$captcha && $userService->optionNeedCaptcha($curUser, UserCaptcha::TYPE_INVEST_PROJ, $this->request->post())) {
                $userService->generateAndSendCaptcha($curUser->username, UserCaptcha::TYPE_INVEST_PROJ);
                throw new UserException('需要验证码', 2001);
            } else if ($captcha && !$userService->validatePhoneCaptcha($curUser->username, $captcha, UserCaptcha::TYPE_INVEST_PROJ)) {
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
		
		// 加锁，如果已经有锁，则抛异常失败
		$curUser->addTradeLock();
		try {
			$project = Project::findOne($id);
			if (!$project) {
				throw new UserException('无此项目', 1005);
			} else if ($project->status != Project::STATUS_PUBLISHED) {
				throw new UserException('项目不是投资中状态', 1006);
			} else if (time() - $project->publish_at > $project->effect_time * 24 * 3600) {
				throw new UserException('该项目已经过期', 1007);
			} else if ($project->success_money >= $project->total_money) {
				throw new UserException('项目已投满', 1008);
			} else if ($project->is_novice && !$curUser->is_novice) {
				throw new UserException('您不是新手，请前往产品列表购买其他产品', 1009);
			} else if ($money < $project->min_invest_money) {
				throw new UserException('投资金额不能小于起购金额：' . ($project->min_invest_money / 100) .'元', 1010);
			} else if ($money % $project->min_invest_money > 0) {
				throw new UserException('投资金额必须是起购金额的整数倍', 1011);
			} else if ($money > $project->total_money - $project->success_money) {
				$curUser->releaseTradeLock();
				return [
					'code' => 1012,
					'message' => '可申购金额不足，请刷新产品重试',
					'remain_money' => $project->total_money - $project->success_money,
				];
			}
			
			$result = false;
			if ($useRemain == 0) { // 全用银行卡
				$result = $this->projectService->investProject($project, $money, $money);
			} else {
				if ($money - $curUser->account->usable_money > 0) {
					$invest_pay_money = $money - $curUser->account->usable_money;
				} else {
					$invest_pay_money = 0;
				}
				$result = $this->projectService->investProject($project, $money, $invest_pay_money);
			}

			if ($result) {
				// 修改订单状态
				$order->status = Order::STATUS_SUCCESS;
				$order->save(false);
					
				// 删除验证码
				UserCaptcha::deleteAll(['phone' => $curUser->username, 'type' => UserCaptcha::TYPE_INVEST_PROJ]);
				// 释放锁
				$curUser->releaseTradeLock();
				
				// 输出投标的数据，用户购买成功的展示
		        $complete =  intval(100 * ($project['success_money'] + $money) / $project['total_money']);
		        $period = $project['period']. ($project['is_day'] == 1 ? "天" : "个月");
		
		        $investInfo = [
		            'invest' => [
		                'project_type_desc' => Project::$typeList[$project['type']],
		                'project_name' => $project['name'],
		                'apr' => $project['apr'],
		                'invest_money' => $money,
		                'date' => date("Y-m-d H:i",time()),
		            ],
		            'start' => [
		                'date' => "目前完成$complete%",
		                'desc' => "复审后开始计算收益",
		            ],
		            'end' => [
		                'date' => "复审后$period",
		                'desc' => "收益到账",
		            ],
		        ];
		
				return [
					'code' => 0,
					'result' => $result,
					'investInfo' => $investInfo,
				];
			} else {
				throw new UserException('投资失败，请稍后再试', 1013);
			}
		} catch (\Exception $e) {
			// 修改订单状态
			$order->status = Order::STATUS_FAILED;
			$order->save(false);
			
			$curUser->releaseTradeLock();
			throw $e;
		}
	}
	
	/**
	 * 投资记录列表
	 * 
	 * @name 投资记录列表 [projectInvestList]
	 * @param integer $id 项目id
	 * @param integer $page 第几页
	 * @param integer $pageSize 每页个数
	 */
	public function actionInvestList($id, $page = 1, $pageSize = 10)
	{
		$page = $page > 1 ? intval($page) : 1;
		$pageSize = intval($pageSize);
		$offset = ($page - 1) * $pageSize;
        $query = (new Query())->from(ProjectInvest::tableName())->select([
			'id', 'username', 'invest_money', 'created_at', 'status'
		])->where([
			'project_id' => intval($id),
		])->orderBy([
			'id' => SORT_DESC,
		]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $invests = $query->offset($offset)->limit($pageSize)->all();

		foreach ($invests as $k => $v) {
			$invests[$k]['username'] = StringHelper::blurPhone($v['username']);
			$invests[$k]['statusLabel'] = ProjectInvest::$status[$v['status']];
		}
		
		return [
			'code' => 0,
			'page' => $page,
			'pageSize' => $pageSize,
            'pages' => $pages,
			'invests' => $invests,
		];
	}

    /**
     * 最新投资记录列表
     * @name 最新投资记录列表 [projectInvestLog]
     * @param int $page 第几页
     * @param int $pageSize 每页个数
     * @param int $status 投资状态
     * @param string $id 项目id
     * @return array
     */
    public function actionInvestLog($page = 1, $pageSize = 10,$status = 2 ,$id = ''){
        $page = $page > 1 ? intval($page) : 1;
        $pageSize = intval($pageSize);
        $offset = ($page - 1) * $pageSize;

        $condition = '1=1';
        if (isset($status)){
            $condition .= ' AND `status` = ' . $status ;
        }

        if (!empty($id)){
            $condition .= ' AND project_id = '.$id;
        }
        $invests = (new Query())->from(ProjectInvest::tableName())->select([
            'id', 'username', 'invest_money', 'created_at', 'status','user_id','project_name'
        ])->where($condition)->orderBy([
            'created_at' => SORT_DESC,
        ])->offset($offset)->limit($pageSize)->all();

        $userArr = [];
        foreach ($invests as $k => $v) {
            $invests[$k]['username'] = StringHelper::blurPhone($v['username']);
            $invests[$k]['statusLabel'] = ProjectInvest::$status[$v['status']];
            $userArr[] = $v['user_id'];
        }

        $userInfo = (new Query())->from(User::tableName())->select([
            'id', 'realname'
        ])->where([
            'id' => array_unique($userArr),
        ])->all();



        if (!empty($userInfo)){
            $userResult = array();
            foreach ($userInfo as $userInfoVal){
                $userResult[$userInfoVal['id']] = $userInfoVal['realname'];
            }

            foreach ($invests as $k => $v) {
                $invests[$k]['realname'] = !empty($userResult[$v['user_id']]) ? $userResult[$v['user_id']] : '';
            }
        }

        return [
            'code' => 0,
            'page' => $page,
            'pageSize' => $pageSize,
            'invests' => $invests,
        ];
    }

    /**
     * 网站列表页面
     *
     * @name 网站列表 [projectWebsiteList]
     * @method get
     * @param integer $page 第几页
     * @param integer $pageSize 每页个数
     * @param string $type 类型
     * @param string $status 状态
     * @param string $period 期限
     * @param string $apr 利率
     */
    public function actionWebsiteList(){
        $page = $this->request->get('page', 1);
        $pageSize = $this->request->get('pageSize', 9);
        $type = $this->request->get('type', '');
        $status = $this->request->get('status', '');
        $periodStr = $this->request->get('period', '');
        $aprStr = $this->request->get('apr', '');

        $page = $page > 1 ? intval($page) : 1;
        $pageSize = intval($pageSize);
        $offset = ($page - 1) * $pageSize;

        $condition = '1=1';
        if (!empty($type)){
            $condition .= " AND `type` = ".$type;
        }

        if ( !empty($status) ){
            $condition .= " AND `status` = ".$status;
        }else{
            $statusArr = array(Project::STATUS_PUBLISHED, Project::STATUS_FULL, Project::STATUS_REPAYING, Project::STATUS_REPAYED);
            $condition .= " AND `status` in (".implode(',',$statusArr).")";
        }

        if (!empty(self::$aprArr[$aprStr])){
            $apr = self::$aprArr[$aprStr];
            $aprArr = explode('-',$apr);
            if (count($aprArr) === 2){
                $condition .= " AND apr >= ".$aprArr[0]." AND apr <= ".$aprArr[1];
            }else{
                $aprslicp = explode('_',$apr);
                if (count($aprslicp) === 2){
                    if ( in_array($aprslicp[0] , $this->CompareSym()) ){
                        $condition .= " AND apr ".$aprslicp[0].$aprslicp[1];
                    }
                }
            }
        }

        if (!empty(self::$perArr[$periodStr])){
            $period = self::$perArr[$periodStr];
            $periodArr = explode('-',$period);
            if (count($periodArr) === 2){
                $condition .= " AND ( ( is_day = 0 AND period >=". $periodArr[0] ." AND period <= ".$periodArr[1].")";
                $condition .= " OR ( is_day = 1 AND period >=". StringHelper::monthToDays($periodArr[0]);
                $condition .= " AND period <= ".StringHelper::monthToDays($periodArr[1]).") )";
            }else{
                $periodslicp = explode('_',$period);
                if (count($periodslicp) === 2){
                    if ( in_array($periodslicp[0],$this->CompareSym()) ){
                        $condition .= " AND ( ( is_day = 0 AND period ". $periodslicp[0] . $periodslicp[1] .")";
                        $condition .= " OR ( is_day = 1 AND period ". $periodslicp[0] . StringHelper::monthToDays($periodslicp[1] ).") )";
                    }
                }
            }
        }

        $query = (new Query())->from(Project::tableName())->select([
            'id', 'name', 'status', 'total_money', 'success_money', 'success_number', 'is_novice', 'min_invest_money', 'period', 'is_day', 'apr','summary',
        ])->where(
            $condition
        )->orderBy([
            'is_novice' => SORT_DESC,
            'id' => SORT_DESC,
        ]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $projects = $query->offset($offset)->limit($pageSize)->all();

        foreach ($projects as &$project) {
            $project['success_percent'] = intval(100 * $project['success_money'] / $project['total_money']);
        }

        return [
            'code' => 0,
            'page' => $page,
            'pageSize' => $pageSize,
            'pages' => $pages,
            'projects' => $projects,
        ];
    }

    private static $perArr = array(1=>'<_1',
        2=>'1-3',
        3=>'3-6',
        4=>'6-12',
        5=>'>_12',
    );

    private static $aprArr = array(1=>'8-10',
        2=>'10-12',
        3=>'>_12',
    );

    private function CompareSym(){
        return array('<','<=','>','>=','=');
    }

}