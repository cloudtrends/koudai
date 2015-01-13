<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;
use common\models\Financial;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$menuKey = $title = '';
switch ($type) {
	default:
		$menuKey = 'menu_financial_list';
		$title = '项目列表';
}
$this->shownav('financial', $menuKey);
$this->showsubmenu($title);
?>

<?php $form = ActiveForm::begin(['id' => 'searchform', 'method' => 'get', 'options' => ['style' => 'margin-bottom:5px;']]); ?>
    ID：<input type="text" value="<?php echo Yii::$app->getRequest()->get('id', ''); ?>" name="id" class="txt" style="width:60px;">&nbsp;
    项目ID：<input type="text" value="<?php echo Yii::$app->getRequest()->get('project_id', ''); ?>" name="project_id" class="txt" style="width:60px;">&nbsp;
    名称关键词：<input type="text" value="<?php echo Yii::$app->getRequest()->get('project_name', ''); ?>" name="project_name" class="txt" style="width:120px;">&nbsp;
    状态：<?php echo Html::dropDownList('status', Yii::$app->getRequest()->get('status', ''), Financial::$status, ['prompt' => '所有状态']); ?>&nbsp;
    项目类型：<?php echo Html::dropDownList('project_type', Yii::$app->getRequest()->get('project_type', ''), Financial::$type, ['prompt' => '所有类型']); ?>&nbsp;
    放款时间：<?php echo Html::dropDownList('loan_time', Yii::$app->getRequest()->get('loan_time', ''), Financial::$loan_time_ranger, ['prompt' => '请选择']); ?>&nbsp;
    还款时间：<?php echo Html::dropDownList('borrower_repayment_time', Yii::$app->getRequest()->get('borrower_repayment_time', ''), Financial::$borrower_repayment_time_ranger, ['prompt' => '请选择']); ?>&nbsp;
    <input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>

<form name="listform" method="post">
    <table class="tb tb2 fixpadding">
    <tr class="header">
        <th>ID</th>
        <th>项目类型</th>
        <th>项目名称</th>
        <th>总融资额(元)</th>
        <th>借款人利率(%)</th>
        <th>用户利率(%)</th>
        <th>实际收益(元)</th>
        <th>借款收益(元)</th>
        <th>用户收益(元)</th>
        <th>状态</th>
        <th>放款时间</th>
        <th>还款时间</th>
        <th>操作</th>
    </tr>
    <?php foreach ($financials as $value): ?>
    <?php if (strtotime($value->loan_time) == strtotime(date('Y-m-d',time()))): ?>
        <?php echo '<tr class="hover" style="background:#ccc;">';?>
    <?php else: ?>
        <?php echo '<tr class="hover">';?>
    <?php endif; ?>
        <td class="td25"><?php echo $value->id; ?></td>
        <td><?php echo Financial::$type[$value->project_type]; ?></td>
        <td><?php echo $value->project_name; ?></td>
        <td><?php echo sprintf('%.2f',$value->total_amount_financing / 100); ?></td>
        <td><?php echo $value->borrower_rate; ?></td>
        <td><?php echo $value->user_rate; ?></td>
        <td><?php echo sprintf('%.2f',$value->total_revenue / 100); ?></td>
        <td><?php echo sprintf('%.2f',$value->platform_revenue / 100); ?></td>
        <td><?php echo sprintf('%.2f',$value->investor_revenue / 100); ?></td>
        <td><?php echo Financial::$status[$value->status]; ?></td>
        <td><?php echo $value->loan_time; ?></td>
        <td><?php echo $value->borrower_repayment_time; ?></td>
        <td class="td23">
            <?php if ($type == 'all'): ?>
                 <a href="<?php echo Url::to(['financial/view', 'id' => $value->id]); ?>">查看</a>
                 <?php if ($value->status != Financial::STATUS_INVALID): ?>
                        <?php if ($value->status == Financial::STATUS_CREATE_RAISED): ?>
                            <a href="<?php echo Url::to(['financial/edit', 'id' => $value->id,'project_type' =>$value->project_type]); ?>">编辑</a>
                        <?php endif; ?>
                        <?php if ($value->status == Financial::PENDING_AUDIT): ?>
                            <a href="<?php echo Url::to(['financial/edit', 'id' => $value->id,'project_type' =>$value->project_type]); ?>">审核</a>
                        <?php endif; ?>
                        <?php if ($value->status == Financial::STATUS_REPAYMENT): ?>
                            <a href="<?php echo Url::to(['financial/operation', 'id' => $value->id]); ?>">操作</a>
                        <?php endif; ?>
                    <a onclick="return confirmMsg('确定要将该项目作废吗？');" href="<?php echo Url::to(['financial/invalid', 'id' => $value->id]); ?>">作废</a>
                <?php else: ?>
                    <a onclick="return confirmMsg('确定要将该项目删除吗？');" href="<?php echo Url::to(['financial/delete', 'id' => $value->id]); ?>">删除</a>
                <?php endif; ?>
             <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
    </table>
    <?php if (empty($financials)): ?>
        <div class="no-result">暂无记录</div>
    <?php endif; ?>
</form>
<?php echo LinkPager::widget(['pagination' => $pages]); ?>