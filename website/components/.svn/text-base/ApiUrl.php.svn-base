<?php
namespace website\components;

class ApiUrl extends \yii\helpers\BaseUrl
{
	public static function toRoute($route, $scheme = true)
	{
		$url = parent::toRoute($route, $scheme);
		return str_replace(
			['website/', 'www.koudailc.com'],
			['frontend/', 'api.koudailc.com'],
			$url
		);
	}
	
	public static function to($url = '', $scheme = true)
	{
		$url = parent::to($url, $scheme);
		return str_replace(
			['website/', 'www.koudailc.com'],
			['frontend/', 'api.koudailc.com'],
			$url
		);
	}
}