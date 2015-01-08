<?php
namespace backend\models;

use yii\base\Model;
use common\models\User;

/**
 * Recharge form
 */
class RechargeForm extends Model
{
	public $username;
	public $money;
	public $remark;
	
	private $_user;
	
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['username', 'money', 'remark'], 'required'],
			['money', 'number', 'min' => 0.01, 'max' => 1000, 'message' => '只能1000以内的金额', 'tooSmall' => '只能1000以内的金额', 'tooBig' => '只能1000以内的金额'],
			['username', function ($attribute, $params) {
				$this->_user = User::findByUsername($this->$attribute);
    			if (!$this->_user) {
    				$this->addError($attribute, '该手机号用户不存在');
    			}
    		}],
		];
	}
	
	public function attributeLabels()
	{
		return [
			'username' => '手机号',
			'money' => '金额',
			'remark' => '备注',
		];
	}
	
	public function getUser()
	{
		return $this->_user;
	}
}