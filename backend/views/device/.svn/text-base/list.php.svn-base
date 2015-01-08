<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * @var backend\components\View $this
 */
$this->shownav('app_info', 'menu_app_info_device_list');
$this->showsubmenu('安装设备列表', array(
	array('列表', Url::toRoute('device/list'), 1),
));

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
		<th>设备标识</th>
		<th>设备名称</th>
		<th>设备类型</th>
		<th>系统版本号</th>
		<th>app版本号</th>
		<th>最后登录用户</th>
		<th>最后登录时间</th>
		<th>安装时间</th>
		<th width="160">创建时间</th>
	</tr>
	<?php foreach ($devices as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo $value->device_id; ?></td>
		<td><?php echo $value->device_info; ?></td>
		<td><?php echo $value->os_type; ?></td>
		<td><?php echo $value->os_version; ?></td>
		<td><?php echo $value->app_version; ?></td>
		<td><?php echo $value->last_login_user ? $value->last_login_user : '-'; ?></td>
		<td><?php echo $value->last_login_time ? date('Y-m-d H:i:s', $value->last_login_time) : '-'; ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->installed_time); ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>