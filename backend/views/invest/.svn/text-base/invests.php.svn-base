<?php
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Project;
use backend\components\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\ProjectInvest;

/**
 * @var backend\components\View $this
 */
$this->shownav('project', 'menu_invest_invests');
$this->showsubmenu('投资记录');

?>

<?php $form = ActiveForm::begin(['id' => 'searchform','method' => "get", 'options' => ['style' => 'margin-bottom:5px;']]); ?>
	用户名关键词：<input type="text" value="<?php echo isset($search['keyword']) ? trim($search['keyword']) : ''; ?>" name="keyword" class="txt">&nbsp;
        状态：<?php echo Html::dropDownList('status', Yii::$app->getRequest()->get('status', ''), ProjectInvest::$status, ['prompt' => '所有状态']); ?>&nbsp;
        是否债权转换：<?php echo Html::dropDownList('is_transfer', Yii::$app->getRequest()->get('is_transfer', ''), ProjectInvest::$is_transfer,['prompt' => '所有']); ?>&nbsp;
	<input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th width="100">ID</th>
		<th width="200">投资人</th>
                <th width="200">项目名称</th>
                <th width="200">项目期限</th>
		<th>投资金额</th>		
		<th>债权转让</th>
		<th>状态</th>
                <th>时间</th>
		<th class="td23">操作</th>
	</tr>
	<?php foreach ($invests as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo $value->username; ?></td>
                <td><?php echo $value->project->name; ?></td>
                <td><?php echo $value->project->period. ' ' . ( $value->project->is_day ? '天' : '月'); ?></td>
		<td><?php echo sprintf('%.2f', $value->invest_money / 100); ?></td>		
		<td><?php echo $value->is_transfer ? '是' : '否'; ?></td>
		<td><?php echo projectInvest::$status[$value->status]; ?></td>
                <td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
                <td><a href="<?php echo Url::to(['invest/view', 'id' => $value->id]); ?>">查看</a></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php if (empty($invests)): ?>
<div class="no-result">暂无记录</div>
<?php endif; ?>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>