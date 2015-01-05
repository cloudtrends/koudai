<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('financial', 'menu_account_list');
$this->showsubmenu('用户资金信息');

?>
<style type="text/css">
th {border-right: 1px dotted #deeffb;}
</style>

<?php $form = ActiveForm::begin(['id' => 'searchform', 'method' => "get", 'options' => ['style' => 'margin-bottom:5px;']]); ?>
	用户ID：<input type="text" value="<?php echo Yii::$app->getRequest()->get('user_id', ''); ?>" name="user_id" class="txt" style="width:120px;">&nbsp;       
	用户名：<input type="text" value="<?php echo Yii::$app->getRequest()->get('username', ''); ?>" name="username" class="txt" style="width:120px;">&nbsp;
	姓名：<input type="text" value="<?php echo Yii::$app->getRequest()->get('realname', ''); ?>" name="realname" class="txt" style="width:120px;">&nbsp;
        <input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>


<table class="tb tb2 fixpadding">
	<tr class="header">
		<th rowspan="2">ID</th>
		<th rowspan="2">用户名</th>
		<th rowspan="2">姓名</th>
		<th rowspan="2">总资产</th>
		<th colspan="2" style="text-align:center;">账户余额</th>
		<th colspan="4" style="text-align:center;">持有资产</th>
		<th colspan="2" style="text-align:center;">昨日总收益</th>
		<th rowspan="2">口袋宝累计收益</th>
		<th rowspan="2" style="border-right:none;">所有累计收益</th>
	</tr>
	<tr class="header">
		<th>可用余额</th>
		<th>提现中余额</th>
		<th>投资中金额（冻结）</th>
		<th>待收本金</th>
		<th>待收收益</th>
		<th>口袋宝总额</th>
		<th>昨日项目收益</th>
		<th>昨日口袋宝收益</th>
	</tr>
	<?php foreach ($accounts as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->user_id; ?></td>
		<td><?php echo $value->user ? $value->user->username : ''; ?></td>
		<td><?php echo $value->user ? $value->user->realname : ''; ?></td>
		<td><?php echo sprintf('%.2f', $value->total_money / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->usable_money / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->withdrawing_money / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->investing_money / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->duein_capital / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->duein_profits / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->kdb_total_money / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->lastday_proj_profits	 / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->lastday_kdb_profits / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->kdb_total_profits / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->total_profits / 100); ?></td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>