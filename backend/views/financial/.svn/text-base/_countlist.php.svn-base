<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('financial', 'menu_financial_count_list');
$this->showsubmenuanchors('总账明细列表');
?>

<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>
<?php $form = ActiveForm::begin(['id' => 'searchform', 'method' => 'get', 'options' => ['style' => 'margin-bottom:5px;']]); ?>
    日期：<input type="text" value="<?php echo Yii::$app->getRequest()->get('date', ''); ?>" name="date" onfocus="WdatePicker({startDcreated_atate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true,readOnly:true})" class="txt" style="width:120px;">&nbsp;
    <input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>

<table class="tb tb2 fixpadding">
    <tr class="header">
        <th>ID</th>
        <th>日期</th>
        <th>当前网站总额（元）</th>
        <th>用户总余额（元）</th>
        <th>活期总额（口袋宝）（元）</th>
        <th>定期总额（本金）（元）</th>
        <th>商户号总额（元）</th>
        <th>待收益总额（元）</th>
        <th>第三方支付余额（元）</th>
        <th>待收还款总额（元）</th>
        <th>历史平台盈亏额（元）</th>        
        <th>盈利额（元）</th>        
        <th>操作</th>
    </tr>
    <?php foreach ($countlist as $value): ?>
    <tr class="hover">
        <td  class="td25"><?php echo $value->id; ?></td>
        <td><?php echo date('Y-m-d',$value->date-86400); ?></td>
        <td><?php echo sprintf('%.2f',$value->site_total_money / 100); ?></td>
        <td><?php echo sprintf('%.2f',$value->usable_money / 100); ?></td>
        <td><?php echo sprintf('%.2f',$value->kdb_total_money / 100); ?></td>
        <td><?php echo sprintf('%.2f',$value->projects_total_money / 100); ?></td>
        <td><?php echo sprintf('%.2f',$value->merchant_number_money / 100); ?></td>
        <td><?php echo sprintf('%.2f',$value->to_total_revenue / 100); ?></td>
        <td><?php echo sprintf('%.2f',$value->third_party_alipay_balance / 100); ?></td>
        <td><?php echo sprintf('%.2f',$value->to_total_repayment / 100); ?></td>
        <td><?php echo sprintf('%.2f',$value->historical_platform_profit / 100); ?></td>
        <td><?php echo sprintf('%.2f',$value->profit / 100); ?></td>
        <td class="td23"><a href="<?php echo Url::to(['financial/count-info', 'id' => $value->id]); ?>">查看</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php if (empty($countlist)): ?>
    <div class="no-result">暂无记录</div>   
<?php endif; ?>
<?php echo LinkPager::widget(['pagination' => $pages]); ?>
