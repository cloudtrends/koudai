<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;

$DEVICE_INFO_OS_TYPE = array(1=>"全平台", 2=>"ios", 3=>"android");
$REQUEST_METHOD_TYPE = array(1=>"GET", 2=>"POST", 3=>"REQUEST");
/**
 * @var backend\components\View $this
 */
$this->shownav('app_config', 'menu_app_config_version_begin');
$this->showsubmenu('app版本管理', array(
	array('列表', Url::toRoute('app-version-info/list'), 1),
	array('添加', Url::toRoute('app-version-info/add'), 0),
));

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th style="width:30px;">ID</th>
		<th>app名称</th>
		<th>系统类型</th>
		<th>版本号</th>
		<th>下载地址</th>
		<th>更新记录</th>
		<th>备注</th>
		<th>创建者</th>
		<th>创建时间</th>
		<th>操作</th>
	</tr>
	<?php foreach ($versions as $value): ?>
	<tr class="hover">
		<td class="td25" style="width:30px;"><?php echo $value->id; ?></td>
		<td><?php echo $apps[$value->app_id]; ?></td>
		<td><?php echo $DEVICE_INFO_OS_TYPE[$value->os_type]; ?></td>
		<td><?php echo $value->version; ?></td>
		<td><?php echo $value->url; ?></td>
		<td><?php echo $value->update_record; ?></td>
		<td><?php echo $value->comment; ?></td>
		<td><?php echo $value->auditor; ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
		<td class="td23">
			<a href="<?php echo Url::to(['app-version-info/edit', 'id' => $value->id]); ?>">编辑</a>
			<a onclick="return confirmMsg('确定要删除吗？');" href="<?php echo Url::to(['app-version-info/delete', 'id' => $value->id]); ?>">删除</a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>