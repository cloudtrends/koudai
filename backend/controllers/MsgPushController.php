<?php
namespace backend\controllers;


use yii;
use yii\data\Pagination;
use yii\helpers\Url;
use common\models\MsgPushTask;
use common\services\MsgPushService;
use common\models\User;



class MsgPushController extends BaseController
{

    protected $msgPushService;

    public function __construct($id, $module, MsgPushService $msgPushService, $config = [])
    {
        $this->msgPushService = $msgPushService;
        parent::__construct($id, $module, $config);
    }

    // 任务列表展示
    public function actionList()
    {

        $query = MsgPushTask::find();
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $pages->pageSize = 15;
        $taskList = $query->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render("list",[
            'taskList' => $taskList,
            'taskType' => MsgPushTask::$taskTypeDesc,
            'action' => $this->action->id,
            'statusDesc' => MsgPushTask::$statusDesc,
            'pages' => $pages,
        ]);
    }

    // 任务添加
    public function actionAdd() {
        $model = new MsgPushTask();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $model->task_type_desc = MsgPushTask::$taskTypeDesc[$model->task_type];
            if ($_POST["MsgPushTask"]["task_type"] != 1) {
                $receiver_list1 = trim($_POST["MsgPushTask"]["receiver_list"]);
                $receiver_list = str_replace(array("\r\n", " ", "\r", "\n"), ',', $receiver_list1);
                $user_arr = explode(',', $receiver_list);
                foreach ($user_arr as $user_arr_Key => $user_arr_Val) {
                    if (!empty($user_arr[$user_arr_Key])) {
                        $user_arr[$user_arr_Key] = trim($user_arr[$user_arr_Key]);
                    }
                }
                //var_dump( $receiver_list);exit;
                $sql = 'select username from tb_user';
                $result = Yii::$app->db->createCommand($sql)->queryAll();
                foreach ($result as $k => $v) {

                    $usernames[] = $v["username"];
                }
                $a = array_diff($user_arr, $usernames);
                $e =  implode(" ", $a);
                $err = implode(" ", $a) . "号码不正确或未注册";
                
                $model->addError('receiver_list', $err);
                if (empty($e)) {
                    if ($model->save()) {
                        return $this->redirectMessage('编辑成功', self::MSG_SUCCESS, Url::toRoute('msg-push/list'));
                    } else {
                        return $this->redirectMessage('编辑失败', self::MSG_ERROR);
                    }
                }
            } else {
                if ($model->save()) {
                    return $this->redirectMessage('编辑成功', self::MSG_SUCCESS, Url::toRoute('msg-push/list'));
                } else {
                    return $this->redirectMessage('编辑失败', self::MSG_ERROR);
                }
            }
        }
        //echo "getRequest:" . var_export(Yii::$app->getRequest()->post(),true);
        return $this->render("add", [
                    'model' => $model,
                    'taskType' => MsgPushTask::$taskTypeDesc,
                    'action' => $this->action->id,
                    'statusDesc' => MsgPushTask::$statusDesc,                  
        ]);
    }

    // 任务编辑
    public function actionEdit($task_id)
    {
        $model = MsgPushTask::findOne(["task_id" => $task_id]);
        if( $model->load(Yii::$app->getRequest()->post()) && $model->validate())
        {
            if ($_POST["MsgPushTask"]["task_type"] != 1) {
                $receiver_list1 = trim($_POST["MsgPushTask"]["receiver_list"]);
                $receiver_list = str_replace(array("\r\n", " ", "\r", "\n"), ',', $receiver_list1);
                $user_arr = explode(',', $receiver_list);
                foreach ($user_arr as $user_arr_Key => $user_arr_Val) {
                    if (!empty($user_arr[$user_arr_Key])) {
                        $user_arr[$user_arr_Key] = trim($user_arr[$user_arr_Key]);
                    }
                }
                //var_dump( $receiver_list);exit;
                $sql = 'select username from tb_user';
                $result = Yii::$app->db->createCommand($sql)->queryAll();
                foreach ($result as $k => $v) {

                    $usernames[] = $v["username"];
                }
                $a = array_diff($user_arr, $usernames);
                $e =  implode(" ", $a);
                $err = implode(" ", $a) . "号码不正确或未注册";
                
                $model->addError('receiver_list', $err);
                if (empty($e)) {
                    if ($model->save()) {
                        return $this->redirectMessage('编辑成功', self::MSG_SUCCESS, Url::toRoute('msg-push/list'));
                    } else {
                        return $this->redirectMessage('编辑失败', self::MSG_ERROR);
                    }
                }
            } else {
                if ($model->save()) {
                    return $this->redirectMessage('编辑成功', self::MSG_SUCCESS, Url::toRoute('msg-push/list'));
                } else {
                    return $this->redirectMessage('编辑失败', self::MSG_ERROR);
                }
            }
        }

        return $this->render("edit",[
            'model' => $model,
            'taskType' => MsgPushTask::$taskTypeDesc,
            'action' => $this->action->id,
            'statusDesc' => MsgPushTask::$statusDesc
        ]);
    }

    // 发送消息
    public function actionSend($task_id)
    {
        //$this->getResponse()->format = Response::FORMAT_JSON;
        $task = MsgPushTask::find()->where(["task_id" => $task_id])->asArray()->one();
        
        if(!empty($task))
        {
            $task_type = intval($task['task_type']);
            switch($task_type){
                case MsgPushTask::TEXT_MSG_PUSH:
                    $ret = $this->msgPushService->sendTextMsg($task['msg_content'],$task['receiver_list']);
                    break;
                case MsgPushTask::APP_PUSH_ALL:
                    $ret = $this->msgPushService->PushAllDevices($task['msg_content']);
                    break;
                case MsgPushTask::APP_PUSH_USERS:
                    $ret = $this->msgPushService->PushAccountList($task['msg_content'],$task['receiver_list']);
                    break;
                case MsgPushTask::APP_PUSH_ANDROID:
                     $ret = $this->msgPushService->PushAccountList($task['msg_content'],$task['receiver_list']);
                    break;
                case MsgPushTask::APP_PUSH_IOS:
                     $ret = $this->msgPushService->PushAccountListIOS($task['msg_content'],$task['receiver_list']);
                    break;
                default:
                    return $this->redirectMessage('消息发送失败(任务类型错误)', self::MSG_SUCCESS, Url::toRoute('msg-push/list'));
                    break;

            }

            if($ret['code'] == 0)
            {
                $this->msgPushService->updateStatus($task_id, MsgPushTask::STATUS_SENT_SUCCESS);
                return $this->redirectMessage("消息发送成功", self::MSG_SUCCESS, Url::toRoute('msg-push/list'));
            }

            $this->msgPushService->updateStatus($task_id, MsgPushTask::STATUS_SENT_FAILED);
            return $this->redirectMessage('消息发送失败('. $ret['msg'] . ")", self::MSG_ERROR);

        }

        $this->msgPushService->updateStatus($task_id,MsgPushTask::STATUS_SENT_FAILED);
        return $this->redirectMessage('消息发送失败', self::MSG_ERROR);
    }
}

