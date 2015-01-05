<?php

use common\models\KdbInfo;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('project', 'menu_koudaibao_setting');
$this->showsubmenu('口袋宝配置');

?>

<!-- 富文本编辑器 注：360浏览器无法显示编辑器时，尝试切换模式（如兼容模式）-->
<script type="text/javascript">
var UEDITOR_HOME_URL = '<?php echo $this->baseUrl; ?>/js/ueditor/'; //一定要用这句话，否则你需要去ueditor.config.js修改路径的配置信息 
</script>
<script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/ueditor/ueditor.all.js"></script>
<script type="text/javascript">
var ue = UE.getEditor('kdbinfo-desc');
</script>

<?php $form = ActiveForm::begin(['id' => 'setting-form']); ?>
<table class="tb tb2">
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'title'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'title')->textInput(); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'total_money'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'total_money')->textInput(); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'apr'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'apr')->textInput(); ?></td>
		<td class="vtop tips2">百分比数值</td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'status'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'status')->dropDownList(KdbInfo::$status); ?></td>
		<td class="vtop tips2">用户设置是否开放投资</td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'daily_invest_limit'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'daily_invest_limit')->textInput(); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'daily_withdraw_limit'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'daily_withdraw_limit')->textInput(); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'user_invest_limit'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'user_invest_limit')->textInput(); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'min_invest_money'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'min_invest_money')->textInput(); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'product_type'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'product_type')->textInput(); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'summary'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'summary')->textarea(); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'instruction'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'instruction')->textarea(); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'desc'); ?></td></tr>
	<tr>
		<td class="td27" colspan="2">
			<div style="width:780px;height:400px;margin:5px auto 40px 0;">
                <?php echo $form->field($model, 'desc')->textarea(['style' => 'width:780px;height:295px;']); ?>
            </div>
            <div class="help-block"><?php echo $model->getFirstError('desc'); ?></div>
	    </td>
	</tr>
	<tr>
		<td colspan="15">
			<input type="submit" value="提交" name="submit_btn" class="btn">
		</td>
	</tr>
</table>
<?php ActiveForm::end(); ?>