<?php

use yii\widgets\LinkPager;
use common\models\KdbInvest;

/**
 * @var backend\components\View $this
 */
$this->shownav('project', 'menu_koudaibao_invests');
$this->showsubmenu('投资记录');

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
		<th>用户名</th>
		<th>投资金额</th>
		<th>来源</th>
		<th>时间</th>
		<th>状态</th>
		<th class="td23">操作</th>
	</tr>
	<?php foreach ($invests as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo $value->username; ?></td>
		<td><?php echo sprintf('%.2f', $value->invest_money / 100); ?></td>
		<td><?php echo $value->source; ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
		<td><?php echo KdbInvest::$status[$value->status]; ?></td>
		<td></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php if (empty($invests)): ?>
<div class="no-result">暂无记录</div>
<?php endif; ?>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>