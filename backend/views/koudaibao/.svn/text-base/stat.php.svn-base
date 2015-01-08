<?php

use yii\widgets\LinkPager;

/**
 * @var backend\components\View $this
 */
$this->shownav('project', 'menu_koudaibao_stat');
$this->showsubmenu('资金统计');

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>统计周期</th>
		<th>剩余可投金额</th>
		<th>历史投资总额</th>
		<th>历史投资人次</th>
		<th>历史盈利总额</th>
		<th>周期内投资总额</th>
		<th>周期内投资人次</th>
		<th>周期内转出总额</th>
	</tr>
	<?php foreach ($accounts as $value): ?>
	<tr class="hover">
		<td><?php echo date('Y-m-d H:i:s', $value->created_at) . ' ~ ' . ($value->end_at ? date('Y-m-d H:i:s', $value->end_at) : '现在'); ?></td>
		<td><?php echo sprintf('%.2f', $value->cur_money / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->history_money / 100); ?></td>
		<td><?php echo $value->history_invest_times; ?></td>
		<td><?php echo sprintf('%.2f', $value->history_profits_money / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->today_money / 100); ?></td>
		<td><?php echo $value->today_invest_times; ?></td>
		<td><?php echo sprintf('%.2f', $value->today_rollout_money / 100); ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php if (empty($accounts)): ?>
<div class="no-result">暂无记录</div>
<?php endif; ?>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>