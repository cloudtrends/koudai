<?php

namespace common\helpers;

class StringHelper extends \yii\helpers\StringHelper
{
	/**
	 * 模糊手机号
	 * 比如：13917883434 变成 139****3434
	 */
	public static function blurPhone($phone)
	{
		return substr($phone, 0, 3) . '****' . substr($phone, 7);
	}

    /**
     * 模糊银行卡
     * 比如：6224 8851 1234 4568 变成 6224 **** 4568
     */
    public static function blurCardNo($card_no)
    {
        $start_pos = strlen($card_no) - 4;
        return substr($card_no, 0, 4) . ' **** ' . substr($card_no, $start_pos);
    }

    /**
     * 安全的将“元”转化成“分”
     * 比如：10.01 变成 1001
     */
    public static function safeConvertCentToInt($num)
    {
        return intval(bcmul(floatval($num) , 100));
    }

    const ONE_MONTH = 30;
    const YEAR_DAYS = 365;
    /**
     * 输入的月份数变成天数
     * @param $numberM 月份数
     */
    public static function monthToDays($numberM){
        if ($numberM < 12 ){
            return intval($numberM * self::ONE_MONTH);
        }
        $years = intval($numberM / 12 ) ;
        $months = intval($numberM % 12) ;
        return intval(self::YEAR_DAYS * $years + self::ONE_MONTH * $months);
    }

    /**
     * 生成唯一ID
     * @return string
     */
    public static function generateUniqid()
    {
    	$prefix = rand(10000, 99999);
    	return uniqid($prefix);
    }

    /*********************************************************************
    函数名称:encrypt
    函数作用:加密解密字符串
    使用方法:
    加密     :encrypt('str','E','nowamagic');
    解密     :encrypt('被加密过的字符串','D','nowamagic');
    参数说明:
    $string   :需要加密解密的字符串
    $operation:判断是加密还是解密:E:加密   D:解密
    $key      :加密的钥匙(密匙);
     *********************************************************************/
    static public function encrypt($string,$operation,$key='nowamagic')
    {
        $key=md5($key);
        $key_length=strlen($key);
        $string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
        $string_length=strlen($string);
        $rndkey=$box=array();
        $result='';
        for($i=0;$i<=255;$i++)
        {
            $rndkey[$i]=ord($key[$i%$key_length]);
            $box[$i]=$i;
        }
        for($j=$i=0;$i<256;$i++)
        {
            $j=($j+$box[$i]+$rndkey[$i])%256;
            $tmp=$box[$i];
            $box[$i]=$box[$j];
            $box[$j]=$tmp;
        }
        for($a=$j=$i=0;$i<$string_length;$i++)
        {
            $a=($a+1)%256;
            $j=($j+$box[$a])%256;
            $tmp=$box[$a];
            $box[$a]=$box[$j];
            $box[$j]=$tmp;
            $result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
        }
        if($operation=='D')
        {
            if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
            {
                return substr($result,8);
            }
            else
            {
                return'';
            }
        }
        else
        {
            return str_replace('=','',base64_encode($result));
        }
    }
}