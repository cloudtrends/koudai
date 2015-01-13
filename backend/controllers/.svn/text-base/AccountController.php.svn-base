<?php

namespace backend\controllers;


use common\exceptions\PayException;
use common\models\BankConfig;
use Yii;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use common\models\UserWithdraw;
use common\services\AccountService;
use common\models\UserAccountLog;
use common\models\UserAccount;
use common\models\User;
use common\models\UserDailyProfits;
use backend\models\RechargeForm;
use yii\helpers\Url;

/**
 * AccountController controller
 */
class AccountController extends BaseController {

    protected $accountService;

    public function __construct($id, $module, AccountService $accountService, $config = []) {
        $this->accountService = $accountService;
        parent::__construct($id, $module, $config);
    }

    /**
     * 用户资金信息
     */
    public function actionList() {
        $search = [];
        $condition = '1=1';
        if ($this->request->get('search_submit')) { // 过滤
            $search = $this->request->get();
            if (!empty($search['username'])) {
                $username = $search['username'];
                $result = Yii::$app->db->createCommand('select * from tb_user WHERE username ="' . $username . '"')->queryOne();
                $uid = $result["id"];
                $condition .= " AND user_id = " . intval($uid);
            }
            if (!empty($search['realname'])) {
                $realname = $search['realname'];
                $result = Yii::$app->db->createCommand('select * from tb_user WHERE realname ="' . $realname. '"')->queryOne();
                $uid = $result["id"];
                $condition .= " AND user_id = " . intval($uid);
            }
            if (!empty($search['user_id'])) {
                    $condition .= " AND user_id = " . intval($search['user_id']);
            }
        }
        $query = UserAccount::find()->where($condition)->orderBy('id desc');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 15;
        $accounts = $query->with('user')->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('list', [
			'accounts' => $accounts,
			'pages' => $pages,
        ]);
    }
    
    /**
     * 用户收益日志
     */
    public function actionDailyProfits() {
        $search = [];
        $condition = '1=1';
        if ($this->request->get('search_submit')) { // 过滤
            $search = $this->request->get();
            if (!empty($search['username'])) {
                $username = $search['username'];
                $result = Yii::$app->db->createCommand('select * from tb_user WHERE username ="' . $username . '"')->queryOne();
                $uid = $result["id"];
                $condition .= " AND user_id = " . intval($uid);
            }
            if (!empty($search['realname'])) {
                $realname = $search['realname'];
                $result = Yii::$app->db->createCommand('select * from tb_user WHERE realname ="' . $realname. '"')->queryOne();
                $uid = $result["id"];
                $condition .= " AND user_id = " . intval($uid);
            }
            if (!empty($search['user_id'])) {
                    $condition .= " AND user_id = " . intval($search['user_id']);
            }
            if (!empty($search['begintime'])) {
                $condition .= " AND UNIX_TIMESTAMP(date) >= " . strtotime($search['begintime']);
                if (!empty($search['endtime'])) {
                    $condition .= " AND UNIX_TIMESTAMP(date) <= " . strtotime($search['endtime']);
                }
            } else {
                if (!empty($search['endtime'])) {
                    $condition .= " AND UNIX_TIMESTAMP(date) <= " . strtotime($search['endtime']);
                }
            }
        }
    	$query = UserDailyProfits::find()->where($condition)->orderBy('date desc');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 15;
        $profitses = $query->with('user')->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('daily-profits', [
			'profitses' => $profitses,
			'pages' => $pages,
        ]);
    }

    /**
     * 提现列表
     */
    public function actionWithdraw() {
        $search = [];
        $condition = '1=1';
        if ($this->request->get('search_submit')) { // 过滤
            $search = $this->request->get();
            if (!empty($search['username'])) {
                $username = $search['username'];
                $result = Yii::$app->db->createCommand('select * from tb_user WHERE username="' . $username . '"')->queryOne();
                $uid = $result["id"];
                $condition .= " AND user_id = " . intval($uid);
            }
            if (!empty($search['user_id'])) {
                    $condition .= " AND user_id = " . intval($search['user_id']);
            }
            if (!empty($search['status'])) {
                    $condition .= " AND status = " . intval($search['status']);
            }
            if (!empty($search['begintime'])) {
                $condition .= " AND created_at >= " . strtotime($search['begintime']);
                if (!empty($search['endtime'])) {
                    $condition .= " AND created_at <= " . strtotime($search['endtime']);
                }
            } else {
                if (!empty($search['endtime'])) {
                    $condition .= " AND created_at <= " . strtotime($search['endtime']);
                }
            }
        }
        $query = UserWithdraw::find()->where($condition)->orderBy('id desc');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 15;
        $withdraws = $query->with('user')->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('withdraw', [
			'withdraws' => $withdraws,
			'pages' => $pages,
        ]);
    }

    public function actionWithdrawDetail($id,$user_id)
    {
        $result = UserWithdraw::find()->where(['id' => $id])->asArray()->one();

        if(empty($result))
        {
            return $this->redirectMessage("无此提现记录({$id})：", self::MSG_ERROR);
        }


        $uid = $result['user_id'];
        $user = User::find()->where(['id' => $uid])->asArray()->one();
        if(empty($user))
        {
            return $this->redirectMessage("无此用户提现记录({$uid})：", self::MSG_ERROR);
        }

        $status_desc = !empty(UserWithdraw::$ump_pay_status[$result['status']]) ? UserWithdraw::$ump_pay_status[$result['status']] : "无效状态";
        $type = $result['type'];

        $withdraw = [
            'id' => $result['id'],
            'user_id' => $result['user_id'],
            'user_name' => $user['username'],
            'user_realname' => $user['realname'],
            'money' => $result['money'] / 100 ."元",
            'type' => $type,
            'status' => $result['status'],
            'status_desc' => $status_desc,
            'result' => json_decode($result['result'],true),
            'notify_result' => json_decode($result['notify_result'],true),
            'review_username' => $result['review_username'],
            'review_result' => $result['review_result'],
            'created_at' => date("Y-m-d H:i:s",$result['created_at']),
        ];


        $operation = $this->request->post("operation");
        if ($operation == "approve")
        {
            return $this->withdrawApprove($id, $withdraw['user_name'], $result['money']);
        }
        else if ($operation == "reject")
        {
            return $this->actionWithdrawReject($id);
        }
        $search = [];
        $condition = '1=1';
        if ($this->request->get('search_submit')) { // 过滤
            $search = $this->request->get();
            if (!empty($search['type'])) {
                $condition .= " AND type = " . intval($search['type']);
            }

            if (!empty($search['begintime'])) {
                $condition .= " AND created_at >= " . strtotime($search['begintime']);
                if (!empty($search['endtime'])) {
                    $condition .= " AND created_at <= " . strtotime($search['endtime']);
                }
            } else {
                if (!empty($search['endtime'])) {
                    $condition .= " AND created_at <= " . strtotime($search['endtime']);
                }
            }
        }
        //用户资金流水记录
        // 1. 查询 tb_user_account_log 表
        $query = UserAccountLog::find()->where(['user_id' => $user_id])->andwhere($condition)->orderBy('id desc');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 10;
        $results = $query->offset($pages->offset)->limit($pages->limit)->with([
                    // 2. 通过 with("user") 关联用户表，获得投资者姓名
                    "user" => function(ActiveQuery $userquery) {
                $userquery->select([
                    'id',
                    'username',
                ]);
            }
                ])->asArray()->all();
        $creditList = [];
        if (!empty($results)) {
            foreach ($results as $value) {
                $stat = [];
                $stat['id'] = $value['id'];
                $stat['user_id'] = $value['user_id'];
                if (isset(UserAccount::$tradeTypes[$value['type']])) {
                    $stat['type'] = UserAccount::$tradeTypes[$value['type']];
                } else {
                    $stat['type'] = "未知类型";
                }
                $stat['operate_money'] = $value['operate_money'];
                $stat['total_money'] = $value['total_money'];
                $stat['usable_money'] = $value['usable_money'];
                $stat['investing_money'] = $value['investing_money'];
                $stat['withdrawing_money'] = $value['withdrawing_money'];
                $stat['duein_capital'] = $value['duein_capital'];
                $stat['duein_profits'] = $value['duein_profits'];
                $stat['kdb_total_money'] = $value['kdb_total_money'];
                $stat['created_at'] = $value['created_at'];

                $user = $value['user'];
                $userSummary = [];
                $userSummary['id'] = $user['id'];
                $userSummary['username'] = $user['username'];
                $stat['user'] = $userSummary;
                $creditList[] = $stat;
            }
        }
        return $this->render('withdraw-detail', [
            'withdraw' => $withdraw,
            'user_account_log_list' => $creditList,
            'pages' => $pages,
        ]);
    }

    /**
     * 提现审核通过
     */
    public function withdrawApprove($id, $user_name, $money) {
        try
        {
            $this->accountService->withdrawApprove(
                $id,
                $money,
                $user_name,
                Yii::$app->user->identity->username
            );
            return $this->redirectMessage('操作成功', self::MSG_SUCCESS);
        }
        catch (\Exception $e)
        {
            return $this->redirectMessage('操作出现异常：' . $e->getMessage() . "(". $e->getCode() .")", self::MSG_ERROR);
        }
    }

    /**
     * 提现审核驳回
     */
    public function actionWithdrawReject($id) {
        try {
            $this->accountService->withdrawReject(intval($id), Yii::$app->user->identity->username);
            return $this->redirectMessage('操作成功', self::MSG_SUCCESS);
        } catch (\Exception $e) {
            return $this->redirectMessage('操作出现异常：' . $e->getMessage(), self::MSG_ERROR);
        }
    }
    
    /**
     * 提现付款查询
     */
    public function actionWithdrawResult($order_id) {

    	if ($this->request->getIsPost()) {
    		$accountService = Yii::$container->get('accountService');
        	$accountService->withdrawHandleSuccess($order_id);
        	return $this->redirectMessage('操作成功', self::MSG_SUCCESS, Url::toRoute('account/withdraw'));
    	}

        //
        $withdraw = UserWithdraw::findOne(['order_id' => $order_id]);

        if(empty($withdraw))
        {
            return $this->redirectMessage("操作出现异常：未找到提现记录" ."(2107)", self::MSG_ERROR);
        }

        if($withdraw['third_platform'] == BankConfig::PLATFORM_UMPAY)
        {
            $payService = Yii::$container->get('payService');
            $result = $payService->withdrawQuery($order_id);
            return $this->render('withdraw-result', [
                'result' => $result,
            ]);
        }
        else if($withdraw['third_platform'] == BankConfig::PLATFORM_LLPAY)
        {
            $llPayService = Yii::$container->get('llPayService');
            $result = $llPayService->withdrawQuery($withdraw);
            return $this->render('withdraw-result-ll', [
                'result' => $result,
            ]);
        }
        return $this->redirectMessage("操作出现异常：不支持的第三方支付" ."(2101)", self::MSG_ERROR);
    }

    /**
     * 资金流水列表
     */
    public function actionStat() {
        $search = [];
        $condition = '1=1';
        if ($this->request->get('search_submit')) { // 过滤
            $search = $this->request->get();
            if (!empty($search['username'])) {
                $username = $search['username'];
                $result = Yii::$app->db->createCommand('select * from tb_user WHERE username="' . $username . '"')->queryOne();
                $uid = $result["id"];
                $condition .= " AND user_id = " . intval($uid);
            }
            if (!empty($search['type'])) {
                $condition .= " AND type = " . intval($search['type']);
            }

            if (!empty($search['begintime'])) {
                $condition .= " AND created_at >= " . strtotime($search['begintime']);
                if (!empty($search['endtime'])) {
                    $condition .= " AND created_at <= " . strtotime($search['endtime']);
                }
            } else {
                if (!empty($search['endtime'])) {
                    $condition .= " AND created_at <= " . strtotime($search['endtime']);
                }
            }
        }
        // 1. 查询 tb_user_account_log 表
        $query = UserAccountLog::find()->where($condition)->orderBy('id desc');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 15;
        $results = $query->offset($pages->offset)->limit($pages->limit)->with([
                    // 2. 通过 with("user") 关联用户表，获得投资者姓名
                    "user" => function(ActiveQuery $userquery) {
                $userquery->select([
                    'id',
                    'username',
                	'realname',
                ]);
            }
                ])->asArray()->all();
        $creditList = [];
        if (!empty($results)) {
            foreach ($results as $value) {
                $stat = [];
                $stat['id'] = $value['id'];
                $stat['user_id'] = $value['user_id'];
                if (isset(UserAccount::$tradeTypes[$value['type']])) {
                    $stat['type'] = UserAccount::$tradeTypes[$value['type']];
                } else {
                    $stat['type'] = "未知类型";
                }
                $stat['operate_money'] = $value['operate_money'];
                $stat['total_money'] = $value['total_money'];
                $stat['usable_money'] = $value['usable_money'];
                $stat['investing_money'] = $value['investing_money'];
                $stat['withdrawing_money'] = $value['withdrawing_money'];
                $stat['duein_capital'] = $value['duein_capital'];
                $stat['duein_profits'] = $value['duein_profits'];
                $stat['kdb_total_money'] = $value['kdb_total_money'];
                $stat['created_at'] = $value['created_at'];

                $user = $value['user'];
                $userSummary = [];
                $userSummary['id'] = $user['id'];
                $userSummary['username'] = $user['username'];
                $userSummary['realname'] = $user['realname'];
                $stat['user'] = $userSummary;
                $creditList[] = $stat;
            }
        }

        return $this->render('stat', [
			'creditList' => $creditList,
			'pages' => $pages,
        ]);
    }

    /**
     * 后台充值
     */
    public function actionRecharge()
    {
    	echo '待完善';exit;
    	$model = new RechargeForm();
    	
    	if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
    		try {
    			$this->accountService->backendRecharge($model->getUser()->id, intval($model->money * 100), $model->remark);
	    		return $this->redirectMessage('操作成功', self::MSG_SUCCESS, Url::toRoute('account/recharge'));
    		} catch (\Exception $e) {
    			return $this->redirectMessage('操作失败：' . $e->getMessage(), self::MSG_ERROR, Url::toRoute('account/recharge'));
    		}
    	}
    	
    	return $this->render('recharge', [
    		'model' => $model
    	]);
    }
}
