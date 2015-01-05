<?php
use backend\components\widgets\ActiveForm;
?>

<?php $this->showtips('技巧提示', ['对于管理员或角色的变更，一般需要对应的管理员重新登录才生效！']); ?>

<?php $form = ActiveForm::begin(['id' => 'admin-form']); ?>
	<table class="tb tb2">
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'username'); ?></td></tr>
		<tr class="noborder">
			<?php if ($this->context->action->id == 'add'): ?>
			<td class="vtop rowform"><?php echo $form->field($model, 'username')->textInput(['autocomplete' => 'off']); ?></td>
			<td class="vtop tips2">只能是字母、数字或下划线，不能重复，添加后不能修改</td>
			<?php else: ?>
			<td colspan="2"><?php echo $model->username; ?></td>
			<?php endif; ?>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'role'); ?></td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'role')->dropDownList($roles, ['prompt' => '选择角色']); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<?php if ($this->context->action->id == 'add'): ?>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'password'); ?></td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><input type="password" autocomplete="off" name="AdminUser[password]" class="txt" id="adminuser-password"></td>
			<td class="vtop tips2">密码为6-16位字符或数字</td>
		</tr>
		<?php endif; ?>
		<tr>
			<td colspan="15">
				<input type="submit" value="提交" name="submit_btn" class="btn">
			</td>
		</tr>
	</table>
<?php ActiveForm::end(); ?>