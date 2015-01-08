<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;
use common\models\Activity;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('content', 'menu_activity_manager');
$this->showsubmenu('活动管理', array(
    array('列表', Url::toRoute('activity/list'), 1),
    array('添加活动', Url::toRoute('activity/add'), 0),
));

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
        <th>标题</th>
		<th>摘要</th>
		<th>创建人</th>
		<th>创建时间</th>
		<th>修改时间</th>
		<th>状态</th>
		<th>操作</th>
	</tr>
	<?php foreach ($activitys as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
        <td><?php echo $value->title; ?><?php echo $value->thumbnail ? '<b>（附图）</b>' : ''; ?></td>
		<td><?php  echo $value->abstract ? mb_substr($value->abstract,0,9,'utf8').'...' : '无摘要'; ?></td>
		<td><?php echo $value->create_user; ?></td>
		<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
		<td><?php echo $value->updated_at ? date('Y-m-d H:i:s', $value->updated_at) : '未修改'; ?></td>
		<td><?php echo Activity::$status[$value->status]; ?></td>
		<td class="td23">
			<a href="<?php echo Url::to(['activity/edit', 'id' => $value->id]); ?>">编辑</a>
			<a onclick="return confirmMsg('确定要删除吗？');" href="<?php echo Url::to(['activity/delete', 'id' => $value->id]); ?>">删除</a>
		</td>
	</tr>
	<?php endforeach; ?>
</table>
    <?php if (empty($activitys)): ?>
        <div class="no-result">暂无记录</div>   
    <?php endif; ?>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>