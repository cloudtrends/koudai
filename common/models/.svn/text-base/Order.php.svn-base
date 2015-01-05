<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use common\helpers\StringHelper;

/**
 * This is the model class for table "{{%order}}".
 */
class Order extends \yii\db\ActiveRecord
{
	const TYPE_INVEST_KDB = 1;
	const TYPE_INVEST_PROJ = 2;
	const TYPE_WITHDRAW = 3;
	
	const STATUS_NEW = 0;		// 初始创建
	const STATUS_HANDING = 1;	// 处理中
	const STATUS_SUCCESS = 2;	// 处理成功
	const STATUS_FAILED = 3;	// 处理失败
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
    	return [
    		TimestampBehavior::className(),
    	];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
    	return [
    		['order_id', 'unique', 'message' => '订单号不能重复'],
    	];
    }
    
    /**
     * 判断是否可以提交处理，避免重复提交
     */
    public function getCanCommit()
    {
    	if ($this->status == self::STATUS_HANDING || $this->status == self::STATUS_SUCCESS) {
    		return false;
    	} else {
    		return true;
    	}
    }
    
    /**
     * 生成订单号
     */
    public static function generateOrderId()
    {
    	$uniqid = StringHelper::generateUniqid();
    	if (!\Yii::$app->user->getIsGuest()) {
    		$order_id = date('Ymd') . \Yii::$app->user->identity->id . "_{$uniqid}";
    	} else {
    		$order_id = date('Ymd') . "_{$uniqid}";
    	}
    	return $order_id;
    }
    
    /**
     * 验证签名
     * @param array $params
     * @param string $sign
     * @return boolean
     */
    public static function validateSign($params, $sign)
    {
        /*
    	$key = '**kdlc**';
    	unset($params['sign']);
    	$signStr = http_build_query($params) . $key;
    	return base64_encode($signStr) == $sign;
        */
        return self::getSign($params) == $sign;
    }

    /**
     * 获得签名
     * @param array $params
     * @param string $sign
     * @return boolean
     */
    public static function getSign($params)
    {
        $key = '**kdlc**';
        unset($params['sign']);
        ksort($params);
        $signStr = http_build_query($params) . $key;
        return base64_encode($signStr);
    }
}