<?php

use common\models\Project;

/**
 * @var backend\components\View $this
 */
$this->shownav('project', 'menu_project_list');
$this->showsubmenuanchors('查看项目', array(
	array('基本信息', 'baseinfo', 1),
	array('投资记录', 'invests', 0),
	array('审核记录', 'reviewlogs', 0),
));

?>

<div id="baseinfo">
	<?php echo $this->render('_baseinfo', ['model' => $model]); ?>
</div>

<div id="invests" style="display:none;">
	<?php echo $this->render('_invest-list', ['model' => $model]); ?>
</div>

<div id="reviewlogs" style="display:none;">
	<table class="tb tb2 fixpadding">
		<tr><th class="partition" colspan="15">审核记录</th></tr>
		<tr class="header">
			<th>ID</th>
			<th>操作人</th>
			<th>操作前状态</th>
			<th>操作后状态</th>
			<th>时间</th>
			<th>备注</th>
		</tr>
		<?php foreach ($model->reviewLogs as $value): ?>
		<tr class="hover">
			<td><?php echo $value->id; ?></td>
			<td><?php echo $value->username; ?></td>
			<td><?php echo Project::$status[$value->pre_status]; ?></td>
			<td><?php echo Project::$status[$value->cur_status]; ?></td>
			<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
			<td><?php echo $value->remark; ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php if (empty($model->reviewLogs)): ?>
	<div class="no-result">暂无记录</div>	
	<?php endif; ?>
</div>