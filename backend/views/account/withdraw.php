<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use backend\components\widgets\ActiveForm;
use yii\helpers\Html;
use common\models\UserWithdraw;

/**
 *
* <!---
*<?php if ($value->status == UserWithdraw::STATUS_PENDING): ?>
* <a onclick="return confirmMsg('确定要通过吗？');" href="<?php echo Url::to(['account/withdraw-approve', 'id' => $value->id]); ?>">通过</a>
* <a onclick="return confirmMsg('确定要驳回吗？');" href="<?php echo Url::to(['account/withdraw-reject', 'id' => $value->id]); ?>">驳回</a>
* <?php endif; ?>
* --->
 * @var backend\components\View $this
 */
$this->shownav('financial', 'menu_account_withdraw');
$this->showsubmenu('提现列表');

?>

<table class="tb tb2 ">
	<tr><td class="tipsblock"><ul><li>提现审核通过，只是向第三方支付平台发起提现申请（用户端仍然显示为提现中），提现结果第三方支付平台会异步通知或发起主动查询</li></ul></td></tr>
</table>

<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>
<?php $form = ActiveForm::begin(['id' => 'searchform', 'method' => "get", 'options' => ['style' => 'margin-bottom:5px;']]); ?>
	
	用户ID：<input type="text" value="<?php echo Yii::$app->getRequest()->get('user_id', ''); ?>" name="user_id" class="txt" style="width:120px;">&nbsp;       
	用户名：<input type="text" value="<?php echo Yii::$app->getRequest()->get('username', ''); ?>" name="username" class="txt" style="width:120px;">&nbsp;
	状态：<?php echo Html::dropDownList('status', Yii::$app->getRequest()->get('status', ''), UserWithdraw::$ump_pay_status, ['prompt' => '所有状态']); ?>&nbsp;
        提现时间：<input type="text" value="<?php echo Yii::$app->getRequest()->get('begintime', ''); ?>" name="begintime" onfocus="WdatePicker({startDate:'%y-%M-%d %H:%m:00',dateFmt:'yyyy-MM-dd HH:mm:00',alwaysUseStartDate:true,readOnly:true})"> 
        至<input type="text" value="<?php echo Yii::$app->getRequest()->get('endtime', ''); ?>"  name="endtime" onfocus="WdatePicker({startDcreated_atate:'%y-%M-%d %H:%m:00',dateFmt:'yyyy-MM-dd HH:mm:00',alwaysUseStartDate:true,readOnly:true})"> 
        <input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
		<th>用户ID</th>
		<th>用户名</th>
		<th>姓名</th>
		<th>提现金额</th>
		<th>提现时间</th>
		<th>状态</th>
		<th>审核状态</th>
		<th>审核人</th>
		<th>审核时间</th>
		<th width="80">操作</th>
	</tr>
	<?php foreach ($withdraws as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo $value->user_id; ?></td>
		<td><?php echo $value->user->username; ?></td>
		<td><?php echo $value->user->realname; ?></td>
		<td><?php echo sprintf('%.2f', $value->money / 100); ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
		<td><?php echo UserWithdraw::$ump_pay_status[$value->status]; ?></td>
		<td><?php echo UserWithdraw::$review_status[$value->review_result]; ?></td>
		<td><?php echo $value->review_username ? $value->review_username : '-'; ?></td>
		<td><?php echo $value->review_time ? date('Y-m-d H:i:s', $value->review_time) : '-'; ?></td>
		<td>
            <a href="<?php echo Url::to(['account/withdraw-detail', 'id' => $value->id, 'user_id' => $value->user_id]); ?>">详情</a>
            <?php if ($value->review_result == UserWithdraw::REVIEW_STATUS_APPROVE): ?>
            <a href="<?php echo Url::to(['account/withdraw-result', 'id' => $value->id]); ?>">付款查询</a>
            <?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>