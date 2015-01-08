<?php
/**
 * Created by PhpStorm.
 * User: haoyu
 * Date: 14-12-30
 * Time: 上午10:25
 */

namespace common\models;


class UserCharge
{
    const STATUS_CHARGE_INIT = 0;
    const STATUS_CHARGE_SUCCESS = 1;
    const STATUS_CHARGE_FAILED = 2;
    const STATUS_CHARGE_EXPIRED = 3;

    public static $status = [
    	self::STATUS_CHARGE_INIT => '未完成',
    	self::STATUS_CHARGE_SUCCESS => '成功',
    	self::STATUS_CHARGE_FAILED => '失败',
    	self::STATUS_CHARGE_EXPIRED => '过期',
    ];

    public static function tableName(){
        return '{{%user_charge}}';
    }
}