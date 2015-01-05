<?php

use yii\widgets\ActiveForm;

$this->shownav('project', 'menu_index_setting');
$this->showsubmenu('首页项目配置');

?>

<?php $this->showtips('技巧提示', [
	'项目ID为0表示口袋宝',
	'普通项目设置不能设置待审核、作废或者已还款项目'
]); ?>

<?php ActiveForm::begin(['id' => 'listform']); ?>
	<table class="tb tb2 fixpadding">
		<tr class="header">
			<th></th>
			<th>项目ID</th>
			<th>标题</th>
			<th width="50%"></th>
		</tr>
		<?php foreach ($data as $key => $value): ?>
		<tr class="hover">
			<td><?php echo "第" . ($key+1) . "页"; ?></td>
			<td><input type="text" name="setting[<?php echo $key; ?>][id]" value="<?php echo $value['id']; ?>" style="width:80px;" /></td>
			<td><input type="text" name="setting[<?php echo $key; ?>][title]" value="<?php echo $value['title']; ?>" /></td>
			<td></td>
		</tr>
		<?php endforeach; ?>
		<tr>
			<td colspan="15"><input type="submit" name="list_submit" value="提交" class="btn"></td>
		</tr>
	</table>
<?php ActiveForm::end(); ?>