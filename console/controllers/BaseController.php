<?php

namespace console\controllers;

abstract class BaseController extends \yii\console\Controller
{
	public function beforeAction($action)
	{
		if (parent::beforeAction($action)) {
			$this->message('begin action', true);
			return true;
		} else {
			return false;
		}
	}
	
	public function afterAction($action, $result)
	{
		$result = parent::afterAction($action, $result);
		$this->message('end action', true);
		return $result;
	}
	
	/**
	 * 输出错误信息到控制台，并记录log
	 * @param string $message
	 * @param bool $log 是否记录日志，默认是
	 */
	public function error($message, $log = true)
	{
		echo "error: {$message}\n";
		if ($log) {
			\Yii::error($message, $this->className());
		}
	}
	
	/**
	 * 输出信息到控制台，并记录log
	 * @param string $message
	 * @param bool $log 是否记录日志，默认否
	 */
	public function message($message, $log = false)
	{
		echo "info: {$message}\n";
		if ($log) {
			\Yii::info($message, $this->className());
		}
	}
}