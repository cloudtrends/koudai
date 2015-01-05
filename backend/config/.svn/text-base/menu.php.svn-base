<?php

use yii\helpers\Url;

$topmenu = $menu = array();

// 一级菜单
$topmenu = array (
	'index' 		=> array('首页', Url::to(['main/home'])),
	'project' 		=> array('借款管理', Url::to(['project/setting-index'])),
	'financial' 	=> array('财务管理', Url::to(['account/list'])),
	'user'	 		=> array('用户管理', Url::to(['user/list'])),
	'content' 		=> array('内容管理', Url::to(['article/list'])),
// 	'app_config' 	=> array('app配置管理', Url::to(['app-conf-info/list'])),
	'app_info' 		=> array('App信息管理', Url::to(['device/list'])),
	'system' 		=> array('系统管理', Url::to(['admin-user/list'])),
);

// 二级菜单
$menu['index'] = array(
	'menu_home'	=> array('管理中心首页', Url::to(['main/home'])),
);

$menu['project'] = array(
	'menu_index_setting'		=> array('首页项目配置', Url::to(['project/setting-index'])),
	'menu_koudaibao_begin'		=> array('口袋宝', 'groupbegin'),
	'menu_koudaibao_setting'	=> array('全局配置', Url::to(['koudaibao/setting'])),
	'menu_koudaibao_invests'	=> array('投资记录', Url::to(['koudaibao/invests'])),
	'menu_koudaibao_rollouts'	=> array('转出记录', Url::to(['koudaibao/rollouts'])),
	'menu_koudaibao_stat'		=> array('资金统计', Url::to(['koudaibao/stat'])),
	'menu_koudaibao_end'	 	=> array('口袋宝', 'groupend'),
	'menu_project_begin'		=> array('安稳袋/金融袋', 'groupbegin'),
	'menu_project_list' 		=> array('项目列表', Url::to(['project/list'])),
	'menu_project_create' 		=> array('项目创建', Url::to(['project/create'])),
	'menu_project_review_new' 	=> array('项目初审', Url::to(['project/new-list'])),
	'menu_project_investing' 	=> array('项目投资中', Url::to(['project/investing-list'])),
	'menu_project_review_full'	=> array('项目满款复审', Url::to(['project/full-list'])),
	'menu_project_repay_list'	=> array('项目还款', Url::to(['project/repay-list'])),
	'menu_project_end'	 		=> array('安稳袋/金融袋', 'groupend'),
	'menu_invest_begin'			=> array('投资信息', 'groupbegin'),
	'menu_invest_invests'       => array('投资记录', Url::to(['invest/invests'])),	
	'menu_invest_assign'        => array('转让信息', Url::to(['invest/assign'])),
	'menu_invest_end'	 		=> array('投资信息', 'groupend'),
);

$menu['financial'] = array(
	'menu_account_begin'			=> array('用户资金管理', 'groupbegin'),
	'menu_account_list'				=> array('用户资金信息', Url::to(['account/list'])),
	'menu_account_profits'			=> array('用户收益日志', Url::to(['account/daily-profits'])),
	'menu_account_log'				=> array('用户资金流水', Url::to(['account/stat'])),
	'menu_account_withdraw'			=> array('提现管理', Url::to(['account/withdraw'])),
// 	'menu_account_recharge'			=> array('后台充值', Url::to(['account/recharge'])),
	'menu_account_end'				=> array('用户资金管理', 'groupend'),
	'menu_financial_manage_begin'			=> array('真实项目管理', 'groupbegin'),
    'menu_financial_regular'          => array('录入定期项目', Url::to(['financial/regular-input'])),
	'menu_financial_current'			=> array('录入活期项目', Url::to(['financial/current-input'])),
	'menu_financial_list'			=> array('项目列表', Url::to(['financial/list'])),
	'menu_financial_manage_end'			=> array('真实项目管理', 'groupend'),    
    'menu_financial_count_begin'          => array('项目统计信息', 'groupbegin'),
    'menu_financial_regular_info'          => array('定期项目统计', Url::to(['financial/regular-info'])),
    'menu_financial_current_info'            => array('活期项目统计', Url::to(['financial/current-info'])),
    'menu_financial_count_list'           => array('总账明细表', Url::to(['financial/count-list'])),
    'menu_financial_count_end'            => array('项目统计信息', 'groupend'),
);

$menu['user'] = array(
	'menu_user_begin'			=> array('用户', 'groupbegin'),
	'menu_user_list'			=> array('用户基本信息', Url::to(['user/list'])),
	'menu_user_detail_list'		=> array('用户详细信息', Url::to(['user/detail-list'])),
	'menu_user_login_log'		=> array('登录日志', Url::to(['user/login-log'])),
	'menu_user_banks'			=> array('银行卡信息', Url::to(['user/banks'])),
    'menu_user_notice'			=> array('消息列表', Url::to(['user/notice'])),
	'menu_user_end'				=> array('用户', 'groupend'),
);

$menu['content'] = array(
	'menu_content_begin'			=> array('通用内容', 'groupbegin'),
	'menu_content_manager'		=> array('文章管理', Url::to(['article/list'])),
	'menu_contenttype_manager'	=> array('栏目管理', Url::to(['article-type/list'])),
	'menu_msg_moblie_push_manager' => array('消息推送', Url::to(['msg-push/add'])),
	'menu_activity_manager'		=> array('活动管理', Url::to(['activity/list'])),
	'menu_content_end'			=> array('通用内容', 'groupend'),
);

$menu['app_config'] = array(
	'menu_app_config_common_begin'	 => array('app管理', 'groupbegin'),
	'menu_app_config_common_manager' => array('app管理', Url::to(['app-conf-info/list'])),
	'menu_app_config_version_manager' => array('app版本管理', Url::to(['app-version-info/list'])),
	'menu_app_config_common_end'	 => array('app管理', 'groupend'),
	'menu_app_config_image_begin'	 => array('app图片配置', 'groupbegin'),
	'menu_app_config_image_manager'	 => array('app图片管理', Url::to(['app-image/list'])),
	'menu_app_config_image_type'	 => array('图片类型管理', Url::to(['app-image-type/list'])),
	'menu_app_config_image_end'	 	 => array('app图片配置', 'groupend'),
	'menu_app_config_interface_begin'	 => array('app接口配置', 'groupbegin'),
	'menu_app_config_interface_manager'	 => array('接口管理', Url::to(['app-interface-info/list'])),
	'menu_app_config_interface_end'	 	 => array('app接口配置', 'groupend'),
);

$menu['app_info'] = array(
	'menu_app_info_device_begin'		=> array('安装设备管理', 'groupbegin'),
	'menu_app_info_device_list'			=> array('安装设备列表', Url::to(['device/list'])),
	'menu_app_info_device_visit_list'	=> array('设备启动记录', Url::to(['device/visit-list'])),
	'menu_app_info_device_end'	 	  	=> array('安装设备管理', 'groupend'),
);

$menu['system'] = array(
	'menu_adminuser_begin'			=> array('系统管理员', 'groupbegin'),
	'menu_adminuser_list'			=> array('管理员管理', Url::to(['admin-user/list'])),
	'menu_adminuser_role_list'		=> array('角色管理', Url::to(['admin-user/role-list'])),
	'menu_adminuser_end'	 		=> array('系统管理员', 'groupend'),
	'menu_guest_suggest_begin'		=> array('客户反馈', 'groupbegin'),
	'menu_guest_suggest_list'		=> array('反馈列表', Url::to(['suggest/list'])),
	'menu_guest_suggest_end'	 	=> array('客户反馈', 'groupend'),
);