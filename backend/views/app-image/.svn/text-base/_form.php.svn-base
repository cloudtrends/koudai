<?php
use backend\components\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(['id' => 'app-image-form']); ?>
	<table style="width:450px" class="tb tb1">
		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>App名称:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'app_id')->dropDownList(array(0=>"请选择app", 1=>"口袋理财", 2=>"口袋赚钱")); ?></td>
			<td class="img_op"></td>
			<td class="vtop tips2">sadasd</td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>App版本号:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'app_version')->dropDownList(array(0=>"请选择版本号", 1=>"1.1", 2=>"1.2", 3=>"1.3")); ?></td>
			<td class="img_op"></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>系统类型:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'os_type')->dropDownList(array(0=>"请选择系统类型", 1=>"ios", 2=>"android")); ?></td>
			<td class="img_op"></td>
			<td class="vtop tips2">sadasd</td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>图片类型:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'type')->dropDownList(array(0=>"请选择图片类型", 1=>"Logo", 2=>"banner图", 3=>"引导页")); ?></td>
			<td class="img_op"></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label">启用时间:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'valid_time_from')->input('date'); ?></td>
			<td class="img_op"></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label">停用时间:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'valid_time_to')->input('date'); ?></td>
			<td class="img_op"></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>上传图片:</td>
			<td class="img_td_input">
				<?php echo $form->field($model, 'img_url')->fileInput(); ?>
			</td>
			<td class="img_op"><a onclick="uploadImg()" href="javascript:;">上传</a></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>图片名称:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'title')->textInput(); ?></td>
			<td class="img_op"></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>图片高度:</td>
			<td class="img_td_input">
				<?php echo $form->field($model, 'height')->textInput(array("style"=>"width:50px;")); ?>
			</td>
			<td class="img_op"></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>图片宽度:</td>
			<td class="img_td_input">
				<?php echo $form->field($model, 'width')->textInput(array("style"=>"width:50px;")); ?>
			</td>
			<td class="img_op"></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label">活动页url:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'act_url')->textInput(); ?></td>
			<td class="img_op"></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label"><font color="red">*</font>状态:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'status')->dropDownList(array(0=>"请选择状态", 1=>"未启用", 2=>"启用")); ?></td>
			<td class="img_op"></td>
			<td class="vtop tips2"></td>
		</tr>

		<tr class="hover">
			<td class="img_td_label">备注:</td>
			<td class="img_td_input"><?php echo $form->field($model, 'comment')->textInput(); ?></td>
			<td class="img_op"></td>
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