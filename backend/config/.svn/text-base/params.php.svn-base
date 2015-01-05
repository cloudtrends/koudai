<?php
return [
    // 接口文档配置
    'apiList' => [
	    [
		    'class' => 'frontend\controllers\AppController',
		    'label' => '全局接口',
	    ],
		[
			'class' => 'frontend\controllers\UserController',
			'label' => '用户基本',
		],
		[
			'class' => 'frontend\controllers\AccountController',
			'label' => '用户资金/交易',
		],
		[
			'class' => 'frontend\controllers\KoudaibaoController',
			'label' => '口袋宝',
		],
		[
			'class' => 'frontend\controllers\ProjectController',
			'label' => '安稳袋/金融袋',
		],
        [
            'class' => 'frontend\controllers\CreditController',
            'label' => '债权转让',
        ],
        [
        	'class' => 'frontend\controllers\PageController',
        	'label' => '更多相关',
        ],
		// 下面这种方式后面考虑支持
// 		[
// 			'class' => null,
// 			'label' => '用户资金',
// 			'actions' => [
// 				'frontend\controllers\UserController::actionLogin',
// 				'frontend\controllers\UserController::actionRegister',
// 			]
// 		]
	],
	// 权限配置Controller,只能是后台backend命名空间下的
	'permissionControllers' => [
		'AdminUserController' => '系统管理员',
		'ArticleController' => '文章管理',
		'ArticleTypeController' => '文章栏目管理',
		'InvestController' => '投资记录',
		'KoudaibaoController' => '口袋宝',
		'MsgPushController' => '消息推送',
		'ProjectController' => '项目',
		'SuggestController' => '用户反馈',
		'UserController' => '用户管理',
	],
];
