<?php
namespace frontend\controllers;

use Yii;
use yii\base\UserException;
use yii\db\Query;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;
use common\models\ArticleType;
use common\models\Article;
use common\models\Activity;
use common\models\GuestSuggest;

/**
 * Page controller
 */
class PageController extends BaseController
{
	/**
	 * 帮助中心
	 * 
	 * @name 帮助中心 [pageHelpList]
	 * @param integer $page 第几页
	 * @param integer $pageSize 每页个数
	 */
	public function actionHelpList($page = 1, $pageSize = 15)
	{
		$articleType = ArticleType::findOne(['name' => ArticleType::TYPE_HELP]);
		$page = $page > 1 ? intval($page) : 1;
		$pageSize = intval($pageSize);
		$offset = ($page - 1) * $pageSize;
		$articles = (new Query())->from(Article::tableName())->select([
			'id', 'title', 'summary', 'content', 'created_at'
		])->where([
			'type_id' => $articleType->id,
		])->orderBy([
			'order' => SORT_DESC,
		])->offset($offset)->limit($pageSize)->all();
		foreach ($articles as &$article) {
			$article['content'] = $article['summary'];
		}
		return [
			'code' => 0,
			'articles' => $articles,
		];
	}
	
	/**
	 * 公告中心
	 *
	 * @name 公告中心 [pageNoticeList]
	 * @param integer $page 第几页
	 * @param integer $pageSize 每页个数
	 */
	public function actionNoticeList($page = 1, $pageSize = 15)
	{
		$articleType = ArticleType::findOne(['name' => ArticleType::TYPE_NOTICE_CENTER]);
		$page = $page > 1 ? intval($page) : 1;
		$pageSize = intval($pageSize);
		$offset = ($page - 1) * $pageSize;
		$articles = (new Query())->from(Article::tableName())->select([
			'id', 'title', 'summary', 'content', 'created_at'
		])->where([
			'type_id' => $articleType->id,
		])->orderBy([
			'id' => SORT_DESC,
		])->offset($offset)->limit($pageSize)->all();
		return [
			'code' => 0,
			'articles' => $articles,
		];
	}
	
	/**
	 * 通用页面详情H5
	 * 
	 * @name 通用页面详情H5 [pageDetail]
	 * @param integer $id 页面id
	 * @uses 例如帮助中心详情等
	 */
	public function actionDetail($id)
	{
		$this->response->format = Response::FORMAT_HTML;
		
		$article = Article::findOne(intval($id));
		if (!$article) {
			throw new NotFoundHttpException('您访问的页面不存在');
		}
		$append = '<style type="text/css">.container{margin:16px;}</style>';
		return $this->render('detail', ['html' => $append . $article->content]);
	}
	
	/**
	 * 风险保证金H5
	 *
	 * @name 风险保证金H5 [pageFxbzj]
	 */
	public function actionFxbzj()
	{
		$this->response->format = Response::FORMAT_HTML;
		return $this->render('fxbzj');
	}

	/**
	 * 客户反馈：提交客户反馈信息
	 *
	 * @name	提交客户反馈信息 [pageAddSuggest]
	 * @method	post
	 * @param	integer $type 反馈类型
	 * @param	string $user_info 用户联系方式
	 * @param	string $content 反馈内容
	 * @author	oscarzhu
	 */
	public function actionAddSuggest()
	{
		$model = new GuestSuggest();
		$model->type = $this->request->post('type');
		$model->user_info = $this->request->post('user_info');
		$model->content = $this->request->post('content');
		$model->client_type = $this->client->clientType;
		$model->app_version = $this->client->appVersion;
		$model->device_name = $this->client->deviceName;
		$model->device_system_version = $this->client->osVersion;
		if (!Yii::$app->user->getIsGuest()) {
			$model->user_id = Yii::$app->user->id;
		} else {
			$model->user_id = 0;
		}
	
		if ($model->validate()) {
			if ($model->save()) {
				return [
					'code' => 0,
					'msg' => '提交成功，谢谢您的保贵意见！'
				];
			} else {
				throw new UserException('保存失败！请稍后重试！');
			}
		} else {
			throw new UserException(array_shift($model->getFirstErrors()));
		}
	}

	/**
	 * 获取活动列表中的所有活动
	 * 
	 * @name 获取列表中的所有活动 [pageActivityList]
	 */
	public function actionListActivity()
	{
		$activityList = (new Query())->from(Activity::tableName())->select([
			'id', 'title','abstract','thumbnail','created_at'
		])->where([
			'status' => Activity::STATUS_ACTIVE, //已发布
		])->orderBy([
			'id' => SORT_DESC,
		])->all();
		foreach ($activityList as &$activity) {
			$activity['thumbnail'] = Activity::getThumbnailAbsUrl($activity['thumbnail']);
			$activity['share_url'] = Url::toRoute(['page/activity-detail', 'id' => $activity['id']], true);
		}
		return [
			'code' => 0,
			'activityList' => $activityList
		];
	}
	
	/**
	 * 活动详情
	 * 
	 * @name 根据活动id获取活动详情H5 [pageActivityDetail]
	 * @param integer $id 活动id
	 */
	public function actionActivityDetail($id)
	{
		$this->response->format = Response::FORMAT_HTML;
		$activity = $this->findActivity(intval($id));
		if (!$activity) {
			throw new NotFoundHttpException('您访问的页面不存在');
		}
		// 为活动中心设置特有的样式
		$append = '<style type="text/css">img{width:100%;}</style>';
		return $this->render('detail', ['html' => $append . $activity->content]);
	}
	
	/**
	 * 获得APP分享信息
	 * 
	 * @name 获得APP分享信息 [pageShareInfo]
	 */
	public function actionShareInfo()
	{
		$url = Url::toRoute('page/share', true);
		return [
			'code' => 0,
			'data' => [
				'title' => Yii::$app->name,
				'desc' => '一款专注服务普通大众的移动理财工具，所有产品收益率均在8%+，支持随取随存，本息垫付；无需跑银行，1分钟即可完成投资；口袋理财在工商银行存入1000万风险保证金，随时保障投资人收益。还在犹豫什么？快快点击' . $url . '去下载吧。',
				'summary' => '口袋理财-专业的移动理财平台',
				'androidDownloadUrl' => $url,
				'url' => $url,
			],
		];
	}
	
	/**
	 * 分享页面H5
	 *
	 * @name 分享页面H5 [pageShare]
	 */
	public function actionShare()
	{
		$this->response->format = Response::FORMAT_HTML;
		return $this->render('share');
	}
	
	/**
	 * 协议页面H5
	 * 
	 * @name 协议页面H5 [pageAgreement]
	 * @param string $type 类型：[use:用户使用协议, buy: 	购买及债权转让协议, pay:支付服务协议]
	 */
	public function actionAgreement($type)
	{
		$this->response->format = Response::FORMAT_HTML;
		$type = 'agreement' . $type;
		if (!in_array($type, [ArticleType::TYPE_AGREEMENT_USE, ArticleType::TYPE_AGREEMENT_BUY, ArticleType::TYPE_AGREEMENT_PAY])) {
			throw new NotFoundHttpException();
		}
		$articleType = ArticleType::findOne(['name' => $type]);
		if (!$articleType) {
			throw new NotFoundHttpException();
		}
		$article = Article::find()->where(['type_id' => $articleType->id])->orderBy('order desc')->one();
		if (!$article) {
			throw new NotFoundHttpException();
		}
		$append = '<style type="text/css">.container{margin:16px;}</style>';
		return $this->render('detail', ['html' => $append . $article->content]);
	}
	
	/**
	 * 红点相关信息
	 * 
	 * @name 红点相关信息 [pageReddotInfo]
	 */
	public function actionReddotInfo()
	{
		// 最近一条活动
		$lastActivity = Activity::find()->where(['status' => Activity::STATUS_ACTIVE])->orderBy('id desc')->one();
		$articleType = ArticleType::findOne(['name' => ArticleType::TYPE_NOTICE_CENTER]);
		// 最近一条公告
		$lastNotice = Article::find()->where(['type_id' => $articleType->id])->orderBy('id desc')->one();
		return [
			'code' => 0,
			'lastActivityTime' => $lastActivity ? $lastActivity->created_at : 0,
			'lastNoticeTime' => $lastNotice ? $lastNotice->created_at : 0,
		];
	}
	
	/**
	 ***************************
	 */
	protected function findActivity($id)
	{
		if (($model = Activity::findOne($id)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException('The requested page does not exist.');
		}
	}
}