<?php

use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('project', 'menu_project_repay_list');
$this->showsubmenu('还款管理');

?>

<script type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>

<?php $form = ActiveForm::begin(['id' => 'repayment-form']); ?>
<table class="tb tb2 fixpadding">
	<tr>
		<td class="td24">计划还款日：</td>
		<td><?php echo $project->getRepayDate(); ?></td>
	</tr>
	<tr>
		<td class="td24">还款总额：</td>
		<td><?php echo sprintf('%.2f', ($project->getProfits() + $project->total_money) / 100); ?></td>
	</tr>
	<tr>
		<td class="td24">还款本金：</td>
		<td><?php echo sprintf('%.2f', $project->total_money / 100); ?></td>
	</tr>
	<tr>
		<td class="td24">还款利息：</td>
		<td><?php echo sprintf('%.2f', $project->getProfits() / 100); ?></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'loaner_repay_money'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'loaner_repay_money')->textInput(); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'loaner_repay_time'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'loaner_repay_time')->textInput(['onFocus' => "WdatePicker()"]); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'overdue_money'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'overdue_money')->textInput(); ?></td>
		<td class="vtop tips2">由于逾期等原因导致比应还总额多的金额</td>
	</tr>
	<tr>
		<td colspan="15">
			<input type="submit" value="提交" name="submit_btn" class="btn">
		</td>
	</tr>
</table>
<?php ActiveForm::end(); ?>