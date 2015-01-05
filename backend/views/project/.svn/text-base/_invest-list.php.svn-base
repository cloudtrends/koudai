<?php
use common\models\ProjectInvest;
?>
<table class="tb tb2 fixpadding">
	<tr><th class="partition" colspan="15">投资记录</th></tr>
	<tr class="header">
		<th>ID</th>
		<th>投资用户</th>
		<th>金额</th>
		<th>时间</th>
		<th class="td24">状态</th>
	</tr>
	<?php foreach ($model->invests as $value): ?>
	<tr class="hover">
		<td><?php echo $value->id; ?></td>
		<td><?php echo $value->username; ?></td>
		<td><?php echo sprintf('%.2f', $value->invest_money / 100); ?> 元</td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
		<td><?php echo ProjectInvest::$status[$value->status]; ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php if (empty($model->invests)): ?>
<div class="no-result">暂无记录</div>	
<?php endif; ?>