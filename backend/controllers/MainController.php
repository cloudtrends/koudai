<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use backend\controllers\BaseController;
use backend\models\LoginForm;
use backend\models\AdminUserRole;

/**
 * Main controller
 */
class MainController extends BaseController
{
	public $verifyPermission = false;
	
	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'rules' => [
					[
						'actions' => ['login', 'error', 'captcha'],
						'allow' => true,
					],
					[
						'actions' => ['index', 'logout', 'home'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'testLimit' => 1,
				'height' => 35,
				'width' => 80,
				'padding' => 0,
				'minLength' => 4,
				'maxLength' => 4,
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
		];
	}
	
	/**
	 * 登录
	 */
	public function actionLogin()
	{
		// 已经登录则直接跳首页
		if (!\Yii::$app->user->isGuest) {
			return $this->goHome();
		}

		$this->layout = false;
		$model = new LoginForm();

		if ($model->load($this->request->post()) && $model->login()) {
			// 把权限信息存到session中
			if ($model->getUser()->role) {
				$roleModel = AdminUserRole::findOne(['name' => $model->getUser()->role]);
				if ($roleModel) {
					Yii::$app->getSession()->set('permissions', $roleModel->permissions);
				}
			}
			$this->goHome();
		}
	
		return $this->render('login', [
			'model' => $model,
		]);
	}
	
	/**
	 * 外层框架首页
	 */
	public function actionIndex()
	{
		include_once '../config/menu.php';
		$this->layout = false;
		return $this->render('index', array(
			'topmenu'	=> $topmenu,
			'menu'		=> $menu,
		));
	}
	
	/**
	 * iframe里面首页
	 */
	public function actionHome()
	{
		return $this->render('home');
	}
	
	/**
	 * 退出
	 */
	public function actionLogout()
	{
		Yii::$app->user->logout();
		return $this->redirect(['login']);
	}
}