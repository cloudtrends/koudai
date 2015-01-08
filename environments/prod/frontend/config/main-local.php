<?php
$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Wwpn7m5wzKDA2q141a6UVLKfK4lrfi-X',
        ],
    ],
];

// if (YII_DEBUG) {
// 	$config['bootstrap'][] = 'debug';
// 	$config['modules']['debug'] = [
// 		'class' => 'yii\debug\Module',
// 		'allowedIPs' => ['127.0.0.1', '::1', '*.*.*.*'],
// 	];
// }

return $config;
