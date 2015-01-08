<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=rdsynufjezu3eae.mysql.rds.aliyuncs.com;dbname=koudai',
            'username' => 'kd',
            'password' => '123456',
            'tablePrefix' => 'tb_',
            'charset' => 'utf8',
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '121.41.113.29',
            'port' => 6379,
            'database' => 0,
        ],
    ],
];
