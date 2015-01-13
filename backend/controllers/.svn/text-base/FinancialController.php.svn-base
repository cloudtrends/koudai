<?php
namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\web\Response;
use backend\controllers\BaseController;
use common\models\Financial;
use common\models\FinancialAccount;
use common\models\Project;
use common\models\KdbInfo;
use common\models\KdbAccount;
use common\models\UserAccount;
use common\models\UserDailyProfits;
use common\models\UserPayOrder;
/**
 * Financial controller
 */
class FinancialController extends BaseController
{

    /**
     * 所有项目列表
     */
    public function actionList()
    {
        //用户资金账户
        $stat = Financial::find()->orderBy('id desc')->asArray()->all();
        $financialList = [];
        foreach ($stat as $v) {
            $financialList['id'] =  $v['id'];
            $financialList['platform_revenue'] =  $v['total_amount_financing'] * $v['borrower_rate']/100/365;
            $financialList['investor_revenue'] =  $v['total_amount_financing'] * $v['user_rate']/100/365;
            $financialList['total_revenue'] =  $v['total_amount_financing'] * ($v['borrower_rate'] - $v['user_rate'])/100/365;
            $model = Financial::findOne($financialList['id']);
            if($model->project_type == Financial::TYPE_REGULAR && $model->status != Financial::STATUS_INVALID){
                $model->platform_revenue = floor((strtotime($model->borrower_repayment_time)-strtotime($model->loan_time)) / (24 * 3600)) * $financialList['platform_revenue'];
                $model->investor_revenue = floor((strtotime($model->borrower_repayment_time)-strtotime($model->loan_time)) / (24 * 3600)) * $financialList['investor_revenue'];
                $model->total_revenue = floor((strtotime($model->borrower_repayment_time)-strtotime($model->loan_time)) / (24 * 3600)) * $financialList['total_revenue'];
                $model->save();
            }
        }

        $search = [];
        $condition = '1=1';
        if ($this->request->get('search_submit')) {
            $search = $this->request->get();
            if ($search['id'] != '') {
                $condition .= " AND id = " . intval($search['id']);
            }
            if ($search['project_id'] != '') {
                $condition .= " AND project_id = " . intval($search['project_id']);
            }
            if ($search['project_name'] != '') {
                $condition .= " AND project_name LIKE '%" . trim($search['project_name']) . "%'";
            }
            if ($search['status'] != '') {
                $condition .= " AND status = " . intval($search['status']);
            }
            if ($search['project_type'] != '') {
                $condition .= " AND project_type = " . intval($search['project_type']);
            }
            if ($search['loan_time'] != '') {
                $condition .= " AND UNIX_TIMESTAMP(loan_time) BETWEEN " . trim($search['loan_time']);
            }
            if ($search['borrower_repayment_time'] != '') {
                $condition .= " AND UNIX_TIMESTAMP(borrower_repayment_time) BETWEEN " . trim($search['borrower_repayment_time']);
            }
        }
        $query = Financial::find()->where($condition)->orderBy('id desc');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 15;
        $financials = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('list', [
            'financials' => $financials,
            'pages' => $pages,
            'type' => 'all',
        ]);
    }

     /**
     * Ajax加载项目信息
     */
    public function actionLoadProject()
    {
        $this->response->format = Response::FORMAT_JSON;
         // 项目栏目
        $projects = Project::find()->where(['id'=>$_GET["ajaxpid"]])->orderBy('id desc')->asArray()->all();
        $total_amount_financing = sprintf('%.2f',$projects[0]['total_money'] / 100);
        $user_rate = $projects[0]['apr'];
        $term = $projects[0]['period'].($projects[0]['is_day'] ? '天' : '月');
        return [
            'total_amount_financing'=>$total_amount_financing,
            'user_rate'=>$user_rate,
            'term'=>$term,
        ];
    }

    /**
     * Ajax加载时间差
     */
    public function actionLoadTime()
    {
        $this->response->format = Response::FORMAT_JSON;
        $loan = ($_GET["years"] >0 ? $_GET["years"].'年' : '').($_GET["months"] >0 ? $_GET["months"].'月' : '').($_GET["days"] >0 ? $_GET["days"].'天' : '');
        return [
            'loan'=>$loan,
        ];
    }

    /**
    * 录入定期项目
    */
    public function actionRegularInput()
    {
        $model = new Financial();
        // 项目信息
        $projects = Project::find()->where(['status'=>[Project::STATUS_PUBLISHED,Project::STATUS_REPAYING]])->orderBy('id desc')->asArray()->all();
        // 财务信息
        $financials = Financial::find()->where(['project_type'=>Financial::TYPE_REGULAR])->andwhere(['status'=>[Financial::STATUS_REPAYMENT,Financial::STATUS_COMPLETED]])->orderBy('id desc')->asArray()->all();
        $projectnames = [];
        $projectname = [];
        foreach ($projects as $v) {
            $projectnames[$v['id']] = $v['id']. '： ' .$v['name'];
            $projectname[$v['id']] = $v['name'];
        }
        foreach ($financials as $v) {
            unset($projectnames[$v['project_id']]);
        }
        if(!empty($_POST["Financial"]["project_id"])){
            $pid = $_POST["Financial"]["project_id"];
            $model->project_name = $projectname[$pid] ;
        }
        if ($model->load($this->request->post()) && $model->validate()) {
            $model->total_amount_financing = $model->total_amount_financing * 100;
            if ($model->save()) {
                return $this->redirectMessage('录入项目成功', self::MSG_SUCCESS, Url::toRoute('financial/list'));
            } else {
                return $this->redirectMessage('录入项目失败', self::MSG_ERROR);
            }
        }
        return $this->render('input', [
            'model' => $model,
            'projectnames' => $projectnames,
            'type' => 'regular',
        ]);
    }

    /**
    * 录入活期项目
    */
    public function actionCurrentInput()
    {
        $model = new Financial();
        $model->project_id = 0;
        //活期利率对应口袋宝利率
        $kdbinfos = KdbInfo::find()->orderBy('id desc')->asArray()->all();
        $user_rate = [];
        foreach ($kdbinfos as $v) {
            $user_rate[$v['id']] = $v['apr'];
        }
        if(!empty($user_rate)){
            $model->user_rate = current($user_rate) ;
        }
        if ($model->load($this->request->post()) && $model->validate()) {
            $model->total_amount_financing = $model->total_amount_financing * 100;
            if ($model->save()) {
                return $this->redirectMessage('录入项目成功', self::MSG_SUCCESS, Url::toRoute('financial/list'));
            } else {
                return $this->redirectMessage('录入项目失败', self::MSG_ERROR);
            }
        }
        $projectnames = null;
        return $this->render('input', [
            'model' => $model,
            'projectnames' => $projectnames,
            'type' => 'current',
        ]);
    }

    /**
    * 编辑、审核项目
    */
    public function actionEdit($id,$project_type)
    {
        $model = $this->findFinancial(intval($id));
        $project_type = intval($project_type);
        $model->total_amount_financing = sprintf('%.2f',$model->total_amount_financing / 100);
        $model->total_revenue = sprintf('%.2f',$model->total_revenue / 100);
        $model->platform_revenue = sprintf('%.2f',$model->platform_revenue / 100);
        $model->investor_revenue = sprintf('%.2f',$model->investor_revenue / 100);
        if ($model->load($this->request->post()) && $model->validate()) {
            $model->total_amount_financing = $model->total_amount_financing * 100;
            $model->total_revenue = $model->total_revenue * 100;
            $model->platform_revenue = $model->platform_revenue * 100;
            $model->investor_revenue = $model->investor_revenue * 100;
            if ($model->save()) {
               return $this->redirectMessage('编辑项目成功', self::MSG_SUCCESS, Url::toRoute('financial/list'));
            } else {
               return $this->redirectMessage('编辑项目失败', self::MSG_ERROR);
            }
        }
        $projectnames = null;
        return $this->render('edit', [
            'model' => $model,   
            'projectnames' => $projectnames,
            'type' => 'edit',
            'project_type' => $project_type,
        ]);
    }

     /**
    * 操作项目
    */
    public function actionOperation($id)
    {
        $model = $this->findFinancial(intval($id));
        if ($model->load($this->request->post()) && $model->validate()) {
            if ($model->save()) {
                return $this->redirectMessage('编辑项目成功', self::MSG_SUCCESS, Url::toRoute('financial/list'));
            } else {
                return $this->redirectMessage('编辑项目失败', self::MSG_ERROR);
            }
        }
        $projectnames = null;
        $project_type = null;
        return $this->render('edit', [
            'model' => $model,    
            'projectnames' => $projectnames,
            'type' => 'operation',
            'project_type' => $project_type,
        ]);
    }

    /**
     * 作废项目
     */
    public function actionInvalid($id)
    {
        $id = intval($id);
        $model = $this->findFinancial($id);
        $model->status = Financial::STATUS_INVALID;
        $model->update();
        return $this->redirect(['financial/list']);
    }

    /**
     * 删除项目
     */
    public function actionDelete($id)
    {
        $this->findFinancial($id)->delete();
        return $this->redirect(['financial/list']);
    }

    /**
     * 查看详情
     */
    public function actionView($id)
    {
        $model = $this->findFinancial(intval($id));
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
    * 定期项目统计
    */
    public function actionRegularInfo()
    {
        $regularlist = [];  
        $projects_total_money = [];
        $active_total_money = [];
        $loan_past_days = [];
        $history_return_revenue = [];
        $history_pay_revenue = [];
        $repayment_return_revenue = [];
        $repayment_pay_revenue = [];
        $total_investor_revenue = [];
        //项目信息
        $projects = Project::find()->where(['status'=>[Project::STATUS_PUBLISHED,Project::STATUS_REPAYING]])->orderBy('id desc')->asArray()->all();
        // 财务信息（定期项目）
        $financials = Financial::find()->where(['project_type'=>Financial::TYPE_REGULAR])->orderBy('id desc')->asArray()->all();
        //项目信息(已还款)
        $projects1 = Project::find()->where(['status'=>Project::STATUS_REPAYED])->orderBy('id desc')->asArray()->all();
        //项目信息(待还款)
        $projects2 = Project::find()->where(['status'=>Project::STATUS_REPAYING])->orderBy('id desc')->asArray()->all();
        foreach ($projects as $v) {
            $projects_total_money[$v['id']] = $v['success_money'];
        }
        foreach ($projects1 as $v) {
            $history_pay_revenue[$v['id']] = (($v['success_money'] * $v['apr'] /100) / 365) * $v['period'] * ($v['is_day'] ? $v['is_day'] : ($v['is_day']*30));//单个项目历史支出
        }
        foreach ($projects2 as $v) {
            $repayment_pay_revenue[$v['id']] = (($v['success_money'] * $v['apr'] /100) / 365) * $v['period'] * ($v['is_day'] ? $v['is_day'] : ($v['is_day']*30));//单个项目投资人待收益
        }
        foreach ($financials as $v) {
            $active_total_money[$v['status']][$v['id']] = $v['total_amount_financing'];
            $loan_past_days[$v['status']][$v['id']] = floor((strtotime(date('Y-m-d',time()))-strtotime($v['loan_time'])) / (24 * 3600)) * ($v['total_amount_financing'] * (($v['borrower_rate']-$v['user_rate']) / 100) / 365);//单个项目 ：当天与放款日间隔的天数*当天实际收益
            $history_return_revenue[$v['status']][$v['id']] = floor((strtotime($v['borrower_repayment_time'])-strtotime($v['loan_time'])) / (24 * 3600)) * ($v['total_amount_financing'] * ($v['borrower_rate'] / 100) / 365);//单个项目 ：还款日与放款日间隔的天数*当天借款收益
            $repayment_return_revenue = $history_return_revenue;
            $total_investor_revenue[$v['status']][$v['id']] = $v['total_revenue'];
        }
        if (array_key_exists(Financial::STATUS_CREATE_RAISED, $active_total_money)) {
            $active_total_money1 = array_sum($active_total_money[Financial::STATUS_CREATE_RAISED]);//活期后台实际进行中项目总额(1)
        }else{
            $active_total_money1 = 0;
        }
        if (array_key_exists(Financial::STATUS_REPAYMENT, $active_total_money)) {
            $active_total_money2 = array_sum($active_total_money[Financial::STATUS_REPAYMENT]);//活期后台实际进行中项目总额(2)
        }else{
            $active_total_money2 = 0;
        }
        if (array_key_exists(Financial::STATUS_REPAYMENT, $loan_past_days)) {
            $active_total_money3 = array_sum($loan_past_days[Financial::STATUS_REPAYMENT]);//活期后台实际进行中项目总额(3)
        }else{
            $active_total_money3 = 0;
        }
        if (array_key_exists(Financial::STATUS_COMPLETED, $history_return_revenue)) {
            $history_return_revenue = array_sum($history_return_revenue[Financial::STATUS_COMPLETED]);//后台历史项目归结：总收益
        }else{
            $history_return_revenue = 0;
        }
        if (array_key_exists(Financial::STATUS_REPAYMENT, $repayment_return_revenue)) {
            $repayment_return_revenue = array_sum($repayment_return_revenue[Financial::STATUS_REPAYMENT]);//后台待收收益
        }else{
            $repayment_return_revenue = 0;
        }
        if (array_key_exists(Financial::STATUS_COMPLETED, $total_investor_revenue)) {
            $total_investor_revenue = array_sum($total_investor_revenue[Financial::STATUS_COMPLETED]);//后台实际收益
        }else{
            $total_investor_revenue = 0;
        }
        $history_pay_revenue = array_sum($history_pay_revenue);//历史投资人收益
        $repayment_pay_revenue = array_sum($repayment_pay_revenue);//投资人待收益
        $active_total_money = $active_total_money1 + $active_total_money2 + $active_total_money3;//定期后台实际进行中项目总额
        $projects_total_money = array_sum($projects_total_money);//当前网站所有正在进行中项目总额
        $regularlist['projects_total_money'] = $projects_total_money;
        $regularlist['active_total_money'] = $active_total_money;
        $regularlist['history_profit'] = $history_return_revenue-$history_pay_revenue;
        $regularlist['history_return_revenue'] = $history_return_revenue;
        $regularlist['history_pay_revenue'] = $history_pay_revenue;
        $regularlist['repayment_profit'] = $repayment_return_revenue-$repayment_pay_revenue;
        $regularlist['total_investor_revenue'] = $total_investor_revenue;
        return $this->render('_regularinfo', [
            'regularlist' => $regularlist,
        ]);
    }

    /**
    * 活期项目统计
    */
    public function actionCurrentInfo()
    {
        $currentlist = [];  
        $kdb_total_money = [];
        $all_investor_revenue = [];
        $today_pay_revenue = [];
        $today = date('Y-m-d',time());
        $active_total_money = [];
        $today_return_revenue = [];
        $loan_past_days = [];
        $history_return_revenue = [];
        $total_investor_revenue = [];

        $kdbaccounts = KdbAccount::findCurrent();
        $kdbinfos = KdbInfo::findKoudai();
        //用户日收益
        $userdailyprofits = UserDailyProfits::find()->where(['project_type'=>UserDailyProfits::PROJECT_TYPE_KDB])->andWhere(['date'=>$today])->orderBy('id desc')->asArray()->all();
        // 财务信息（活期）
        $financials = Financial::find()->where(['project_type'=>Financial::TYPE_CURRENT])->orderBy('id desc')->asArray()->all();
        foreach ($userdailyprofits as $v) {   
            $today_pay_revenue[$v['id']] = $v['lastday_profits'];
        }
        foreach ($financials as $v) {       
            $active_total_money[$v['status']][$v['id']] = $v['total_amount_financing'];//本金
            $today_return_revenue[$v['status']][$v['id']] = $v['total_amount_financing'] * ($v['borrower_rate'] / 100) / 365;//单个项目当天借款收益
            $loan_past_days[$v['status']][$v['id']] = floor((strtotime(date('Y-m-d',time()))-strtotime($v['loan_time'])) / (24 * 3600)) * ($v['total_amount_financing'] * ($v['borrower_rate'] / 100) / 365);//单个项目 ：当天与放款日间隔的天数*当借款收益
            $history_return_revenue[$v['status']][$v['id']] = floor((strtotime($v['borrower_repayment_time'])-strtotime($v['loan_time'])) / (24 * 3600)) * ($v['total_amount_financing'] * ($v['borrower_rate'] / 100) / 365);//单个项目 ：还款日与放款日间隔的天数*当天借款收益
            $total_investor_revenue[$v['status']][$v['id']] = $v['total_revenue'];
        }
        $currentlist['kdb_total_money1'] = $kdbinfos->total_money;
        $currentlist['kdb_total_money2'] = $kdbaccounts->cur_money;
        $kdb_total_money = $kdbinfos->total_money - $kdbaccounts->cur_money;//当前网站口袋宝总额  
        $all_investor_revenue = $kdbaccounts->history_profits_money;//活期当前给用户产生收益总额
        $today_pay_revenue = array_sum($today_pay_revenue);//活期当日支出收益    
        if (array_key_exists(Financial::STATUS_REPAYMENT, $active_total_money)) {
            $active_total_money1 = array_sum($active_total_money[Financial::STATUS_REPAYMENT]);//活期后台实际进行中项目总额(1)
        }else{
            $active_total_money1 = 0;
        }
        if (array_key_exists(Financial::STATUS_REPAYMENT, $loan_past_days)) {
            $active_total_money2 = array_sum($loan_past_days[Financial::STATUS_REPAYMENT]);//活期后台实际进行中项目总额(2)
        }else{
            $active_total_money2 = 0;
        }
        $active_total_money = $active_total_money1 + $active_total_money2;//活期后台实际进行中项目总额
        if (array_key_exists(Financial::STATUS_REPAYMENT, $today_return_revenue)) {
            $today_return_revenue = array_sum($today_return_revenue[Financial::STATUS_REPAYMENT]);//活期后台当日收益
        }else{
            $today_return_revenue = 0;
        }
        if (array_key_exists(Financial::STATUS_COMPLETED, $history_return_revenue)) {
            $history_return_revenue = array_sum($history_return_revenue[Financial::STATUS_COMPLETED]);//后台历史项目归结：总收益
        }else{
            $history_return_revenue = 0;
        }
        if (array_key_exists(Financial::STATUS_REPAYMENT, $total_investor_revenue)) {
            $total_investor_revenue = array_sum($total_investor_revenue[Financial::STATUS_REPAYMENT]);//后台实际收益
        }else{
            $total_investor_revenue = 0;
        }
        $currentlist['kdb_total_money'] = $kdb_total_money;
        $currentlist['all_investor_revenue'] = $all_investor_revenue;
        $currentlist['today_pay_revenue'] = $today_pay_revenue;
        $currentlist['active_total_money'] = $active_total_money;
        $currentlist['today_return_revenue'] = $today_return_revenue;
        $currentlist['history_return_revenue'] = $history_return_revenue;
        $currentlist['total_investor_revenue'] = $total_investor_revenue;
        return $this->render('_currentinfo', [
            'currentlist' => $currentlist,
        ]);
    }

    /**
    * 总账明细详情
    */
    public function actionCountInfo($id)
    {
        $model = FinancialAccount::findOne($id);
        return $this->render('_countinfo', [
            'model' => $model,
        ]);
    }
    /**
    * 总账明细表
    */
    public function actionCountList()
    {
        $search = [];
        $condition = '1=1';
        if ($this->request->get('search_submit')) {
            $search = $this->request->get();
            if ($search['date'] != '') {
                $condition .= " AND  FROM_UNIXTIME(date-86400,'%Y-%m-%d') = '" . trim($search['date'])."'";
            }
        }
        $query = FinancialAccount::find()->where($condition)->orderBy('id desc');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 15;
        $financialaccounts = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('_countlist', [
            'countlist' => $financialaccounts,
            'pages' => $pages,
        ]);
    }

    /**
    ********************
    */
    protected function findFinancial($id)
    {
        if (($model = Financial::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

     protected function findProject()
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}