<?php
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('financial', 'menu_account_recharge');
$this->showsubmenuanchors('后台充值');
$this->showtips('技巧提示', [
	'后台充值请慎重操作，不诚信行为后果自负',
]);
?>

<?php $form = ActiveForm::begin(['id' => 'recharge-form']); ?>
<table class="tb tb2">
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'username'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'username')->textInput(['autocomplete' => 'off']); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'money'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'money')->textInput(['autocomplete' => 'off']); ?></td>
		<td class="vtop tips2">只能1000以内的金额</td>
	</tr>
	<tr><td class="td27" colspan="2"><?php echo $this->activeLabel($model, 'remark'); ?></td></tr>
	<tr class="noborder">
		<td class="vtop rowform"><?php echo $form->field($model, 'remark')->textarea(); ?></td>
		<td class="vtop tips2"></td>
	</tr>
	<tr>
		<td colspan="15">
			<input type="submit" value="提交" name="submit_btn" class="btn">
		</td>
	</tr>
</table>
<?php ActiveForm::end(); ?>