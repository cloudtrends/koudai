<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * @var backend\components\View $this
 */
$this->shownav('app_config', 'menu_app_config_common_begin');
$this->showsubmenu('app管理', array(
	array('列表', Url::toRoute('app-conf-info/list'), 1),
	array('添加', Url::toRoute('app-conf-info/add'), 0),
));

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
		<th>名称</th>
		<th>英文名称</th>
<!-- 		<th>安卓最新版本号</th>
		<th>ios最新版本号</th> -->
		<th>配置信息版本号</th>
		<th>创建者</th>
		<th>创建时间</th>
		<th style="width:120px;">操作</th>
	</tr>
	<?php foreach ($confs as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo $value->app_name; ?></td>
		<td><?php echo $value->app_en_name; ?></td>
		<td><?php echo $value->conf_version; ?></td>
		<td><?php echo $value->auditor; ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
		<td class="td23">
			<a href="<?php echo Url::to(['app-conf-info/view', 'id' => $value->id]); ?>">详细信息</a>
			<a href="<?php echo Url::to(['app-conf-info/edit', 'id' => $value->id]); ?>">编辑</a>
			<a onclick="return confirmMsg('确定要删除吗？');" href="<?php echo Url::to(['app-conf-info/delete', 'id' => $value->id]); ?>">删除</a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>