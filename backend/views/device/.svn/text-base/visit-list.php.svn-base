<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * @var backend\components\View $this
 */
$this->shownav('app_info', 'menu_app_info_device_visit_list');
$this->showsubmenu('设备启动记录', array(
	array('列表', Url::toRoute('device/visit-list'), 1),
));

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
		<th>设备标识</th>
		<th>用户信息</th>
		<th>网络类型</th>
		<th width="190">启动时间</th>
		<th width="190">创建时间</th>
	</tr>
	<?php foreach ($visits as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo $value->device_id; ?></td>
		<td><?php echo $value->username; ?></td>
		<td><?php echo $value->net_type;  ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->visit_time); ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>