<?php
/**
 * @var backend\components\View $this
 */
$this->shownav('financial', 'menu_financial_current_info');
?>
<table class="tb tb2 fixpadding">
    <tr><th class="partition" colspan="15">整体概况</th></tr>
    <tr height=60>
        <td class="td27" width=200>当前网站口袋宝总额（元）</td>
        <td width=200><?php echo sprintf('%.2f',$currentlist['kdb_total_money'] / 100); ?></td>
        <td class="tips2"><?php echo '口袋宝资金池容量 '.sprintf('%.2f',$currentlist['kdb_total_money1'] / 100).'-当前剩余可投总额 '.sprintf('%.2f',$currentlist['kdb_total_money2'] / 100) ;?></td>
    </tr>
    <tr height=60>
        <td class="td27">当前用户收益总额（元）</td>
        <td><?php echo sprintf('%.2f',$currentlist['all_investor_revenue'] / 100); ?></td>
        <td class="tips2">口袋宝资金表中的历史盈利总额</td>
    </tr>
    <tr height=60>
        <td class="td27">当日支出收益（元）</td>
        <td><?php echo sprintf('%.2f',$currentlist['today_pay_revenue'] / 100); ?></td>
        <td class="tips2">用户日收益表中当天收益金额</td>
    </tr>
    <tr height=60>
        <td class="td27">后台进行中项目总额（元）</td>
        <td><?php echo sprintf('%.2f',$currentlist['active_total_money'] / 100); ?></td>
        <td class="tips2">后台待还款项目本金及其当前的借款待收益</td>
    </tr>
    <tr height=60>
        <td class="td27">后台当日总收益（元）</td>
        <td><?php echo sprintf('%.2f',$currentlist['today_return_revenue'] / 100); ?></td>
        <td class="tips2">所有还款中项目当天借款收益（还款中本金*借款人利率/365）之和</td>
    </tr>
    <tr height=60>
        <td class="td27">后台历史总收益（元）</td>
        <td><?php echo sprintf('%.2f',$currentlist['history_return_revenue'] / 100); ?></td>
        <td class="tips2">所有已还款项目一天借款收益（已还款本金*借款人利率/365*期限）之和</td>
    </tr>
    <tr><th class="partition" colspan="15">项目结算盈亏情况</th></tr>
    <tr height=60>
        <td class="td27">总盈利（元）</td>
        <td><?php echo sprintf('%.2f',($currentlist['history_return_revenue']-$currentlist['all_investor_revenue']) / 100); ?></td>
        <td class="tips2">后台历史总收益（元） - 当前用户收益总额（元）</td>
    </tr>
    <tr height=60>
        <td class="td27">当日盈利（元）</td>
        <td><?php echo sprintf('%.2f',($currentlist['today_return_revenue']-$currentlist['today_pay_revenue']) / 100); ?></td>
        <td class="tips2">后台当日总收益（元） - 当日支出收益（元）</td>
    </tr>
    <tr height=60>
        <td class="td27">实际盈利（元）</td>
        <td><?php echo sprintf('%.2f',$currentlist['total_investor_revenue'] / 100); ?></td>
        <td class="tips2">后台借款收益-后台用户收益</td>
    </tr>
</table>