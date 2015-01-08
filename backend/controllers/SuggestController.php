<?php
namespace backend\controllers;

use common\models\GuestSuggest;
use yii\data\Pagination;

/**
 * Suggest controller
 */
class SuggestController extends BaseController
{   
    /**
     * 反馈信息列表
     */
	public function actionList()
	{
		$query = GuestSuggest::find()->orderBy('id desc');
		$countQuery = clone $query;
		$pages = new Pagination(['totalCount' => $countQuery->count()]);
		$pages->pageSize = 15;
		$list = $query->offset($pages->offset)->limit($pages->limit)->all();
		
		return $this->render('list', ['suggestList' => $list,'pages' => $pages]);
	}
}
