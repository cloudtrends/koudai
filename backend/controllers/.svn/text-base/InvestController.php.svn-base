<?php
namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\db\ActiveQuery;
use backend\controllers\BaseController;
use common\models\Project;
use common\models\ProjectInvest;
use backend\models\ActionModel;
use common\models\UserAccountLog;
use common\models\UserAccount;
use common\models\User;
use common\models\ProjectProfits;
use common\models\CreditBaseInfo;

class InvestController extends BaseController {

    /**
     * 投资记录列表
     */
    public function actionInvests() {
        $search = array();
        
        $condition = '1=1';
        if ($this->request->get('search_submit')) { // 过滤
            $search = $this->request->get();
            if (isset($search['keyword'])) {
                $condition .= " AND username LIKE '%" . trim($search['keyword']) . "%'";
            }

            if (!empty($search['status'])) {
                $condition .= " AND status = " . intval($search['status']);
            }

            if ($search['is_transfer']=== '0' || $search['is_transfer']=== '1' ){
                $condition .= " AND is_transfer = " . intval($search['is_transfer']);
            }
        }
     
        $query = ProjectInvest::find()->where($condition)->orderBy('id desc');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 15;
        $invests = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('invests', [
            'invests' => $invests,
            'pages' => $pages,
        ]);
    }

    /**
     * 查看详情
     */

    /*
     public function actionView($id)
    {
        $model = projectInvest::find($id);
        $sql = "select
                    pi.username,
                    pi.invest_money,
                    pi.status istatus,
                    pi.is_transfer,
                    pi.transfer_money,
                    pi.remark,
                    pi.former_invest_id,
                    pi.latter_invest_id,
                    p.name,
                    p.type,
                    p.total_money,
                    p.apr,
                    p.period,
                    p.is_day,
                    p.status jstatus,
                    pp.duein_money,
                    pp.duein_profits,
                    pp.project_apr,
                    pp.status fstatus,
                    pp.interest_start_date,
                    pp.last_repay_date,
                    pp.created_at
                FROM
                    tb_project_invest pi LEFT JOIN (tb_project p LEFT JOIN tb_project_profits pp
                    ON pp.project_id=p.id )
                    ON p.id=pi.project_id
                WHERE pi.id = $id ";

        $result = Yii::$app->db->createCommand($sql)->queryOne();

        // $fid = $result['former_invest_id'];
        // $results4 = $model->where(['id' => $fid])->with("profits")->asArray()->one();
        // $lid = $result['latter_invest_id'];
        // $results5 = $model->where(['id' => $lid])->with("profits")->asArray()->one();

        $investItem = [];

        if (!empty($result))
        {
            $investItem['project_name'] = $result['name'];
            $investItem['type'] = Project::$typeList[$result['type']];
            $investItem['total_money'] = $result['total_money'];
            $investItem['apr'] = $result['apr'];
            $investItem['period'] = $result['period'];
            $investItem['is_day'] = $result['is_day'];
            $investItem['project_status'] = Project::$status[$result['jstatus']];
            $investItem['username'] = $result['username'];
            $investItem['invest_money'] = $result['invest_money'];
            $investItem['status'] = $result['istatus'];
            $investItem['duein_money'] = $result['duein_money'];
            $investItem['duein_profits'] = $result['duein_profits'];
            $investItem['project_apr'] = $result['project_apr'];
            if (isset(ProjectProfits::$status[$result['fstatus']])) {
                $investItem['profits_status'] = ProjectProfits::$status[$result['fstatus']];
            } else {
                $investItem['profits_status'] = "未知类型";
            }

            $investItem['interest_start_date'] = $result['interest_start_date'];
            $investItem['last_repay_date'] = $result['last_repay_date'];
            $investItem['created_at'] = $result['created_at'];
            $investItem['is_transfer'] = $result['is_transfer'];
            $investItem['transfer_money'] = $result['transfer_money'];
            $investItem['remark'] = $result['remark'];
        }
        //var_dump($investItem);exit;
        $formItem = [];

        $fid = $result['former_invest_id'];
        if (!empty($fid))
        {
            $sql = "select
                    *
                    from " . ProjectInvest::tableName() . " pi "
                ." inner join ". ProjectProfits::tableName()." pp "
                ." on pi.id = pp.invest_id "
                ." where pi.id = {$fid}";

            $results4 = Yii::$app->db->createCommand($sql)->queryOne();


            if (!empty($results4)) {
                $formItem['invest_money'] = $results4['invest_money'];
                if (isset(ProjectProfits::$status[$results4['profits']['status']])) {
                    $formItem['status'] = ProjectProfits::$status[$results4['profits']['status']];
                } else {
                    $formItem['status'] = "未知类型";
                }
                $formItem['project_apr'] = $results4['profits']['project_apr'];
            }

            var_dump($formItem);die;

        }


        $latterItem = [];

        $lid = $result['latter_invest_id'];
        if (!empty($lid)){
            $sql = "select
                    *
                    from " . ProjectInvest::tableName() . " pi "
                ." inner join ". ProjectProfits::tableName()." pp "
                ." on pi.id = pp.invest_id "
                ." where pi.id = {$lid}";

            $results5 = Yii::$app->db->createCommand($sql)->queryOne();

            if (!empty($results5)) {
                $latterItem['usernmae'] = $results5['username'];
                $latterItem['invest_money'] = $results5['invest_money'];
                // $formItem['status'] = $results4['status'];
                if (isset(ProjectProfits::$status[$results5['status']])) {
                    $latterItem['status'] = ProjectProfits::$status[$results5['status']];
                } else {
                    $latterItem['status'] = "未知类型";
                }
                //$latterItem['project_apr'] = $results5['profits']['project_apr'];
                //$latterItem['start_date'] = $results5['profits']['interest_start_date'];
               // $latterItem['repay_date'] = $results5['profits']['last_repay_date'];
            }

            var_dump($latterItem);die;

        }



        // var_dump($formItem);exit;
        $results3 = $model->where(['id' => $id])->with([
            "cabaseinfo" => function(ActiveQuery $caquery) {
                $caquery->select([
                    'id',
                    'user_name',
                    'assign_rate',
                    'assign_start_date',
                    'assign_end_date',
                    'commission_rate',
                    'status',
                ]);
            }
        ])->asArray()->one();

        $creditList = [];

        if (!empty($results3)) {

            $creditList['id'] = $results3['cabaseinfo']['id'];
            $creditList['user_name'] = $results3['cabaseinfo']['user_name'];
            $creditList['assign_rate'] = $results3['cabaseinfo']['assign_rate'];
            $creditList['assign_start_date'] = $results3['cabaseinfo']['assign_start_date'];
            $creditList['assign_end_date'] = $results3['cabaseinfo']['assign_end_date'];
            $creditList['commission_rate'] = $results3['cabaseinfo']['commission_rate'];
            if (isset(CreditBaseInfo::$status[$results3['cabaseinfo']['status']])) {
                $creditList['status'] = CreditBaseInfo::$status[$results3['cabaseinfo']['status']];
            } else {
                $creditList['status'] = "未知类型";
            }
        }
        // var_dump($creditList);exit;
        return $this->render('view', [
            'model' => $model,
            'creditList'=>$creditList,
            'investItem'=> $investItem,
            'formItem'=>$formItem,
            'latterItem'=>$latterItem,
        ]);
    }


    */
    public function actionView($id) {
        $model = $this->findModel(intval($id));
        $sql = "SELECT
                    i.username,
                    i.type itype,
                    i.invest_money,
                    i.status istatus,
                    i.is_transfer,
                    i.transfer_money,
                    i.remark,
                    i.former_invest_id,
                    i.latter_invest_id,
                    j.name,
                    j.type jtype,
                    j.total_money,
                    j.apr,
                    j.period,
                    j.is_day,
                    j.status jstatus,
                    f.invest_id,
                    f.duein_money,
                    f.duein_profits,
                    f.project_apr,
                    f.status fstatus,
                    f.interest_start_date,
                    f.last_repay_date,
                    f.created_at
                FROM 
                    tb_project j LEFT JOIN ( tb_project_invest i  LEFT JOIN tb_project_profits f
                    ON i.id = f.invest_id )
                    ON  j.id = i.project_id
                WHERE i.id = $id";
        $result = Yii::$app->db->createCommand($sql)->queryOne();
        //$result =  $results[0];
        
         //$result[] = !empty($result[]) ? $result[] : 0 ;
         //var_dump($result);exit;
        $fid = $result['former_invest_id'];
        $results4 = $model->where(['id' => $fid])->with("profits")->asArray()->one();
        $lid = $result['latter_invest_id'];
        $results5 = $model->where(['id' => $lid])->with("profits")->asArray()->one();
        $investItem = [];
        if (!empty($result)) {
            $investItem['project_name'] = $result['name'];
            $investItem['jtype'] = Project::$typeList[$result['jtype']];
            $investItem['itype'] = Project::$typeList[$result['itype']];
            $investItem['total_money'] = $result['total_money'];
            $investItem['apr'] = $result['apr'];
            $investItem['period'] = $result['period'];
            $investItem['is_day'] = $result['is_day'];
            $investItem['project_status'] = Project::$status[$result['jstatus']];
            $investItem['username'] = $result['username'];
            $investItem['invest_money'] = $result['invest_money'];
            $investItem['status'] = $result['istatus'];
            $investItem['duein_money'] = $result['duein_money'];
            $investItem['duein_profits'] = $result['duein_profits'];
            $investItem['project_apr'] = $result['project_apr'];
            if (isset(ProjectProfits::$status[$result['fstatus']])) {
                $investItem['profits_status'] = ProjectProfits::$status[$result['fstatus']];
            } else {
                $investItem['profits_status'] = "未知类型";
            }

            $investItem['interest_start_date'] = $result['interest_start_date'];
            $investItem['last_repay_date'] = $result['last_repay_date'];
            $investItem['created_at'] = $result['created_at'];
            $investItem['is_transfer'] = $result['is_transfer'];
            $investItem['transfer_money'] = $result['transfer_money'];
            $investItem['remark'] = $result['remark'];
        }
        //var_dump($investItem);exit;
        $formItem = [];
        if (!empty($results4)) {
            $formItem['invest_money'] = $results4['invest_money'];
            if (isset(ProjectProfits::$status[$results4['profits']['status']])) {
                $formItem['status'] = ProjectProfits::$status[$results4['profits']['status']];
            } else {
                $formItem['status'] = "未知类型";
            }
            $formItem['project_apr'] = $results4['profits']['project_apr'];
        }
        $latterItem = [];
        if (!empty($results5)) {
            $latterItem['usernmae'] = $results5['username'];
            $latterItem['invest_money'] = $results5['invest_money'];
            // $formItem['status'] = $results4['status'];
            if (isset(ProjectProfits::$status[$results5['status']])) {
                $latterItem['status'] = ProjectProfits::$status[$results5['status']];
            } else {
                $latterItem['status'] = "未知类型";
            }
            $latterItem['project_apr'] = $results5['profits']['project_apr'];
            $latterItem['start_date'] = $results5['profits']['interest_start_date'];
            $latterItem['repay_date'] = $results5['profits']['last_repay_date'];
        }
        // var_dump($formItem);exit;
      
        $results3 = $model->where(['id' => $id])->with([
            "cabaseinfo" => function(ActiveQuery $caquery) {
                $caquery->select([
                    'id',
                    'user_name',
                    'assign_rate',
                    'assign_fee',
                    'assign_start_date',
                    'assign_end_date',
                    'commission_rate',
                    'status',
                ]);
            }
        ])    ->asArray()->one();

        if (!empty($results3)) {
            $creditList = [];
            $creditList['id'] = $results3['cabaseinfo']['id'];
            $creditList['user_name'] = $results3['cabaseinfo']['user_name'];
            $creditList['assign_rate'] = $results3['cabaseinfo']['assign_rate'];
            $creditList['assign_fee'] = $results3['cabaseinfo']['assign_fee'];
            $creditList['assign_start_date'] = $results3['cabaseinfo']['assign_start_date'];           
            $creditList['assign_end_date'] = $results3['cabaseinfo']['assign_end_date'];
            $creditList['commission_rate'] = $results3['cabaseinfo']['commission_rate'];
            if (isset(CreditBaseInfo::$status[$results3['cabaseinfo']['status']])) {
                $creditList['status'] = CreditBaseInfo::$status[$results3['cabaseinfo']['status']];
            } else {
                $creditList['status'] = "未知类型";
            }
        }
        //var_dump($creditList);exit;
        return $this->render('view', [
            'model' => $model,
            'creditList'=>$creditList,
            'investItem'=> $investItem,
            'formItem'=>$formItem,
            'latterItem'=>$latterItem,
        ]);
    }

    /**
     * 转让记录列表
     */
    public function actionAssign() {
        $search = array();
        $condition = '1=1';
        if ($this->request->get('search_submit')) { // 过滤
            $search = $this->request->get();
            //  var_dump($search);exit;

        }

        $query = CreditBaseInfo::find()->where($condition)->orderBy('id desc');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 15;
        $results = $query->offset($pages->offset)->limit($pages->limit)->with([
            // 2. 通过 with("project") 关联用户表，获得投资者姓名
            "project" => function(ActiveQuery $projectquery) {
                $projectquery->select([
                    'id',
                    'name',
                ]);
            }
        ])->asArray()->all();
        $assignList = [];
        if (!empty($results)) {
            foreach ($results as $value) {
                $assign['id'] = $value['id'];
                $assign['project_id'] = $value['project_id'];
                $assign['invest_id'] = $value['invest_id'];
                $assign['user_name'] = $value['user_name'];
                $assign['assign_fee'] = $value['assign_fee'];
                $assign['assign_rate'] = $value['assign_rate'];
                $assign['assign_start_date'] = $value['assign_start_date'];
                $assign['assign_end_date'] = $value['assign_end_date'];
                $assign['commission_rate'] = $value['commission_rate'];
                $assign['status'] = $value['status'];
                if (isset(CreditBaseInfo::$status[$value['status']])) {
                    $assign['status'] = CreditBaseInfo::$status[$value['status']];
                } else {
                    $assign['status'] = "未知类型";
                }
                $assign['project'] = $value['project']['name'];
                $assignList[] = $assign;
            }
        }
        // var_dump($assignList);exit;
        return $this->render('assign', [
            'assignList' => $assignList,
            'pages' => $pages,
        ]);
    }



    protected function findModel($id) {
        if (($model = projectInvest::find($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
