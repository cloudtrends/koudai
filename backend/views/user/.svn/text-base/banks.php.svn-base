<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use common\models\UserBankCard;
use yii\helpers\Html;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('user', 'menu_user_banks');
$this->showsubmenu('银行卡信息');

?>
<?php $form = ActiveForm::begin(['id' => 'searchform','method'=>'get', 'options' => ['style' => 'margin-bottom:5px;']]); ?>
            用户ID：<input type="text" value="<?php echo Yii::$app->getRequest()->get('user_id', ''); ?>" name="user_id" class="txt" style="width:60px;">&nbsp;
	绑卡手机号关键词：<input type="text" value="<?php echo Yii::$app->getRequest()->get('bind_phone', ''); ?>" name="bind_phone" class="txt" style="width:120px;">&nbsp;
	<input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>用户ID</th>
		<th>绑卡手机号</th>
		<th>银行标识</th>
		<th>银行名称</th>
		<th>卡号</th>
		<th>支付平台</th>
		<th>状态</th>
		<th>创建时间</th>
		<th>更新时间</th>
		<th>操作</th>
	</tr>
	<?php foreach ($banks as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->user_id; ?></td>
		<td><?php echo $value->bind_phone; ?></td>
		<td><?php echo $value->bank_id; ?></td>
		<td><?php echo $value->bank_name; ?></td>
		<td><?php echo $value->card_no; ?></td>
		<td><?php echo $value->getPlatformLabel(); ?></td>
		<td><?php echo UserBankCard::$status_desc[$value->status]; ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->updated_at); ?></td>
		<td class="td23">
			<?php if ($value->status): ?>
			<a onclick="return confirmMsg('确定要解绑吗？');" href="<?php echo Url::to(['user/un-bind-card', 'user_id' => $value->user_id]); ?>">解绑</a>
			<?php else: ?>
			-
			<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>