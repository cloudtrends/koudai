<?php
use backend\components\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(['id' => 'app-conf-info-form']); ?>
	<table style="width:500px" class="tb tb1">
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>App名称:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'app_name')->textInput(array("disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>英文名称:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'app_en_name')->textInput(array("disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>网站名称:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'website_name')->textInput(array("disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>网站url:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'website_url')->textInput(array("disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>备案号:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'beian')->textInput(array("disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>	
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>联系电话:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'tel')->textInput(array("disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>联系地址:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'address')->textInput(array("style"=>"width:300px;","disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>	
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>微信分享页面url:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'wx_share_url')->textInput(array("style"=>"width:300px;","disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="conf_td_label"><font color="red">*</font>微信分享内容:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'wx_share_content')->textarea(array("style"=>"width:300px;","disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr class="hover">
			<td class="conf_td_label">备注:</td>
			<td class="conf_td_input"><?php echo $form->field($model, 'comment')->textInput(array("style"=>"width:300px;","disabled"=>"true")); ?></td>
			<td class="vtop tips2"></td>
		</tr>
	</table>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
</script>