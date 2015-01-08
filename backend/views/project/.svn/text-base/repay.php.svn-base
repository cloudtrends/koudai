<?php

use yii\helpers\Html;
use backend\components\widgets\ActiveForm;
use common\models\ProjectRepayment;

/**
 * @var backend\components\View $this
 */
$this->shownav('project', 'menu_project_repay_list');
$this->showsubmenu('还款管理');

?>

<script type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>

<div id="baseinfo">
	<?php echo $this->render('_baseinfo', ['model' => $model]); ?>
</div>

<?php $form = ActiveForm::begin(['id' => 'repayment-form']); ?>
<table class="tb tb2 fixpadding">
	<tr><th class="partition" colspan="15">平台还款</th></tr>
	<tr>
		<td class="td24">还款总额：</td>
		<td width="300" id="total_money"><?php echo sprintf('%.2f', ($model->getProfits() + $model->total_money) / 100); ?></td>
		<td class="td24">计划还款日：</td>
		<td><?php echo $model->getRepayDate(); ?></td>
	</tr>
	<tr>
		<td class="td24">还款本金：</td>
		<td><?php echo sprintf('%.2f', $model->total_money / 100); ?></td>
		<td class="td24">还款利息：</td>
		<td><?php echo sprintf('%.2f', $model->getProfits() / 100); ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $this->activeLabel($repayment, 'status'); ?></td>
		<td class="vtop rowform" colspan="3"><?php echo $form->field($repayment, 'status')->radioList(ProjectRepayment::$status); ?></td>
	</tr>
	<tr class="loaner-repay"<?php echo !$repayment->status || $repayment->status == ProjectRepayment::STATUS_PLATFORM_FULL_REPAY ? ' style="display:none;"' : '' ?>>
		<td class="td24"><?php echo $this->activeLabel($repayment, 'loaner_repay_money'); ?></td>
		<td class="vtop rowform" colspan="3">
			<?php echo $form->field($repayment, 'loaner_repay_money', ['options' => ['style' => 'float:left;']])->textInput(['style' => 'width:200px;']); ?>
			<span class="tips2"></span>
		</td>
	</tr>
	<tr class="loaner-repay"<?php echo !$repayment->status || $repayment->status == ProjectRepayment::STATUS_PLATFORM_FULL_REPAY ? ' style="display:none;"' : '' ?>>
		<td class="td24"><?php echo $this->activeLabel($repayment, 'loaner_repay_time'); ?></td>
		<td class="vtop rowform" colspan="3"><?php echo $form->field($repayment, 'loaner_repay_time')->textInput([
			'style' => 'width:200px;',
			'onFocus' => "WdatePicker()",
		]); ?></td>
	</tr>
	<tr>
		<td class="td24">备注：</td>
		<td class="vtop rowform" colspan="3"><?php echo Html::textarea('remark', '', ['style' => 'width:300px;']); ?></td>
	</tr>
	<tr>
		<td colspan="15">
			<input type="submit" value="提交" name="submit_btn" class="btn">
		</td>
	</tr>
</table>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
$(function(){
	$('input:radio[name="ProjectRepayment[status]"]').change(function(){
		var status = $("input:radio:checked").val();
		if (status == <?php echo ProjectRepayment::STATUS_PLATFORM_FULL_REPAY; ?>) {
			$('.loaner-repay').hide();
		} else {
			if (status == <?php echo ProjectRepayment::STATUS_LOANER_FULL_REPAY; ?>) {
				$('#projectrepayment-loaner_repay_money').val($('#total_money').html());
			} else {
				$('#projectrepayment-loaner_repay_money').val('');
			}
			$('.loaner-repay').show();
		}
	});
});
</script>
      <script>
                  //jquery 入口
                  $(function(){
                    $("#financial-project_id").change(function(){
                      //获取选择的值
                      var val = $(this).val();
                      //得到当前的select对象
                      var $_this = $(this);
                      //ajax操作
                      $.ajax({
                        url:'<{:U("Area/loadCity")}>',
                        async:true,
                        data:{upid:val},
                        type:'post',
                        dataType:'json',
                        success:function(data){
                  })
                </script>