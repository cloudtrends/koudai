<?php
/**
 * @var backend\components\View $this
 */
$this->shownav('financial', 'menu_financial_regular_info');
?>
<table class="tb tb2 fixpadding">
    <tr><th class="partition" colspan="15">整体概况</th></tr>
    <tr height=60>
        <td class="td27" width=200>网站所有正在进行中的总额（元）</td>
        <td width=200><?php echo sprintf('%.2f',$regularlist['projects_total_money'] / 100); ?></td>
        <td class="tips2">项目已经完成金额（还款中+投资中）</td>
    </tr>
    <tr height=60>
     <td class="td27">后台实际进行中的总额（元）</td>
        <td><?php echo sprintf('%.2f',$regularlist['active_total_money'] / 100); ?></td>
        <td class="tips2">后台待还款项目本金及其当前的借款待收益+募集中项目本金</td>
    </tr>
    <tr><th class="partition" colspan="15">项目结算盈亏情况</th></tr>
    <tr height=60>
        <td class="td27">总盈利（元）</td>
        <td><?php echo sprintf('%.2f',($regularlist['history_profit'] + $regularlist['repayment_profit']) / 100); ?></td>
        <td class="tips2">历史盈利（元） +  待收盈利（元）</td>
    </tr>
    <tr height=60>
        <td class="td27">历史盈利（元）</td>
        <td><?php echo sprintf('%.2f',$regularlist['history_profit'] / 100); ?></td>
        <td class="tips2"><?php echo '后台历史项目总收益（所有 后台完成还款项目历史平均一天借款收益*期限 之和）'.sprintf('%.2f',$regularlist['history_return_revenue'] / 100).'- 历史投资人收益（所有 完成还款项目中已经完成金额*年利率/365 * 借款期限 之和）'.sprintf('%.2f',$regularlist['history_pay_revenue'] / 100); ?></td>
    </tr>
    <tr height=60>
        <td class="td27">待收盈利（元）</td>
        <td><?php echo sprintf('%.2f',$regularlist['repayment_profit'] / 100); ?></td>
        <td class="tips2">后台待收收益（后台所有 待还款项目平均一天借款待收益*期限 之和） - 投资人待收益（后台所有 投资中项目中已经完成金额*年利率/365 * 借款期限 之和）</td>
    </tr>
    <tr height=60>
        <td class="td27">实际盈利（元）</td>
        <td><?php echo sprintf('%.2f',$regularlist['total_investor_revenue'] / 100); ?></td>
        <td class="tips2">后台借款收益-后台用户收益</td>
    </tr>
</table>