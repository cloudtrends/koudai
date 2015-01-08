<?php

namespace common\helpers;

use Yii;

class MessageHelper
{
	public static function sendSMS($phone, $message, $smsServiceUse = 'smsService')
	{

		if ($smsServiceUse == 'smsService') {
			$msg = urlencode($message);
			$url = Yii::$app->params['smsService']['url'];
			$uid = Yii::$app->params['smsService']['uid'];
			$auth = md5(Yii::$app->params['smsService']['code'] . Yii::$app->params['smsService']['password']);
			$result = file_get_contents("{$url}?uid={$uid}&auth={$auth}&mobile={$phone}&msg={$msg}&expid=0&encode=utf-8");
			// 返回值要是0这种格式才成功，后面是短信id
			if ($result && strpos($result, ',') !== false) {
				list($resCode, $resMsg) = explode(",", $result);
				if ($resCode == '0') {
					return true;
				}
			}else{
				Yii::error("发送短信失败，result:{$result} mobile:{$phone} msg:{$msg}");
				return false;
			}
		}else{
			/**
			* 普通接口发短信
			* apikey 为云片分配的apikey
			* text 为短信内容
			* mobile 为接受短信的手机号
			*/
			$url1 = Yii::$app->params['smsService1']['url'];
			$apikey = Yii::$app->params['smsService1']['apikey'];
			$msg = urlencode('【口袋理财】'.$message);			
			$post_string="apikey=$apikey&text=$msg&mobile=$phone";
			$data = "";
			$info=parse_url($url1);
			$fp=fsockopen($info["host"],80,$errno,$errstr,30);
			if(!$fp){
				return $data;
			}
			$head="POST ".$info['path']." HTTP/1.0\r\n";
			$head.="Host: ".$info['host']."\r\n";
			$head.="Referer: http://".$info['host'].$info['path']."\r\n";
			$head.="Content-type: application/x-www-form-urlencoded\r\n";
			$head.="Content-Length: ".strlen(trim($post_string))."\r\n";
			$head.="\r\n";
			$head.=trim($post_string);
			$write=fputs($fp,$head);
			$header = "";
			while ($str = trim(fgets($fp,4096))) {
				$header.=$str;
			}
			while (!feof($fp)) {
				$data .= fgets($fp,4096);
			}
			// 返回值要是0这种格式才成功
			if ($data && strpos($data, ',') !== false) {
				list($resCode, $resMsg) = explode(",", $data);
				if (substr($resCode, -1) == '0') {
					return true;
				}
			}else{
				Yii::error("发送短信失败,".$data);
				return false;
			}
		}
	}

}