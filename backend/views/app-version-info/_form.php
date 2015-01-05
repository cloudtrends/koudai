<?php
use backend\components\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(['id' => 'app-conf-info-form']); ?>
	<table style="width:500px" class="tb tb1">
		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>App名称:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'app_id')->dropDownList($apps, array("prompt"=>"请选择app")); ?></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>系统类型:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'os_type')->dropDownList(array(1=>"全平台", 2=>"ios", 3=>"android"), array("prompt"=>"请选择系统类型")); ?></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>版本号:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'version')->textInput(); ?></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>更新内容:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'update_record')->textarea(array("style"=>"width:300px;")); ?></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>上传安装包:</td>
			<td class="img_td_input">
				<?php echo $form->field($model, 'url')->fileInput(); ?>
			</td>
			<td class="img_op"><a onclick="uploadImg()" href="javascript:;">上传</a></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="conf_td_label">备注:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'comment')->textInput(array("style"=>"width:300px;")); ?></td>
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