<?php
use backend\components\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(['id' => 'app-image-type-form']); ?>
	<table style="width:400px" class="tb tb1">
		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>类型名称:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'name')->textInput(); ?></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label">备注:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'comment')->textInput(); ?></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr>
			<td></td>
			<td style="text-align:center;">
				<input type="submit" value="提交" name="submit_btn" class="btn">
			</td>
		</tr>
	</table>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
</script>