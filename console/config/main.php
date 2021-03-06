<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../environments/' . YII_ENV . '/common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/../../environments/' . YII_ENV . '/console/config/params-local.php')
);

return [
    'id' => 'app-console',
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'targets' => [
	            [
		            'class' => 'yii\log\FileTarget',
		            'levels' => ['error', 'warning','info'],
		            'except' => [
			            'console\controllers\ProfitsController',
			            'console\controllers\KoudaibaoController',
		            ],
	            ],
	            // 各业务日志单独文件记录
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['console\controllers\ProfitsController'],
                    'logFile' => '@runtime/logs/ProfitsController.log',
                ],
                [
	                'class' => 'yii\log\FileTarget',
	                'levels' => ['error', 'warning', 'info'],
	                'categories' => ['console\controllers\KoudaibaoController'],
	                'logFile' => '@runtime/logs/KoudaibaoController.log',
                ],
            ],
        ],
    ],
    'params' => $params,
];
