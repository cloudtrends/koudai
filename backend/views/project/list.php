<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;
use common\models\Project;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$menuKey = $title = '';
switch ($type) {
	case 'review-new':
		$menuKey = 'menu_project_review_new';
		$title = '审核新项目';
		break;
	case 'investing':
		$menuKey = 'menu_project_investing';
		$title = '投资中项目';
		break;
	case 'review-full':
		$menuKey = 'menu_project_review_full';
		$title = '复审满款项目';
		break;
	case 'repay-list':
		$menuKey = 'menu_project_repay_list';
		$title = '还款管理';
		break;
	default:
		$menuKey = 'menu_project_list';
		$title = '项目列表';
}
$this->shownav('project', $menuKey);
$this->showsubmenu($title);

?>

<?php $form = ActiveForm::begin(['id' => 'searchform', 'method' => 'get', 'options' => ['style' => 'margin-bottom:5px;']]); ?>
	ID：<input type="text" value="<?php echo Yii::$app->getRequest()->get('id', ''); ?>" name="id" class="txt" style="width:60px;">&nbsp;
	名称关键词：<input type="text" value="<?php echo Yii::$app->getRequest()->get('name', ''); ?>" name="name" class="txt" style="width:120px;">&nbsp;
	状态：<?php echo Html::dropDownList('status', Yii::$app->getRequest()->get('status', ''), Project::$status, ['prompt' => '所有状态']); ?>&nbsp;
	<input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>ID</th>
		<th>类型</th>
		<th>名称</th>
		<th>创建人</th>
		<th>项目金额</th>
		<th>已投金额</th>
		<th>年利率</th>
		<th>期限</th>
		<?php if ($type == 'repay-list'): ?>
		<th>计划还款日</th>
		<?php endif; ?>
		<th>新手专属</th>
		<th>状态</th>
		<th>操作</th>
	</tr>
	<?php foreach ($projects as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->id; ?></td>
		<td><?php echo Project::$typeList[$value->type]; ?></td>
		<td><?php echo $value->name; ?></td>
		<td><?php echo $value->created_username; ?></td>
		<td><?php echo sprintf('%.2f', $value->total_money / 100); ?>元</td>
		<td><?php echo sprintf('%.2f', $value->success_money / 100); ?>元</td>
		<td><?php echo $value->apr; ?>%</td>
		<td><?php echo $value->getPeriodLabel(); ?></td>
		<?php if ($type == 'repay-list'): ?>
		<td><?php echo $value->getRepayDate(); ?></td>
		<?php endif; ?>
		<td><?php echo $value->is_novice ? '是' : '否'; ?></td>
		<td><?php echo Project::$status[$value->status]; ?></td>
		<td class="td23">
			<a href="<?php echo Url::to(['project/view', 'id' => $value->id]); ?>">查看</a>
            <?php if ($type == 'all'): ?>
                <?php //if ($value->status == Project::STATUS_NEW): ?>
                <a href="<?php echo Url::to(['project/edit', 'id' => $value->id]); ?>">编辑</a>
            	<?php //endif; ?>
            <?php elseif ($type == 'review-new'): ?>
                <?php if ($value->status == Project::STATUS_NEW): ?>
                <a href="<?php echo Url::to(['project/review-new', 'id' => $value->id]); ?>">初审</a>
            	<?php endif; ?>
            <?php elseif ($type == 'investing'): ?>
                <?php if ($value->status == Project::STATUS_PUBLISHED): ?>
                <a href="<?php echo Url::to(['project/cancle', 'id' => $value->id]); ?>">作废</a>
            	<?php endif; ?>
            <?php elseif ($type == 'review-full'): ?>
                <?php if ($value->status == Project::STATUS_FULL): ?>
                <a href="<?php echo Url::to(['project/review-full', 'id' => $value->id]); ?>">满款复审</a>
            	<?php endif; ?>
            <?php elseif ($type == 'repay-list'): ?>
                <?php if ($value->status == Project::STATUS_REPAYING): ?>
                <a href="<?php echo Url::to(['project/repay', 'id' => $value->id]); ?>">还款</a>
            	<?php else: ?>
                <a href="<?php echo Url::to(['project/edit-repayment', 'id' => $value->id]); ?>">更新还款记录</a>
            <?php endif; ?>
        <?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
</table>
<?php if (empty($projects)): ?>
<div class="no-result">暂无记录</div>	
<?php endif; ?>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>