<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../environments/' . YII_ENV . '/common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/../../environments/' . YII_ENV . '/frontend/config/params-local.php')
);

return [
    'id' => 'app-frontend',
    'name' => '口袋理财',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
	    'urlManager' => [
		    'enablePrettyUrl' => true,
		    'showScriptName' => false,
		    'rules' => [
		    	'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
		    ],
	    ],
        'user' => [
            'identityClass' => 'common\models\User',
            // 允许使用auth_key来自动登录
            'enableAutoLogin' => true,
            // 设为null避免跳转
            'loginUrl' => null,
        ],
        'session' => [
        	// 使用redis做session
        	'class' => 'yii\redis\Session',
        	'redis' => 'redis',
        	// 与后台区分开会话key，保证前后台能同时单独登录
        	'name' => 'SESSIONID',
        	'timeout' => 20 * 24 * 3600,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info'],
                    'categories' => ['koudai.pay.*'],
                    'logFile' => '@runtime/logs/notify.log',
                    'logVars' => [],
                ],[
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'info'],
                    'categories' => ['koudai.llpay.*'],
                    'logFile' => '@runtime/logs/llnotify.log',
                    'logVars' => [],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],

            ],
        ],
        // 下面是扩展了系统的组件
        'errorHandler' => [
        	'class' => 'frontend\components\ErrorHandler',
        ],
        'request' => [
        	'class' => 'frontend\components\Request',
        ],
    ],
    'params' => $params,
];
