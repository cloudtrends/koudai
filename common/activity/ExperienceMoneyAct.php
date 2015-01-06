<?php
namespace common\activity;

/**
 * 注册送体验金活动
 */
class ExperienceMoneyAct
{
	public static $config = [
		'money' => 500000,					// 5000体验金
		'profits_time' => 15,				// 体验金计息15天
		'extend_invest_money' => 100000,	// 投资满1000延计息时间
		'extend_profits_time' => 10,		// 投资满1000延计息天数
	];
}