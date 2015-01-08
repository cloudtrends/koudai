<?php

namespace common\models;

class AppConfig
{
	// 后面可能存db或缓存，所有获取配置通过方法获取
	private static $_configs = [
		'callCenter'		=> '400-002-0802',
		'callQQGroup'		=> '421985497',
	];
	
	public static function getConfig($key)
	{
		return self::$_configs[$key];
	}
	
	public static function getConfigs()
	{
		return self::$_configs;
	}
}