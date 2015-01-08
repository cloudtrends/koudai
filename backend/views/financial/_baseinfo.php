<?php
use common\models\Financial;
?>
<table class="tb tb2 fixpadding">	
	<tr>		
		<td class="td24 td27"><?php echo $model->getAttributeLabel('project_type'); ?>：</td>
		<td colspan="3"><?php echo Financial::$type[$model->project_type]; ?></td>
	</tr>
	<tr>		
		<td class="td24"><?php echo $model->getAttributeLabel('project_id'); ?>：</td>
		<td  width="300"><?php echo $model->project_id ? $model->project_id : '无'; ?></td>
		<td class="td24"><?php echo $model->getAttributeLabel('project_name'); ?>：</td>
		<td><?php echo $model->project_name; ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('total_amount_financing'); ?>：</td>
		<td colspan="3"><?php echo sprintf('%.2f',$model->total_amount_financing / 100); ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('borrower_rate'); ?>：</td>
		<td><?php echo $model->borrower_rate; ?></td>
		<td class="td24"><?php echo $model->getAttributeLabel('user_rate'); ?>：</td>
		<td><?php echo $model->user_rate; ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('platform_revenue'); ?>：</td>
		<td><?php echo sprintf('%.2f',$model->platform_revenue / 100); ?></td>
		<td class="td24"><?php echo $model->getAttributeLabel('investor_revenue'); ?>：</td>
		<td><?php echo sprintf('%.2f',$model->investor_revenue / 100); ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('total_revenue'); ?>：</td>
		<td colspan="3"><?php echo sprintf('%.2f',$model->total_revenue / 100); ?></td>	
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('project_start_time'); ?>：</td>
		<td><?php echo $model->project_start_time ? date('Y-m-d H:i:s', $model->project_start_time) : '-'; ?></td>
		<td class="td24"><?php echo $model->getAttributeLabel('project_end_time'); ?>：</td>
		<td><?php echo $model->project_end_time ? date('Y-m-d H:i:s', $model->project_end_time) : '-'; ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('loan_time'); ?>：</td>
		<td><?php echo $model->loan_time ? $model->loan_time : '-'; ?></td>
		<td class="td24"><?php echo $model->getAttributeLabel('borrower_repayment_time'); ?>：</td>
		<td><?php echo $model->borrower_repayment_time ? $model->borrower_repayment_time.$model->loan : '-'; ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('created_at'); ?>：</td>
		<td><?php echo $model->created_at ? date('Y-m-d H:i:s', $model->created_at) : '-'; ?></td>
		<td class="td24"><?php echo $model->getAttributeLabel('updated_at'); ?>：</td>
		<td><?php echo $model->updated_at ? date('Y-m-d H:i:s', $model->updated_at) : '-'; ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('status'); ?>：</td>
		<td  colspan="3"><?php echo Financial::$status[$model->status]; ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('remarks'); ?>：</td>
		<td colspan="3"><?php echo $model->remarks ? $model->remarks : '无'; ?></td>
	</tr>
</table>