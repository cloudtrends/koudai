<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../environments/' . YII_ENV . '/common/config/params-local.php'),
    require(__DIR__ . '/params.php')
);


return [
    'id' => 'app-website',
    'name' => '口袋理财',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'website\controllers',
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
        ],
        'session' => [
        	// 使用redis做session
        	'class' => 'yii\redis\Session',
        	'redis' => 'redis',
        	// 与后台区分开会话key，保证前后台能同时单独登录
        	'name' => 'SESSIONID',
        	'timeout' => 24 * 3600,
            'keyPrefix' => substr(md5("app-website"), 0, 5),
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],

            ],
        ],
        'request' => [
        	'class' => 'website\components\Request',
            'cookieValidationKey' => 'Wwpn7m5wzKDA2q141a6UVLKfK4lrfi-X',
        ],
        'view' => [
        	'class' => 'website\components\View',
        ],
    ],
    'params' => $params,
];
