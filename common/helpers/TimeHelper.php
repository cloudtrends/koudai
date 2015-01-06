<?php
/**
 * Created by PhpStorm.
 * User: haoyu
 * Date: 2014/11/8
 * Time: 18:25
 */

namespace common\helpers;


class TimeHelper
{
    const DAY = 86400;

    // DateA 是否比 DateB 大于 30天
    public static function getLimitDay(){
        return 5 * self::DAY;
    }
    public static function isLT30Days($timeA, $timeB)
    {
        $timeA = self::zeroClockTimeOfDay($timeA);
        $timeB = self::zeroClockTimeOfDay($timeB);
        if ( $timeA - $timeB >= self::getLimitDay())
        {
            return true;
        }
        return false;
    }

    // 转换成当天0点的Linux时间戳
    public static function zeroClockTimeOfDay($time)
    {
        // 如果是linux时间戳
        if (is_numeric($time))
        {
            $date = date("Y-m-d",$time);
            return strtotime($date);
        }
        // 如果是日期函数
        else if (is_string($time)){
            $time = strtotime($time);
            return self::zeroClockTimeOfDay($time);
        }
        return false;
    }

    // 转换成当天24点的Linux时间戳
    public static function twentyFourTimeOfDay($time)
    {
        // 如果是linux时间戳
        if (is_numeric($time))
        {
            $date = date("Y-m-d",$time + self::DAY);
            return strtotime($date);
        }
        // 如果是日期函数
        else if (is_string($time)){
            $time = strtotime($time);
            return self::twentyFourTimeOfDay($time);
        }
        return false;
    }

    /*
    public static function Now($delay = ""){
        return strtotime($delay, time());
    }
    */

    public static function Now(){
        return time();
    }

    public static function Today(){
        return date("Y-m-d",self::Now());
    }

    // 获得时间的前一天
    public static function Yesterday($time = '')
    {
        if(empty($time))
        {
            $time = strtotime(self::Today());
        }
        else
        {
            if(is_string($time))
            {
                $time = strtotime($time);
            }
        }
        return date("Y-m-d", strtotime("-1 day", $time));
    }

    // 获得时间的后一天
    public static function Tomorrow($time = '')
    {
        if(empty($time))
        {
            $time = strtotime(self::Today());
        }
        else
        {
            if(is_string($time))
            {
                $time = strtotime($time);
            }
        }
        return date("Y-m-d", strtotime("+1 day", $time));
    }

    // 获得两个日期相差的天数
    public static function DiffDays($dateA, $dateB){
        return intval( ( strtotime($dateA) - strtotime($dateB) ) / self::DAY );
    }
}