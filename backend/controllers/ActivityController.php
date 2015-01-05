<?php
namespace backend\controllers;

use Yii;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;
use backend\controllers\BaseController;
use common\models\Activity;

/**
 * Activity controller
 */
class ActivityController extends BaseController
{
    /**
    * 活动列表
    */
    public function actionList()
    {
        $condition = $this->getFilterCondition(); // 过滤
        $query = Activity::find()->where($condition)->orderBy('id desc');
         $countQuery = clone $query;
         $pages = new Pagination(['totalCount' => $countQuery->count()]);
         $pages->pageSize = 15;
         $activitys = $query->offset($pages->offset)->limit($pages->limit)->all();

         return $this->render('list', [
            'activitys' => $activitys,
            'pages' => $pages,
        ]);
    }

    /**
     * 添加活动
     */
    public function actionAdd()
    {
        $model = new Activity();
        // 有提交则装载post值并验证
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $model->create_user = Yii::$app->user->identity->username;
            $image = UploadedFile::getInstance($model, 'thumbnail');
            // 上传目录到frontend/web下面去(绝对路径)
            $dir = Yii::getAlias('@frontend/web') . '/' . Yii::$app->params['activityImgPath'];
            $imgbasename = 'activity_thumbnail'.date('Ymd',time()).rand(10000,99999);
           if (is_object($image)) { 
                $model->thumbnail = $imgbasename.'.'.$image->extension;
                if (!is_dir($dir)) {
                   FileHelper::createDirectory($dir);
                }                        
                $image->saveAs($dir.'/'.$model->thumbnail);
            }
            if ($model->save()) {
                return $this->redirectMessage('添加成功', self::MSG_SUCCESS, Url::toRoute('activity/list'));
            } else {
            	//print_r($model->getErrors());
                return $this->redirectMessage('添加失败', self::MSG_ERROR);
            }
        }
            return $this->render('add', array(
            'model' => $model,
        ));
    }

    /**
     * 编辑活动
     */
    public function actionEdit($id)
    {
        $id = intval($id);
        $model = $this->findModel($id);
        // 有提交则装载post值并验证
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) { 
            $image = UploadedFile::getInstance($model, 'thumbnail');
            // 上传目录到frontend/web下面去(绝对路径)
            $dir = Yii::getAlias('@frontend/web') . '/' . Yii::$app->params['activityImgPath'];
            $imgbasename = $id.'_activity_thumbnail'.date('Ymd',time()).rand(10000,99999);
            if (is_object($image)) { 
                $model->thumbnail = $imgbasename.'.'.$image->extension;
                if (!is_dir($dir)) { 
                    FileHelper::createDirectory($dir);
                }
                $image->saveAs($dir.'/'.$model->thumbnail);                
            } else {
                $model->thumbnail = $model->getOldAttribute('thumbnail');
            }
            if ($model->save()) {
                return $this->redirectMessage('编辑成功', self::MSG_SUCCESS, Url::toRoute('activity/list'));
            } else {
                return $this->redirectMessage('编辑失败', self::MSG_ERROR);
            }
        }
        return $this->render('edit', array(
            'model'     => $model,
        ));
    }

    /**
     * 删除活动
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['activity/list']);
    }

    /**
    ********************
    */
    protected function getFilterCondition()
    {
        $search = array();
        $condition = '1=1';
        if ($this->request->get('search_submit')) {
            $search = $this->request->get();
            if ($search['id'] != '' ) {
                $condition .= " AND id = " . intval($search['id']);
            }
            if ($search['title'] != '') {
                $condition .= " AND title LIKE '%" . trim($search['title']) . "%'";
            }
        }
        return $condition;
    }

    protected function findModel($id)
    {
        if (($model = Activity::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}