<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;

/**
 * @var backend\components\View $this
 */
$this->shownav('content', 'menu_contenttype_manager');
$this->showsubmenu('栏目管理', array(
	array('列表', Url::toRoute('article-type/list'), 1),
	array('添加栏目', Url::toRoute('article-type/add'), 0),
));

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
		<th>标识</th>
		<th>名称</th>
		<th>是否内置</th>
		<th>创建人</th>
		<th>创建时间</th>
		<th>操作</th>
	</tr>
	<?php foreach ($articleTypes as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo $value->name; ?></td>
		<td><?php echo $value->title; ?></td>
		<td><?php echo $value->is_builtin ? '是' : '否'; ?></td>
		<td><?php echo $value->create_user ? $value->create_user : '-'; ?></td>
		<td><?php echo $value->created_at ? date('Y-m-d H:i:s', $value->created_at) : '-'; ?></td>
		<td class="td23">
			<?php if (!$value->is_builtin): ?>
			<a href="<?php echo Url::to(['article-type/edit', 'id' => $value->id]); ?>">编辑</a>
			<a onclick="return confirmMsg('删除栏目则该栏目对应的文章将归为默认类型\n\n确定要删除吗？');" href="<?php echo Url::to(['article-type/delete', 'id' => $value->id]); ?>">删除</a>
			<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>