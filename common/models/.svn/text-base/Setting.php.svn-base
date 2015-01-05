<?php

namespace common\models;

/**
 * Setting model
 */
class Setting extends \yii\db\ActiveRecord
{
	/**
	 * 为了避免配置key的管理混乱，增加配置项都要在此添加key，并注释
	 */
	public static $keys = [
		'project_index'		// 首页项目配置
	];
	
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%setting}}';
	}
	
	/**
	 * Find by key
	 */
	public static function findByKey($key)
	{
		return static::findOne(['skey' => $key]);
	}
	
	/**
	 * 更新配置，如果不存在则创建
	 * @param string $key
	 * @param string $value
	 */
	public static function updateSetting($key, $value)
	{
		if (!in_array($key, static::$keys)) return false;
		
		$setting = static::findByKey($key);
		if (!$setting) {
			$setting = new Setting();
		}
		$setting->skey = $key;
		$setting->svalue = $value;
		return $setting->save();
	}
}