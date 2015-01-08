<?php

namespace frontend\controllers;


use Yii;
use yii\db\ActiveQuery;
use common\exceptions\InvestException;
use yii\data\Pagination;
use yii\filters\AccessControl;

use common\models\Project;
use common\models\InvestCor;
use common\models\CreditBaseInfo;
use common\models\ProjectInvest;
use common\helpers\TimeHelper;
use common\services\CreditService;
use common\models\ProjectProfits;

class CreditController extends BaseController
{

    // 默认页面数 1
    const DEFAULT_PAGE = 1;

    // 最近发布成功 页面展示个数
    const RECENTLY_PUBLISHED_PAGE_SIZE = 5;

    // 最近申购成功 页面展示个数
    const RECENTLY_APPLIED_PAGE_SIZE = 5;

    // 可转让项目 页面展示个数
    const ASSIGNABLE_ITEMS_PAGE_SIZE = 5;
    
    // 用户发布债权 页面展示个数
    const USER_PUBLISHED_ASSIGNABLE_PAGE_SIZE = 5;

    // 用户申购债权 页面展示个数
    const USER_APPLIED_ASSIGNABLE_PAGE_SIZE = 5;

    // services
    protected $creditService;

    public function __construct($id, $module, CreditService $creditService, $config = [])
    {
        $this->creditService = $creditService;
        parent::__construct($id, $module, $config);
    }
    
    public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				// 只有下面的action需要登录
				'only' => ['user-published-assignable-items', 'user-applied-assignable-items', 'assign',
							'apply_assignment', 'cancel_assignment'],
				'rules' => [
					[
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}
    //------------------------------------------------- 读接口 -------------------------------------------------

    /**
     *
     * @name 转让专区数据接口 [creditMarketForApp]
     * @method get
     * @uses 返回转让专区所有的数据，专为App包装
     * @param integer $page 需要查看的当前页（从1开始）
     * @param integer $pageSize 每页个数
     */
    public function actionMarketForApp(
        $page = self::DEFAULT_PAGE,
        $pageSize = self::ASSIGNABLE_ITEMS_PAGE_SIZE)
    {
        $data = [];
        $data['code'] = 0;
        $result = $this->actionStatistics();
        if( $result['code'] != 0 ){
            InvestException::throwCodeExt();
        }

        $data['statistics'] = $result;

        $result = $this->actionRecentlyAppliedAssignableItems($page, $pageSize);
        if( $result['code'] != 0 ){
            InvestException::throwCodeExt();
        }

        $data['recentlyAppliedItems'] = $result;

        $result = $this->actionRecentlyPublishedAssignableItems($page, $pageSize);
        if( $result['code'] != 0 ){
            InvestException::throwCodeExt();
        }

        $data['recentlyPublishedItems'] = $result;

        return $data;

    }

    /**
     * @name 项目转让统计数据 [creditStatistics]
     * @method get
     * @uses 在App“产品列表” -> “转让专区”，拉取“统计数据”
     */
    public function actionStatistics()
    {
        $result = Yii::$app->db->createCommand(
            "SELECT ".
            "count(1) as accumulatedCount,
            sum(duein_capital) as accumulatedAmount
            FROM tb_project_profits
            WHERE status in (".ProjectProfits::STATUS_SUCCESS. ",".ProjectProfits::STATUS_REPAYED." )".
            " AND is_transfer = 1;"
        )->queryOne();

        return array(
            'code' => 0,
            'accumulatedCount' => strval(intval($result['accumulatedCount'])),
            'accumulatedAmount' =>strval(intval($result['accumulatedAmount']))
        );
    }

    /**
     * @name 拉取所有可转让项目总数 [creditAssignableItemsCount]
     * @method get
     */
    public function actionAssignableItemsCount()
    {
        $creditQuery = CreditBaseInfo::find();
        $totalCount = $creditQuery->where(['status' => CreditBaseInfo::STATUS_ASSIGNING ])->count();

        return array(
            'code' => 0,
            'creditItemsCount' => $totalCount
        );
    }

    /**
     * @name 拉取所有项目 [creditAllItems]
     * @method get
     * @param integer $page 需要查看的当前页（从1开始）
     * @param integer $pageSize 每页个数
     */
    public function actionAllItems(
        $page = self::DEFAULT_PAGE,
        $pageSize = self::ASSIGNABLE_ITEMS_PAGE_SIZE
    )
    {
        $creditQuery = CreditBaseInfo::find();
        $totalCount = $creditQuery->count();
        $pages = new Pagination(['totalCount' => $totalCount]);
        $pages->page = $page - 1;
        $pages->pageSize = $pageSize;

         // 1. 查询 tb_ca_base_info 基本表获得转让的债权
        $results = $creditQuery->orderBy("updated_at desc")
            ->offset($pages->offset)->limit($pages->limit)->with([
            // 2. 通过 with("project") 关联项目表，获得转让项目的详细信息
            "project" => function(ActiveQuery $query){
                $query->select([
                    'id',
                    'name',
                    'type',
                    'status',
                    'period',
                    'is_day',
                    'effect_time',
                    'created_username',
                    'publish_username',
                    'total_money',
                    'success_money',
                    'apr',
                    'is_novice',
                    'desc',
                ]);
            }
        ])->asArray()->all();


        $creditList = [];
        if(!empty($results))
        {
            foreach($results as $value){
                $credit = [];
                $credit['id'] = $value['id'];
                $credit['project_type'] = $value['project_type'];
                $credit['assign_fee'] = $value['assign_fee'];
                $credit['assign_rate'] = $value['assign_rate'];
                $credit['rest_days'] = intval(($value['assign_end_date'] - time()) / TimeHelper::DAY);

                $project = $value['project'];
                $projectSummary = [];
                $projectSummary['id'] = $project['id'];
                $projectSummary['apr'] = $project['apr'];
                $projectSummary['name'] = $project['name'];
                $credit['project'] = $projectSummary;
                $creditList[] = $credit;
            }
        }


        return array(
            'code' => 0,
            'creditItems' => $creditList,
            'creditItemsCount'=> $totalCount,
        );
    }


    /**
     * @name 拉取指定的转让项目详情 [creditItemById]
     * @method get
     * @uses 转让专区 -> 转让项目列表 -> 立即购买 : 进入指定项目详情后拉取详情
     * @param integer $id 需要查看转让ID
     */
    public function actionItemById($id)
    {
        $creditQuery = CreditBaseInfo::find();
        $queryItem = $creditQuery->where(['id' => $id])->with("project")->asArray()->one();
        $creditItem = [];
        if( !empty($queryItem) )
        {
            //
            $creditItem['project_name'] = $queryItem['project']['name'];
            $creditItem['assign_rate'] = $queryItem['assign_rate'];
            $creditItem['assign_fee'] = $queryItem['assign_fee'];
            $creditItem['project'] = $queryItem['project'];

            $invest_id = $queryItem['invest_id'];
            $investItem = ProjectInvest::find($invest_id)->asArray()->one();

            if( !empty($investItem) )
            {


            }
        }

        return array(
            'code' => 0,
            'creditItem' => $creditItem,
        );
    }

    /**
     * @name 拉取指定的投资的详情 [creditInvestById]
     * @method get
     * @uses 个人中心 -> 我的项目列表 -> 转让 : 进入指定项目详情后拉取详情
     * @param integer $invest_id 需要查看的投资
     */
    public function actionInvestById($invest_id){
        $queryResult = ProjectProfits::find()->where(['invest_id' => $invest_id])->asArray()->one();

        $investItem = [];
        $investItem['project_name'] = $queryResult['project_name'];
        $investItem['project_apr'] = $queryResult['project_apr'];
        $investItem['duein_money'] = $queryResult['duein_money'];
        $investItem['duein_capital'] = $queryResult['duein_capital'];

        // 已经进行的天数
        $total_days = TimeHelper::DiffDays( $queryResult['last_repay_date'], $queryResult['interest_start_date'] );
        $last_days = TimeHelper::DiffDays( date("Y-m-d",time()) , $queryResult['interest_start_date'] );
        $last_days = $last_days < 0 ? 0 : $last_days;

        $investItem['total_days'] = $total_days;
        $investItem['last_days'] = $last_days;
        $investItem['rest_days'] = $total_days - $last_days;
        $investItem['profits'] = round($queryResult['duein_profits'] * $last_days / $total_days);
        $investItem['refer_money'] = $investItem['duein_capital'] + $investItem['profits'];

        return array(
            'code' => 0,
            'investItem' => $investItem,
           // 'queryResult' => $queryResult,
        );
    }

    /**
     * @name 拉取转让项目列表 [creditRecentlyPublishedAssignableItems]
     * @method get
     * @uses 产品列表 -> 转让专区 ： 拉取“转让项目列表”
     * @param integer $page 需要查看的当前页（从1开始）
     * @param integer $pageSize 每页个数
     */
    public function actionRecentlyPublishedAssignableItems(
        $page = self::DEFAULT_PAGE,
        $pageSize = self::ASSIGNABLE_ITEMS_PAGE_SIZE
    )
    {

        $page = $page < 1 ? 1 : $page;
        $creditQuery = CreditBaseInfo::find();
        $totalCount = $creditQuery->where(['status' => CreditBaseInfo::STATUS_ASSIGNING ])->count();
        /*$pages = new Pagination(['totalCount' => $totalCount]);
        $pages->page = $page - 1;
        $pages->pageSize = $pageSize;


        // 1. 查询 tb_ca_base_info 基本表获得转让的债权
        $results = $creditQuery->select([
            'id',
            'project_type',
            'invest_id',
            'project_id',
            'assign_start_date',
            'assign_end_date',
            'assign_fee',
            'assign_rate',
            'user_id',
            'user_name',
            'commission_rate',
            'status',
        ])->where( ['status' => CreditBaseInfo::STATUS_ASSIGNING] )
            ->orderBy("updated_at desc")
            ->offset($pages->offset)->limit($pages->limit)->with([
            "project" => function(ActiveQuery $query){
                $query->select([
                    'id',
                    'name',
                    'type',
                    'status',
                    'period',
                    'is_day',
                    'effect_time',
                    'created_username',
                    'publish_username',
                    'total_money',
                    'success_money',
                    'apr',
                    'is_novice',
                    'desc',
                ]);
            }
        ])->asArray()->all();



        $creditList = [];
        if(!empty($results))
        {
            foreach($results as $value){
                $credit = [];
                $credit['id'] = $value['id'];
                $credit['invest_id'] = $value['invest_id'];
                $credit['project_type'] = $value['project_type'];
                $credit['assign_fee'] = $value['assign_fee'];
                $credit['assign_rate'] = $value['assign_rate'];
                $credit['rest_days'] = intval(($value['assign_end_date'] - time()) / TimeHelper::DAY);
                $credit['user_name'] = $value['user_name'];

                $project = $value['project'];
                $projectSummary = [];
                $projectSummary['id'] = $project['id'];
                $projectSummary['apr'] = $project['apr'];
                $projectSummary['name'] = $project['name'];
                $credit['project'] = $projectSummary;

                $creditList[] = $credit;
            }
        }

        */
        $db = Yii::$app->db;

        $start = ($page - 1) * $pageSize;
        $sql = "select "."
                    cbi.id ca_base_id,
                    cbi.project_type cbi_project_type,
                    cbi.invest_id cbi_invest_id ,
                    cbi.assign_start_date cbi_assign_start_date,
                    cbi.assign_end_date cbi_assign_end_date,
                    cbi.assign_fee cbi_assign_fee,
                    cbi.assign_rate cbi_assign_rate,
                    cbi.commission_rate cbi_commission_rate,
                    cbi.status cbi_status,
                    cbi.user_name cbi_user_name,
                    p.id project_id,
                    p.name project_name,
                    p.type p_type,
                    p.product_type p_product_type,
                    p.status p_status,
                    p.is_day p_is_day,
                    p.period p_period,
                    p.publish_at p_publish_at,
                    p.effect_time p_effect_time,
                    p.review_at p_review_at,
                    p.apr p_apr
                from
                    tb_ca_base_info cbi left join tb_project p
                    on p.id = cbi.project_id
                where
                    cbi.status=".CreditBaseInfo::STATUS_ASSIGNING ."
                order by cbi.updated_at desc
                limit $start, $pageSize";

        $results = $db->createCommand($sql)->queryAll();
        $creditList2 = [];
        if(!empty($results))
        {
            foreach($results as $value)
            {
                $credit = [];

                $credit['id'] = $value['ca_base_id'];
                $credit['invest_id'] = $value['cbi_invest_id'];
                $credit['project_type'] = $value['cbi_project_type'];
                $credit['assign_fee'] = $value['cbi_assign_fee'];
                $credit['assign_rate'] = $value['cbi_assign_rate'];
                $credit['rest_days'] = intval(($value['cbi_assign_end_date'] - time()) / TimeHelper::DAY);
                $credit['user_name'] = $value['cbi_user_name'];

                $credit['project'] = [
                    'id' => $value['project_id'],
                    'apr' => $value['p_apr'],
                    'name' => $value['project_name'],
                ];

                $creditList2[] = $credit;
            }
        }

        return array(
            'code' => 0,
            //'creditItems' => $creditList,
            'creditItems' => $creditList2,
            'creditItemsCount'=> $totalCount,
        );
    }


    /**
     * @name 拉取最新交易记录 [creditRecentlyAppliedAssignableItems]
     * @method get
     * @uses 产品列表 -> 转让专区 : 查询平台最新的转让交易记录
     * @param integer $page 需要查看的当前页（从1开始）
     * @param integer $pageSize 每页个数
     */
    public function actionRecentlyAppliedAssignableItems(
        $page = self::DEFAULT_PAGE,
        $pageSize = self::ASSIGNABLE_ITEMS_PAGE_SIZE
    )
    {
        $investQuery = ProjectProfits::find();

        $totalCount = $investQuery->where([
            'status' => [ProjectProfits::STATUS_SUCCESS,ProjectProfits::STATUS_REPAYED],
            'is_transfer' => 1 ]
        )->count();
            
        $pages = new Pagination(['totalCount' => $totalCount]);
        $pages->page = $page - 1;
        $pages->pageSize = $pageSize;

        $queryItems = $investQuery->where([
            'status' => [ProjectProfits::STATUS_SUCCESS,ProjectProfits::STATUS_REPAYED],
            'is_transfer' => 1,
        ])->orderBy("updated_at desc")
            ->offset($pages->offset)->limit($pages->limit)
            ->asArray()->all();
        

        $creditList = array();
        
        if(!empty($queryItems))
        {
            foreach($queryItems as $value){
                $credit = array();
                $credit['id'] = $value['id'];
                $credit['invest_uid'] = $value['invest_uid'];
                $credit['profits_uid'] = $value['profits_uid'];
                $credit['project_apr'] = $value['project_apr'];
                $credit['duein_capital'] = $value['duein_capital'];
                $credit['trade_time'] = date("Y-m-d H:i:s", ( $value['updated_at'] ));
                $creditList[] = $credit;               
            }
        }

        return array(
            'code' => 0,
            'creditItems' => $creditList,
            'creditItemsCount' => $totalCount,
        );
    }

    /**
     * @name 根据用户UID拉取发布的债权记录 [creditUserPublishedAssignableItems]
     * @method get
     * @uses 个人中心 : 用户查询自己发布的转让项目
     * @param integer $user_id 用户ID
     * @param integer $page 需要查看的当前页（从1开始）
     * @param integer $pageSize 每页个数
     */
    public function actionUserPublishedAssignableItems(
        $user_id,
        $page = self::DEFAULT_PAGE,
        $pageSize = self::ASSIGNABLE_ITEMS_PAGE_SIZE)
    {
        $creditQuery = CreditBaseInfo::find();

        // 总数查询
        $condition = ['user_id' => $user_id];
        $totalCount = $creditQuery->where($condition)->count();
        $pages = new Pagination(['totalCount' => $totalCount]);
        $pages->page = $page - 1;
        $pages->pageSize = $pageSize;

        // 数据查询
        $appliedItems = $creditQuery->select([
            'id',
            'project_type',
            'invest_id',
            'project_id',
            'assign_start_date',
            'assign_end_date',
            'assign_fee',
            'assign_rate',
            'user_id',
            'user_name',
            'commission_rate',
            'status',
        ])->where( $condition )->offset($pages->offset)->limit($pages->limit)
        ->orderBy("updated_at desc") // 根据时间排序
        ->with([ // 2. 通过 with("project") 关联项目表，获得转让项目的详细信息
            "project" => function(ActiveQuery $query){
                $query->select([
                    'id',
                    'name',
                    'type',
                    'status',
                    'period',
                    'is_day',
                    'effect_time',
                    'created_username',
                    'publish_username',
                    'total_money',
                    'success_money',
                    'apr',
                    'is_novice',
                    'desc',
                ]);
            }
        ])->asArray()->all();

        if( empty($appliedItems) )
            $appliedItems = [];

        return array(
            'code' => 0,
            'totalCount' => $totalCount,
            'pageSize' => self::RECENTLY_PUBLISHED_PAGE_SIZE,
            'appliedItems' => $appliedItems,
        );
    }


    /**
     * @name 根据用户ID拉取申购的债权记录 [creditUserAppliedAssignableItems]
     * @method get
     * @uses 个人中心 : 用户查询自己申购的转让项目
     * @param integer $user_id 用户ID
     * @param integer $page 需要查看的当前页（从1开始）
     * @param integer $pageSize 每页个数
     */
    public function actionUserAppliedAssignableItems(
        $user_id,
        $page = self::DEFAULT_PAGE,
        $pageSize = self::ASSIGNABLE_ITEMS_PAGE_SIZE)
    {
        $investQuery = InvestCor::find();

        // 总数查询
        $condition = ['user_id' => $user_id];
        $totalCount = $investQuery->where($condition)->count();
        $pages = new Pagination(['totalCount' => $totalCount]);
        $pages->page = $page - 1;
        $pages->pageSize = $pageSize;

        // 数据查询
        $appliedItems = $investQuery->select([
            'ca_base_info_id',
            'former_invest_id',
            'later_invest_id',
            'user_id',
            'user_name',
        ])->where($condition)->offset($pages->offset)->limit($pages->limit)
        ->with(["creditBaseInfo" => function(ActiveQuery $query){
            $query->select([
                'id',
                'project_type',
                'invest_id',
                'project_id',
                'assign_start_date',
                'assign_end_date',
                'assign_fee',
                'assign_rate',
                'user_id',
                'user_name',
                'commission_rate',
                'status',
            ])->where(['status' => CreditBaseInfo::STATUS_ASSIGNING]);
        }])->asArray()->all();

        // TODO: 转让项目推荐利率

        return array(
            'code' => 0,
            'limit' => self::RECENTLY_APPLIED_PAGE_SIZE,
            'appliedItems' => $appliedItems,
        );
    }


    //------------------------------------------------- 写接口 -------------------------------------------------

    /**
     *
     * @name 用户转让已投资的债权项目 [creditAssign]
     * @method post
     * @uses 个人中心 : 用户对自己的某个已投资项目，进行转让操作
     * @param integer $invest_id 投资ID
     * @param integer $assign_fee 转让价格
     * @param integer $pay_password 交易密码
     */
    public function actionAssign()
    {

        $db = Yii::$app->db;

        try
        {
            $invest_id = $this->request->post("invest_id");
            if( empty( $invest_id ))
            {
                InvestException::throwCodeExt(-1002,"invest_id");
            }

            $assign_fee = $this->request->post("assign_fee");
            if( empty( $assign_fee ))
            {
                InvestException::throwCodeExt(-1002,"assign_fee");
            }

            $pay_password = $this->request->post("pay_password");
            if( empty( $pay_password ))
            {
                InvestException::throwCodeExt(-1002,"pay_password");
            }

            // -----------  1. 获取用户信息 -------------
            $curUser = Yii::$app->user->identity;

            if(empty($curUser)) {
                InvestException::throwCodeExt(1101);
            }

            if(!$curUser->validatePayPassword($pay_password)){
                InvestException::throwCodeExt(1102);
            }

            // -----------  2. 根据用户ID 获取投标的信息 -------------
            $uid = $curUser['id'];

            // 查询投资详情转让记录
            $investInfo = ProjectInvest::getDetailById($invest_id);

            if ( empty($investInfo) )
            {
                InvestException::throwCodeExt(1002);
            }

            if ( empty($investInfo['project_id']) or empty($investInfo['profit_id'])  )
            {
                InvestException::throwCodeExt(1003);
            }

            if ( $uid != $investInfo['invest_uid'] )
            {
                InvestException::throwCodeExt(1021);
            }

            // ----------- 3. 检查转让标信息 -------------
            // 3.1 状态是否在进行中
            if ( !isset($investInfo['p_status']) || $investInfo['p_status'] != Project::STATUS_REPAYING )
            {
                InvestException::throwCodeExt(1004,"({$investInfo['p_status']})");
            }

            if ( !isset($investInfo['pi_status']) || $investInfo['pi_status'] != ProjectInvest::STATUS_SUCCESS )
            {
                InvestException::throwCodeExt(1005,"({$investInfo['pi_status']})");
            }

            // 3.2 当前时间 需要 晚于开始时间一个月
            $now = TimeHelper::Now();
            $start_time = $investInfo['p_review_at'];
            if ( !TimeHelper::isLT30Days($now, $start_time) )
            {
                InvestException::throwCodeExt(1006);
            }

            // 3.3 当前时间 需要 早于结束时间一个月
            if( $investInfo['p_is_day'] == 1)
            {
                // 按天 计算结束时间
                $end_time = strtotime("+{$investInfo['p_period']} days", $start_time );
            }
            else
            {
                // 按月 计算结束时间
                $end_time = strtotime("+{$investInfo['p_period']} months", $start_time );
            }

            if ( !TimeHelper::isLT30Days($end_time, $now) )
            {
                InvestException::throwCodeExt(1007);
            }

            // 3.4 转让价格不能低于本金，高于本金 + 利息

            $assign_fee_cents = $assign_fee * 100;
            if( $assign_fee_cents < $investInfo['pp_duein_capital'] )
            {
                InvestException::throwCodeExt(1019);
            }

            if( $assign_fee_cents >= $investInfo['pp_duein_money'] )
            {
                InvestException::throwCodeExt(1020);
            }

            // ------- 4. 转让，写DB -------
            $creditInfo = $this->creditService->assign($invest_id, $assign_fee, $investInfo);

            return [
                'code' => 0,
                'creditInfo' => $creditInfo,
                "note" => array(
                    1 => "转让有效期为30天",
                    2 => "30天内成交有效，否则系统将取消转让",
                )
            ];
        }
        catch(InvestException $e)
        {
            throw $e;
        }

    }


    /**
     *
     * @name 用户申购其他用户转让的债权项目 [creditApplyAssignment]
     * @method post
     * @uses 转让专区 : 用户确定购买其他人转让的项目
     * @param integer $invest_id 投资ID
     * @param integer $pay_password 交易密码
     * @param integer $use_remain 是否使用余额（1用余额，0不用余额）
     */
    public function actionApplyAssignment()
    {
        $invest_id = $this->request->post("invest_id");
        if( empty( $invest_id ))
        {
            InvestException::throwCodeExt(-1002,"invest_id");
        }

        $pay_password = $this->request->post("pay_password");
        if( empty( $pay_password ))
        {
            InvestException::throwCodeExt(-1002,"pay_password");
        }

        $use_remain = $this->request->post("use_remain");
        if( !in_array($use_remain, [0, 1]) )
        {
            InvestException::throwCodeExt(-1002,"use_remain");
        }

        // -----------  1. 获取用户信息 -------------
        $curUser = Yii::$app->user->identity;

        if(empty($curUser)) {
            InvestException::throwCodeExt(1101);
        }

        if(!$curUser->validatePayPassword($pay_password)){
            InvestException::throwCodeExt(1102);
        }

        // -----------  2. 根据投资ID 获取投标的信息 -------------
        $investInfo = self::_getInvestInfo($invest_id);

        // ----------- 3. 不能申购自己转让的投资 -------------
        if( $curUser['id'] == $investInfo['invest_uid'] )
        {
            InvestException::throwCodeExt(1017);
        }

        // ----------- 4. 申购转让项目，写DB -------------
        $investInfoReturn = $this->creditService->applyAssignment(
            $curUser,
            $invest_id,
            $use_remain,
            $investInfo
        );

        return [
            'code' => 0,
            'uid' => $curUser['id'],
            'investInfo' => $investInfoReturn,
        ];
    }

    /**
     *
     * @name 用户取消转让的债权项目 [creditCancelAssignment]
     * @method post
     * @uses 个人中心 : 用户取消已转让的项目，恢复到自己持有的状态
     * @param integer $invest_id 投资ID
     */
    public function actionCancelAssignment()
    {
        $invest_id = $this->request->post("invest_id");
        if( empty( $invest_id ))
        {
            InvestException::throwCodeExt(-1002,"invest_id");
        }

        // -----------  1. 获取用户信息 -------------
        $curUser = Yii::$app->user->identity;

        if(empty($curUser)) {
            InvestException::throwCodeExt(1101);
        }

        // -----------  2. 根据投资 获取债权信息 -------------
        $investInfo = self::_getInvestInfo($invest_id);


        // 只能取消自己的转让项目
        $uid = $curUser['id'];
        if ( $investInfo['invest_uid'] != $uid)
        {
            InvestException::throwCodeExt(1021);
        }


        // ------- 3. 转让，写DB -------
        $creditInfo = $this->creditService->cancelAssignment($invest_id);

        return [
            'code' => 0,
            'creditInfo' => $creditInfo,
        ];
    }


    private static function _getInvestInfo($invest_id)
    {
        $investInfo = CreditBaseInfo::getDetailByInvestId($invest_id);
        if ( empty($investInfo) )
        {
            InvestException::throwCodeExt(1002,"({$invest_id})");
        }

        // 1.1 invest 对应的 ca_base project profits 三张表都有对应的数据
        if ( empty($investInfo['profit_id']) )
        {
            InvestException::throwCodeExt(1008,"($invest_id)");
        }

        if ( empty($investInfo['ca_base_id']) )
        {
            InvestException::throwCodeExt(1008,"($invest_id)");
        }

        if ( empty($investInfo['project_id']) )
        {
            InvestException::throwCodeExt(1008,"($invest_id)");
        }

        // 1.2 投资记录状态是否为“转让中”
        if ( !isset($investInfo['pi_status'] ) || $investInfo['pi_status'] != ProjectInvest::STATUS_ASSIGNING )
        {
            InvestException::throwCodeExt(1013,"({$investInfo['pi_status']})");
        }

        // 1.3 投资收益状态是否为“转让中”

        if ( !isset($investInfo['pp_status'] ) || $investInfo['pp_status'] != ProjectProfits::STATUS_ASSIGNING )
        {
            InvestException::throwCodeExt(1005,"({$investInfo['pp_status']})");
        }

        // 1.4 项目状态是否在“还款中”

        if ( !isset($investInfo['p_status'] ) || $investInfo['p_status'] != Project::STATUS_REPAYING )
        {
            InvestException::throwCodeExt(1012,"({$investInfo['p_status']})");
        }

        return $investInfo;
    }

    //------------------------------------------------- 私有函数 -------------------------------------------------

}