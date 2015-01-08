<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('content', 'menu_content_manager');
$this->showsubmenu('文章管理', array(
	array('列表', Url::toRoute('article/list'), 1),
	array('添加文章', Url::toRoute('article/add'), 0),
));

?>

<?php $form = ActiveForm::begin(['id' => 'searchform', 'method' => 'get', 'options' => ['style' => 'margin-bottom:5px;']]); ?>
	标题关键词：<input type="text" value="<?php echo isset($search['keyword']) ? trim($search['keyword']) : ''; ?>" name="keyword" class="txt">&nbsp;
	栏目类型：<?php echo Html::listBox('type', isset($search['type']) ? trim($search['type']) : '', $articleTypes, ['size' => 0, 'prompt' => '不限类型']); ?>&nbsp;
	<input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>

<?php ActiveForm::begin(['id' => 'listform']); ?>
	<table class="tb tb2 fixpadding">
		<tr class="header">
			<th>ID</th>
			<th>标题</th>
			<th>排序</th>
			<th>栏目</th>
			<th>创建人</th>
			<th>创建时间</th>
			<th>操作</th>
		</tr>
		<?php foreach ($articles as $value): ?>
		<tr class="hover">
			<td class="td25"><?php echo $value->id; ?></td>
			<td><?php echo $value->title; ?></td>
			<td><input class="td25" type="text" value="<?php echo $value->order; ?>" name="orders[<?php echo $value->id; ?>]" title="越大越靠前"></td>
			<td><?php echo $value->articleType->title; ?></td>
			<td><?php echo $value->create_user; ?></td>
			<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
			<td class="td23">
				<a href="<?php echo Url::to(['article/edit', 'id' => $value->id]); ?>">编辑</a>
				<a onclick="return confirmMsg('确定要删除吗？');" href="<?php echo Url::to(['article/delete', 'id' => $value->id]); ?>">删除</a>
			</td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="15"><input type="submit" name="list_submit" value="提交" class="btn"></td>
		</tr>
	</table>
<?php ActiveForm::end(); ?>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>