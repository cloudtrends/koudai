<?php

use yii\widgets\LinkPager;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('user', 'menu_user_detail_list');
$this->showsubmenu('用户详细信息');

?>

<style type="text/css">
th {border-right: 1px dotted #deeffb;}
</style>

<?php $form = ActiveForm::begin(['id' => 'searchform','method'=>'get', 'options' => ['style' => 'margin-bottom:5px;']]); ?>
            ID：<input type="text" value="<?php echo Yii::$app->getRequest()->get('id', ''); ?>" name="id" class="txt" style="width:60px;">&nbsp;
	<input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th rowspan="2">用户ID</th>
		<th rowspan="2">用户名</th>
		<th colspan="5" style="text-align:center;border-right:none;">注册来源</th>
	</tr>
	<tr class="header">
		<th>注册终端类型</th>
		<th>注册app版本</th>
		<th>注册设备名称</th>
		<th>注册设备os版本</th>
		<th style="border-right:none;">注册app来源市场</th>
	</tr>
	<?php foreach ($users as $value): ?>
	<tr class="hover">
		<td class="td23"><?php echo $value->user_id; ?></td>
		<td><?php echo $value->user->username; ?></td>
		<td><?php echo $value->reg_client_type ? $value->reg_client_type : '-'; ?></td>
		<td><?php echo $value->reg_app_version ? $value->reg_app_version : '-'; ?></td>
		<td><?php echo $value->reg_device_name ? $value->reg_device_name : '-'; ?></td>
		<td><?php echo $value->reg_os_version ? $value->reg_os_version : '-'; ?></td>
		<td><?php echo $value->reg_app_market ? $value->reg_app_market : '-'; ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php if (empty($users)): ?>
	<div class="no-result">暂无记录</div>   
<?php endif; ?>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>