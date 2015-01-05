<?php
use backend\components\widgets\ActiveForm;
?>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>
<!-- 富文本编辑器 注：360浏览器无法显示编辑器时，尝试切换模式（如兼容模式）-->
<script type="text/javascript">
var UEDITOR_HOME_URL = '<?php echo $this->baseUrl; ?>/js/ueditor/'; //一定要用这句话，否则你需要去ueditor.config.js修改路径的配置信息 
</script>
<script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/ueditor/ueditor.all.js"></script>
<script type="text/javascript">
var ue = UE.getEditor('article-content');
</script>

<?php $form = ActiveForm::begin(['id' => 'article-form']); ?>
	<table class="tb tb2">
		<tr><td class="td27" colspan="2">标题:</td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'title')->textInput(); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr><td class="td27" colspan="2">栏目类型:</td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'type_id')->listBox($articleTypeItems, ['size' => 6]); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr><td class="td27" colspan="2">排序:</td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'order')->textInput(); ?></td>
			<td class="vtop tips2">越大越靠前，默认为0</td>
		</tr>
		<tr><td class="td27" colspan="2">摘要:</td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'summary')->textarea(); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr><td class="td27" colspan="2">内容:</td></tr>
		<tr class="noborder">
			<td class="vtop rowform" colspan="2">
				<div style="width:780px;height:400px;margin:5px auto 40px 0;">
                    <?php echo $form->field($model, 'content')->textarea(['style' => 'width:780px;height:295px;']); ?>
                </div>
                <div class="help-block"><?php echo $model->getFirstError('content'); ?></div>
			</td>
		</tr>
		<tr>
			<td colspan="15">
				<input type="submit" value="提交" name="submit_btn" class="btn">
			</td>
		</tr>
	</table>
<?php ActiveForm::end(); ?>
