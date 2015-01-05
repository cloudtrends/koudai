<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use backend\models\AdminUser;

/**
 * @var backend\components\View $this
 */
$this->shownav('system', 'menu_adminuser_list');
$this->showsubmenu('管理员管理', array(
	array('列表', Url::toRoute('admin-user/list'), 1),
	array('添加管理员', Url::toRoute('admin-user/add'), 0),
));

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
		<th>用户名</th>
		<th>角色</th>
		<th>创建人</th>
		<th>创建时间</th>
		<th>操作</th>
	</tr>
	<?php foreach ($users as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo $value->username; ?></td>
		<td><?php echo $value->role; ?></td>
		<td><?php echo $value->created_user; ?></td>
		<td><?php echo date('Y-m-d', $value->created_at); ?></td>
		<td class="td24">
			<a href="<?php echo Url::to(['admin-user/change-pwd', 'id' => $value->id]); ?>">修改密码</a>
			<?php if ($value->username != AdminUser::SUPER_USERNAME): ?>
			<a href="<?php echo Url::to(['admin-user/edit', 'id' => $value->id]); ?>">编辑</a>
			<a onclick="return confirmMsg('确定要删除吗？');" href="<?php echo Url::to(['admin-user/delete', 'id' => $value->id]); ?>">删除</a>
			<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>