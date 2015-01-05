<?php

namespace mobile\components;

class Request extends \yii\web\Request
{
	/**
	 * 覆盖框架的获取IP地址的实现
	 */
	public function getUserIP()
	{
		if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
			$cip = $_SERVER["HTTP_CLIENT_IP"];
		} elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			$cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		} elseif (!empty($_SERVER["REMOTE_ADDR"])) {
			$cip = $_SERVER["REMOTE_ADDR"];
		} else{
			$cip = "";
		}
		return $cip;
	}
}