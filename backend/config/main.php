<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
	require(__DIR__ . '/../../environments/' . YII_ENV . '/common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/../../environments/' . YII_ENV . '/backend/config/params-local.php')
);

return [
    'id' => 'app-backend',
    'name' => '口袋理财',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'main/index',
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
	    'view' => [
	    	'class' => 'backend\components\View',
	    ],
        'user' => [
            'identityClass' => 'backend\models\AdminUser',
            'loginUrl' => ['main/login'],
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
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'main/error',
        ],
    ],
    'params' => $params,
];
