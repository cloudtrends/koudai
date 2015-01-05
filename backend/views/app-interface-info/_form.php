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
			<td class="img_td_label"><font color="red">*</font>App版本号:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'app_version')->dropDownList(array("0.0.0"=>"请选择版本号", "1.1.1"=>"1.1", "1.1.2"=>"1.2", "1.1.3"=>"1.3")); ?></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>系统类型:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'os_type')->dropDownList(array(1=>"全平台", 2=>"ios", 3=>"android"), array("prompt"=>"请选择系统类型")); ?></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>接口名称:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'name')->textInput(); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>接口的key:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'key')->textInput(); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>method:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'method')->dropDownList(array(0=>"请选择请求方法", 1=>"GET", 2=>"POST", 3=>"REQUEST")); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>接口url:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'url')->textInput(array("style"=>"width:300px;")); ?></td>
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