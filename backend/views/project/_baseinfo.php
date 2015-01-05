<?php
use common\models\Project;
?>
<table class="tb tb2 fixpadding">
	<tr><th class="partition" colspan="15">基本信息</th></tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('name'); ?>：</td>
		<td width="300"><?php echo $model->name . " （" . Project::$status[$model->status] . "）"; ?></td>
		<td class="td24"><?php echo $model->getAttributeLabel('total_money'); ?>：</td>
		<td><?php echo sprintf('%.2f', $model->total_money / 100); ?> 元</td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('type'); ?>：</td>
		<td><?php echo Project::$typeList[$model->type] . " （" . $model->product_type . "）"; ?></td>
		<td class="td24"><?php echo $model->getAttributeLabel('apr'); ?>：</td>
		<td><?php echo $model->apr; ?> %</td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('created_username'); ?>：</td>
		<td><?php echo $model->created_username; ?></td>
		<td class="td24"><?php echo $model->getAttributeLabel('period'); ?>：</td>
		<td><?php echo $model->period . ' ' . ($model->is_day ? '天' : '月'); ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('created_at'); ?>：</td>
		<td><?php echo date('Y-m-d H:i:s', $model->created_at); ?></td>
		<td class="td24"><?php echo $model->getAttributeLabel('effect_time'); ?>：</td>
		<td><?php echo $model->effect_time; ?> 天</td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('publish_at'); ?>：</td>
		<td><?php echo $model->publish_at ? date('Y-m-d H:i:s', $model->publish_at) : '-'; ?></td>
		<td class="td24"><?php echo $model->getAttributeLabel('min_invest_money'); ?>：</td>
		<td><?php echo $model->min_invest_money / 100; ?> 元</td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('review_at'); ?>：</td>
		<td><?php echo $model->review_at ? date('Y-m-d H:i:s', $model->review_at) : '-'; ?></td>
		<td class="td24"><?php echo $model->getAttributeLabel('is_novice'); ?>：</td>
		<td><?php echo $model->is_novice ? '是' : '否'; ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('interest_date'); ?>：</td>
		<td colspan="3"><?php echo $model->interest_date; ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('repay_date'); ?>：</td>
		<td colspan="3"><?php echo $model->repay_date; ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('repayment_remark'); ?>：</td>
		<td colspan="3"><?php echo $model->repayment_remark; ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('summary'); ?>：</td>
		<td colspan="3"><?php echo $model->summary; ?></td>
	</tr>
	<tr>
		<td class="td24"><?php echo $model->getAttributeLabel('desc'); ?>：</td>
		<td colspan="3"><?php echo $model->desc; ?></td>
	</tr>
</table>