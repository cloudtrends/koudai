<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Project;
use backend\components\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\ProjectInvest;
use common\models\User;
$this->shownav('project', 'menu_invest_assign');
$this->showsubmenu('转让信息');

?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th width="100">ID</th>
                	
                <th width="200">项目名称</th>
                <th width="200">投资人</th>
                <th width="200">转让价格</th>
                <th width="200">转让利率</th>
		<th>状态</th>		
		<th>手续费</th>
		<th>开始时间</th>
                <th>结束时间时间</th>
		<th class="td23">查看投资记录</th>
	</tr>
	<?php foreach ($assignList as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value['id']; ?></td>
               
                <td><?php echo $value['project']; ?></td>
		<td><?php echo $value['user_name']; ?></td>
                <td><?php echo sprintf('%.2f',$value['assign_fee'] / 100); ?></td>
                <td><?php echo $value['assign_rate']; ?> %</td>
                <td><?php echo $value['status']; ?></td>
                <td><?php echo $value['commission_rate']; ?> 元</td>
                <td><?php echo date('Y-m-d H:i:s', $value['assign_start_date']); ?></td>
                <td><?php echo date('Y-m-d H:i:s', $value['assign_start_date']); ?></td>
                <td><a href="<?php echo Url::to(['invest/view', 'id' => $value['invest_id']]); ?>">查看</a></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php if (empty($assignList)): ?>
<div class="no-result">暂无记录</div>
<?php endif; ?>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>