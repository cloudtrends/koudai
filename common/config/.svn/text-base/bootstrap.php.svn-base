<?php
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('console', dirname(dirname(__DIR__)) . '/console');

/**
 * 设置别名，即可以通过Yii::$container->get('userService')的方式获得对应的service对象
 * 当然也可以通过构造函数注入到成员变量中
 */
Yii::$container->set('userService', 'common\services\UserService');
Yii::$container->set('accountService', 'common\services\AccountService');
Yii::$container->set('projectService', 'common\services\ProjectService');
Yii::$container->set('creditService', 'common\services\CreditService');
Yii::$container->set('msgPushService', 'common\services\MsgPushService');
Yii::$container->set('payService', 'common\services\PayService');
Yii::$container->set('llPayService', 'common\services\LLPayService');
Yii::$container->set('weixinService', 'common\services\WeixinService');
