<?php
/**
 * 定义一些全局宏和配置，不要定义太多
 */
return [
	'appConfig' => [
		'iosVersion'			=> '1.1.0',
		'iosForceUpgrade'		=> 0,
		'androidVersion'		=> '1.0.0',
		'androidForceUpgrade'	=> 0,
		'androidDownloadUrl'	=> 'http://www.koudailc.com/attachment/download/koudailicai.apk',
	],
	// 单日提现限制
	'withdraw' => [
		'daily_times_limit' => 5,		// 次数
		'daily_money_limit' => 1000,	// 限额，暂未使用
	],
    // 短信服务配置
    'smsService' => [
    	'url' 		=> 'http://210.5.158.31:9011/hy/',
    	'uid'		=> '80244',
    	'code' 		=> 'kdlc',
    	'password'	=> 'asd1234',
    ],
    //云片网络
    'smsService1' => [
    	'url' 		=> 'http://yunpian.com/v1/sms/send.json',
    	'apikey' 		=> '72c972af1c2e6a351cdbe77534565e80 ',
    ],
    // 支持绑定的银行卡列表
    'supportBanks' => [
    	[
    		'code' => '1',
    		'abbreviation' => 'ICBC', // 缩写
    		'name' => '工商银行',
    		'sml' => 5000000,	// 单笔限额
    		'dml' => 5000000,	// 单日限额
    		'dtl' => 5,			// 单日次数限额
    	],
    	[
	    	'code' => '2',
	    	'abbreviation' => 'ABC',
	    	'name' => '农业银行',
	    	'sml' => 2000000,
	    	'dml' => 2000000,
	    	'dtl' => 5,
    	],
    	[
	    	'code' => '3',
	    	'abbreviation' => 'CEB',
	    	'name' => '光大银行',
	    	'sml' => 5000000,
	    	'dml' => 50000000,
	    	'dtl' => 5,
    	],
    	[
	    	'code' => '4',
	    	'abbreviation' => 'PSBC',
	    	'name' => '邮政储蓄银行',
	    	'sml' => 100000000,
	    	'dml' => 100000000,
	    	'dtl' => 5,
    	],
    	[
	    	'code' => '5',
	    	'abbreviation' => 'CIB',
	    	'name' => '兴业银行',
	    	'sml' => 50000000,
	    	'dml' => 50000000,
	    	'dtl' => 5,
    	],
    	[
	    	'code' => '6',
	    	'abbreviation' => 'SDB',
	    	'name' => '深圳发展银行',
	    	'sml' => 50000000,
	    	'dml' => 50000000,
	    	'dtl' => 5,
    	],
    	[
	    	'code' => '7',
	    	'abbreviation' => 'CCB',
	    	'name' => '建设银行',
	    	'sml' => 500000,
	    	'dml' => 1000000,
	    	'dtl' => 5,
    	],
    ],
    // 活动图片路径，frontend/web下面
    'activityImgPath' => 'attachment/activity',
];
