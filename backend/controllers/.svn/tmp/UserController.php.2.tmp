<?php
namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use backend\controllers\BaseController;
use common\models\User;
use common\models\UserBankCard;
use common\models\UserLoginLog;
use common\models\NoticeSms;
use common\models\UserDetail;
use common\models\BankConfig;
use common\models\UserUnbindCard;

/**
 * User controller
 */
class UserController extends BaseController
{
    /**
     * 用户基本信息列表
     */
    public function actionList()
    {
        $condition = $this->getFilterCondition(); // 过滤
        $query = User::find()->where($condition)->orderBy('id desc');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 15;
        $users = $query->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('list', [
            'users' => $users,
            'pages' => $pages,
		]);
    }

    /**
     * 编辑用户
     */
    public function actionEdit($id)
    {
        $id = intval($id);
        $model = $this->findModel($id);
        // 有提交则装载post值并验证
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            if ($model->save()) {
                 return $this->redirectMessage('编辑成功', self::MSG_SUCCESS, Url::toRoute('user/list'));
            } else {
                 return $this->redirectMessage('编辑失败', self::MSG_ERROR);
            }
        }
		return $this->render('edit', array( 'model'=> $model)); 
    }

    /**
     * 禁用用户
     */
    public function actionDelete($id,$status)
    {
        $id = intval($id);
        $status = intval($status) ? 0 : 1;
        $model = $this->findModel($id);
        $model->status = $status;
        $model->update();
        return $this->redirect(['user/list']);
    }
    
    /**
     * 用户详细信息列表
     */
    public function actionDetailList()
    {
    	$search = [];
    	$condition = '1=1';
    	if ($this->request->get('search_submit')) {
    		$search = $this->request->get();
    		if (isset($search['id']) && $search['id'] != '' ) {
    			$condition .= " AND user_id = " . intval($search['id']);
    		}
    		if (isset($search['username']) && $search['username'] != '') {
    			$condition .= " AND username LIKE '%" . trim($search['username']) . "%'";
    		}
    	}
    	$query = UserDetail::find()->where($condition)->orderBy('id desc');
    	$countQuery = clone $query;
    	$pages = new Pagination(['totalCount' => $countQuery->count()]);
    	$pages->pageSize = 15;
    	$users = $query->offset($pages->offset)->limit($pages->limit)->all();
    	
    	return $this->render('detail-list', [
			'users' => $users,
			'pages' => $pages,
		]);
    }
    
    /**
     * 登录日志
     */
    public function actionLoginLog()
    {
        $search = [];
        $condition = '1=1';
        if ($this->request->get('search_submit')) {
            $search = $this->request->get();
            if ($search['user_id'] != '' ) {
                $condition .= " AND user_id = " . intval($search['user_id']);
            }
            if ($search['type'] != '') {
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
        } // 过滤 
    	$query = UserLoginLog::find()->where($condition)->orderBy('id desc');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 15;
        $user_login_logs = $query->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('loglist', [
            'user_login_logs' => $user_login_logs,
            'pages' => $pages,
        ]);
    }
    
    /**
     * 银行卡信息
     */
    public function actionBanks()
    {
        $search = [];
        $condition = '1=1';
        if ($this->request->get('search_submit')) {
            $search = $this->request->get();
            if ($search['user_id'] != '' ) {
                $condition .= " AND user_id = " . intval($search['user_id']);
            }
            if ($search['bind_phone'] != '') {
                $condition .= " AND bind_phone LIKE '%" . trim($search['bind_phone']) . "%'";
            }
        } // 过滤        
    	$query = UserBankCard::find()->where($condition)->orderBy('id desc');
    	$countQuery = clone $query;
    	$pages = new Pagination(['totalCount' => $countQuery->count()]);
    	$pages->pageSize = 15;
    	$banks = $query->offset($pages->offset)->limit($pages->limit)->all();
    	return $this->render('banks', [
			'banks' => $banks,
			'pages' => $pages,
		]);
    }
    
    /**
     * 解绑
     * @param integer $user_id
     */
    public function actionUnBindCard($user_id)
    {
    	$user = User::findOne(intval($user_id));
    	$bank = UserBankCard::findOne(['user_id' => $user->id]);
    	
    	$payService = Yii::$container->get('payService');
    	if ($bank->third_platform == BankConfig::PLATFORM_UMPAY) {
    		$ret = $payService->unBindCard($bank['bind_phone']);
    	} else {
    		// 连连的卡解绑先直接在我们这置为未绑定，暂不真实调用第三方支付解绑
    		$ret['code'] = 0;
    	}
    	
    	if ($ret['code'] == 0 or $ret['code'] == "00060064") {
    		$no_agree = $bank->no_agree;
    		$no_order = $bank->no_order;
    		
    		$bank->status = UserBankCard::STATUS_UNBIND;
    		$bank->no_agree = '';
    		$bank->no_order = '';
    		$user->card_bind_status = UserBankCard::STATUS_UNBIND;
    		$user->save();
    		$bank->save();
    		
    		// 把解绑的卡存到另一个表中，方便以后有需要
    		$unbindCard = new UserUnbindCard();
    		$unbindCard->no_agree = $no_agree;
    		$unbindCard->no_order = $no_order;
    		$unbindCard->user_id = $bank->user_id;
    		$unbindCard->user_account_id = $bank->user_account_id;
    		$unbindCard->bank_id = $bank->bank_id;
    		$unbindCard->bank_name = $bank->bank_name;
    		$unbindCard->bank_detail = $bank->bank_detail;
    		$unbindCard->bind_phone = $bank->bind_phone;
    		$unbindCard->bind_result = $bank->bind_result;
    		$unbindCard->third_platform = $bank->third_platform;
    		$unbindCard->card_no = $bank->card_no;
    		$unbindCard->status = $bank->status;
    		$unbindCard->save();
    		
    		return $this->redirectMessage('解绑成功', self::MSG_SUCCESS);
    	} else {
    		return $this->redirectMessage("解绑失败，code:{$ret['code']},message:{$ret['message']}", self::MSG_ERROR);
    	}
    }

    /**
     ********************
     */
    protected function getFilterCondition()
    {
        $search = [];
        $condition = '1=1';
        if ($this->request->get('search_submit')) {
            $search = $this->request->get();
            if (isset($search['id']) && $search['id'] != '' ) {
        		$condition .= " AND id = " . intval($search['id']);
            }
            if (isset($search['username']) && $search['username'] != '') {
        		$condition .= " AND username LIKE '%" . trim($search['username']) . "%'";
            }
        }
        return $condition;
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * 消息列表
     */
    public function actionNotice(){
        $search = [];
        $condition = '1=1';
        if ($this->request->get('search_submit')) {
            $search = $this->request->get();
            if ($search['user_id'] != '' ) {
                $condition .= " AND user_id = " . intval($search['user_id']);
            }
            if ($search['type'] != '') {
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
        } // 过滤
        $query = NoticeSms::find()->where($condition)->orderBy('id desc');
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 15;
        $NoticeData = $query->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('noticelist', [
            'NoticeData' => $NoticeData,
            'pages' => $pages,
        ]);
    }
}
