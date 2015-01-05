<?php

use yii\helpers\Html;
use backend\components\widgets\ActiveForm;
use common\models\Project;

?>

<!-- 富文本编辑器 注：360浏览器无法显示编辑器时，尝试切换模式（如兼容模式）-->
<script type="text/javascript">
var UEDITOR_HOME_URL = '<?php echo $this->baseUrl; ?>/js/ueditor/'; //一定要用这句话，否则你需要去ueditor.config.js修改路径的配置信息 
</script>
<script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl; ?>/js/ueditor/ueditor.all.js"></script>
<script type="text/javascript">
var ue = UE.getEditor('project-desc');
</script>

<?php $form = ActiveForm::begin(['id' => 'project-form']); ?>
	<table class="tb tb2">
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'type'); ?></td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'type')->dropDownList(Project::$typeList); ?></td>
			<td class="vtop tips2">项目类型</td>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'name'); ?></td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'name')->textInput(); ?></td>
			<td class="vtop tips2">作为项目标题在App中展示</td>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'total_money'); ?>(元)</td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'total_money')->textInput(); ?></td>
			<td class="vtop tips2">项目金额必须是起购的整数倍</td>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'min_invest_money'); ?>(元)</td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'min_invest_money')->dropDownList(Project::$minInvestMoneys); ?></td>
			<td class="vtop tips2">每次投资的最小额度</td>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'apr'); ?>(%)</td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'apr')->textInput(); ?></td>
			<td class="vtop tips2">预计年化收益</td>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'period'); ?></td></tr>
		<tr class="noborder">
			<td class="vtop rowform">
				<?php echo $form->field($model, 'period', ['options' => ['style' => 'float:left;']])->textInput(['style' => 'width:160px;']); ?>
				<?php echo Html::activeRadioList($model, 'is_day', ['0' => '月', '1' => '天']); ?>
			</td>
			<td class="vtop tips2"></td>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'product_type'); ?></td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'product_type')->dropDownList(Project::$productTypes); ?></td>
			<td class="vtop tips2">例如：车辆抵押、房产抵押、信托理财等</td>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'is_novice'); ?></td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'is_novice')->radioList(['1' => '是', '0' => '否']); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'effect_time'); ?>(天)</td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'effect_time')->textInput(); ?></td>
			<td class="vtop tips2">如果写N，表示项目发布后N天内可投资</td>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'interest_date'); ?></td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'interest_date')->textInput(); ?></td>
			<td class="vtop tips2">如有确定计息日期，则填该日期，例如：11月10日</td>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'repay_date'); ?></td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'repay_date')->textInput(); ?></td>
			<td class="vtop tips2">如有确定还款日期，则填该日期，例如：11月10日</td>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'repayment_remark'); ?></td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'repayment_remark')->textInput(); ?></td>
			<td class="vtop tips2"></td>
		</tr>
		<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'summary'); ?></td></tr>
		<tr class="noborder">
			<td class="vtop rowform"><?php echo $form->field($model, 'summary')->textarea(); ?></td>
			<td class="vtop tips2">
                “项目概述”需要简单的项目用途，会展示在App的项目详情中<br><br>
                “详细描述”包含披露项目具体投资去向，借款人信息等
            </td>
		</tr>
		<tr>
			<td class="td27" colspan="2">
				<?php echo $this->activeLabel($model, 'desc'); ?>
				<a href="http://api.koudailc.com/project/desc-detail?id=<?php echo $model->id; ?>" target="_blank" style="margin-left:20px;">在线预览</a>
				<span style="color:#999;font-weight:normal;">（暂只支持线上环境，且为已保存记录）</span>
			</td>
		</tr>
		<tr class="noborder">
			<td colspan="2">
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