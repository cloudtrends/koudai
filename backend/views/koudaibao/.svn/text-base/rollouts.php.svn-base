<?php

use yii\widgets\LinkPager;

/**
 * @var backend\components\View $this
 */
$this->shownav('project', 'menu_koudaibao_rollouts');
$this->showsubmenu('转出记录');

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
		<th>用户名</th>
		<th>转出金额</th>
		<th>时间</th>
	</tr>
	<?php foreach ($rollouts as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo $value->username; ?></td>
		<td><?php echo sprintf('%.2f', $value->money / 100); ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php if (empty($rollouts)): ?>
<div class="no-result">暂无记录</div>
<?php endif; ?>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>