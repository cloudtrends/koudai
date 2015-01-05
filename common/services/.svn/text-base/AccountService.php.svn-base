<?php

namespace common\services;

use common\exceptions\InvestException;
use Yii;
use yii\base\Object;
use yii\base\UserException;
use common\models\User;
use common\models\UserWithdraw;
use common\models\UserAccount;
use common\models\NoticeSms;

/**
 * 用户资金模块service
 */
class AccountService extends Object
{
	/**
	 * 申请提现
	 * 成功返回时间，金额，手续费（手续费暂定免费）
	 */
	public function withdraw(User $user, $money)
	{
		$transaction = Yii::$app->db->beginTransaction();
		try {
			$withdraw = new UserWithdraw();
			$withdraw->user_id = $user->id;
			$withdraw->money = $money;
			$withdraw->status = UserWithdraw::UMP_PAYING;
			if ($withdraw->save()) {
				UserAccount::updateAccount($user->id, [
					['usable_money', '-', $money],
					['withdrawing_money', '+', $money],
				]);
				UserAccount::addLog($user->id, UserAccount::TRADE_TYPE_APPLY_WITHDRAW, $money);
			} else {
				throw new UserException('申请提现失败，请稍后再试');
			}
			
			$transaction->commit();
			return [
				'money' => $withdraw->money,
				'created_at' => $withdraw->created_at,
				'poundage' => 0,
				'message' => "您的一笔" . ($withdraw->money / 100) . "元提现申请已发出，我们将在T+1个工作日内完成打款（遇节假日顺延）请注意查收。",
			];
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
	
	/**
	 * 提现通过
	 */
	public function withdrawApprove($id,  $money, $phone_no, $review_username)
	{
		$transaction = Yii::$app->db->beginTransaction();
		try {
            //Yii::
            $payService = Yii::$container->get('payService');
            $ret = $payService->withdraw(
                $id,
                $money,
                $phone_no
            );
            if( $ret['code'] != 0 ){
                InvestException::throwCodeAndMsg($ret['code'], $ret['message']);
            }

			$withdraw = UserWithdraw::findOne($id);
// 			$withdraw->status = UserWithdraw::UMP_PAY_SUCCESS;
			$withdraw->review_username = $review_username;
			$withdraw->review_time = time();
			$withdraw->review_result = UserWithdraw::REVIEW_STATUS_APPROVE;
			$withdraw->save();
// 			if ($withdraw->save()) {
// 				UserAccount::updateAccount($withdraw->user_id, [
// 					['withdrawing_money', '-', $withdraw->money],
// 					['total_money', '-', $withdraw->money],
// 				]);
// 				UserAccount::addLog($withdraw->user_id, UserAccount::TRADE_TYPE_WITHDRAW, $withdraw->money);
// 			} else {
// 				throw new \Exception('提现记录修改失败');
// 			}
//             /** 记录NoticeSms 提现成功 JohnnyLin */
//             NoticeSms::instance()->init_sms_str($withdraw->user_id,NoticeSms::NOTICE_DRAWAL,array('money'=>$withdraw->money));

			$transaction->commit();
			return true;
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
	
	/**
	 * 提现成功后处理
	 */
	public function withdrawHandleSuccess($id)
	{
		$transaction = Yii::$app->db->beginTransaction();
		try {
			$withdraw = UserWithdraw::findOne($id);
			// 如果已经是提现成功则不重复处理
			if ($withdraw->status == UserWithdraw::UMP_PAY_SUCCESS) {
				$transaction->commit();
				return true;
			}
			$withdraw->status = UserWithdraw::UMP_PAY_SUCCESS;
			if ($withdraw->save()) {
				UserAccount::updateAccount($withdraw->user_id, [
					['withdrawing_money', '-', $withdraw->money],
					['total_money', '-', $withdraw->money],
				]);
				UserAccount::addLog($withdraw->user_id, UserAccount::TRADE_TYPE_WITHDRAW, $withdraw->money);
			} else {
				throw new \Exception('提现记录修改失败');
			}
            /** 记录NoticeSms 提现成功 JohnnyLin */
            NoticeSms::instance()->init_sms_str($withdraw->user_id,NoticeSms::NOTICE_DRAWAL,array('money'=>$withdraw->money));
            
			$transaction->commit();
			return true;
		} catch (\Exception $e) {
			$transaction->rollBack();
			return false;
		}
	}
	
	/**
	 * 提现驳回
	 */
	public function withdrawReject($id, $review_username)
	{
		$transaction = Yii::$app->db->beginTransaction();
		try {
			$withdraw = UserWithdraw::findOne($id);
			$withdraw->status = UserWithdraw::UMP_PAY_FAILED;
			$withdraw->review_username = $review_username;
			$withdraw->review_time = time();
			$withdraw->review_result = UserWithdraw::REVIEW_STATUS_REJECT;
			if ($withdraw->save()) {
				UserAccount::updateAccount($withdraw->user_id, [
					['withdrawing_money', '-', $withdraw->money],
					['usable_money', '+', $withdraw->money],
				]);
				UserAccount::addLog($withdraw->user_id, UserAccount::TRADE_TYPE_REJECT_WITHDRAW, $withdraw->money);
			} else {
				throw new \Exception('提现记录修改失败');
			}
			$transaction->commit();
			return true;
		} catch (\Exception $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
	
	/**
	 * 后台充值
	 */
	public function backendRecharge($user_id, $money, $remark)
	{
		UserAccount::updateAccount($user_id, [
			['usable_money', '+', $money],
			['total_money', '+', $money],
		]);
		UserAccount::addLog($user_id, UserAccount::TRADE_TYPE_BACKEND_RECHARGE, $money, $remark);
		return true;
	}
}