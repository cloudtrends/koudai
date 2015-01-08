<?php

namespace common\models;

/**
 * Client model
 */
class Client
{
	const TYPE_IOS = 'ios';
	const TYPE_ANDROID = 'android';
	const TYPE_PC = 'pc';
	const TYPE_H5 = 'h5';
	
	// 终端类型
	public $clientType;
	// 设备名称
	public $deviceName;
	// app版本
	public $appVersion;
	// 终端操作系统版本
	public $osVersion;
	// app来源市场
	public $appMarket;
	
	// 序列化
	public function serialize()
	{
		return serialize([
			'clientType' => $this->clientType,
			'deviceName' => $this->deviceName,
			'appVersion' => $this->appVersion,
			'osVersion' => $this->osVersion,
			'appMarket' => $this->appMarket,
		]);
	}
}