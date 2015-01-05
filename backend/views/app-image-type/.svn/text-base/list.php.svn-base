<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * @var backend\components\View $this
 */
$this->shownav('app_config', 'menu_app_config_banner_begin');
$this->showsubmenu('图片类型管理', array(
	array('列表', Url::toRoute('app-image-type/list'), 1),
	array('添加类型', Url::toRoute('app-image-type/add'), 0),
));

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
		<th>名称</th>
		<th>备注</th>
		<th>创建人</th>
		<th>创建时间</th>
		<th>操作</th>
	</tr>
	<?php foreach ($imageTypes as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo $value->name; ?></td>
		<td><?php echo $value->comment; ?></td>
		<td><?php echo $value->auditor; ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
		<td class="td23">
			<a href="<?php echo Url::to(['app-image-type/edit', 'id' => $value->id]); ?>">编辑</a>
			<a onclick="return confirmMsg('确定要删除吗？');" href="<?php echo Url::to(['app-image-type/delete', 'id' => $value->id]); ?>">删除</a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>