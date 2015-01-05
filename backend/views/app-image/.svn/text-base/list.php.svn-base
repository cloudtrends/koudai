<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * @var backend\components\View $this
 */
$this->shownav('app_config', 'menu_app_config_banner_begin');
$this->showsubmenu('app图片管理', array(
	array('列表', Url::toRoute('app-image/list'), 1),
	array('添加图片', Url::toRoute('app-image/add'), 0),
));

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
		<th>标题</th>
		<th>栏目</th>
		<th>创建人</th>
		<th>创建时间</th>
		<th>操作</th>
	</tr>
	<?php foreach ($images as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo $value->title; ?></td>
		<td><?php echo $value->type_id; ?></td>
		<td><?php echo $value->create_user; ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
		<td class="td23">
			<a href="<?php echo Url::to(['article/edit', 'id' => $value->id]); ?>">编辑</a>
			<a onclick="return confirmMsg('确定要删除吗？');" href="<?php echo Url::to(['article/delete', 'id' => $value->id]); ?>">删除</a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>