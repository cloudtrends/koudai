<?php
use backend\components\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(['id' => 'article-type-form']); ?>
	<table class="tb tb2">
		<tr><td class="td27" colspan="2">名称:</td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'title')->textInput(); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr><td class="td27" colspan="2">标识:</td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'name')->textInput(); ?></td>
			<td class="vtop tips2">唯一标识，只能是数字或字母</td>
		</tr>
		<tr>
			<td colspan="15">
				<input type="submit" value="提交" name="submit_btn" class="btn">
			</td>
		</tr>
	</table>
<?php ActiveForm::end(); ?>