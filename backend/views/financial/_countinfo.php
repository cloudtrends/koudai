<?php
/**
 * @var backend\components\View $this
 */

$this->showsubmenuanchors('总账明细详情');
?>
<table class="tb tb2 fixpadding">
    <tr><th class="partition" colspan="15">当前网站总额&nbsp;&nbsp;&nbsp;&nbsp;<?php echo sprintf('%.2f',$model->site_total_money / 100); ?>元</th></tr>
    <tr height=50>
        <td class="td27" width=200>定期总额（本金）（元）</td>
        <td  colspan=2><?php echo sprintf('%.2f',$model->projects_total_money / 100); ?></td>
    </tr>
    <tr height=50>
        <td class="td27">活期总额（口袋宝）（元）</td>
        <td  colspan=2><?php echo sprintf('%.2f',$model->kdb_total_money / 100); ?></td>
    </tr>
    <tr height=50>
        <td class="td27">用户总余额（元）</td>
        <td><?php echo sprintf('%.2f',$model->usable_money / 100); ?></td>
        <td class="tips2">用户资金流水所有用户可用余额之和</td>
    </tr>
    <tr><th class="partition" colspan="15">商户号总额&nbsp;&nbsp;&nbsp;&nbsp;<?php echo sprintf('%.2f',$model->merchant_number_money / 100); ?>元</th></tr>
    <tr height=50>
        <td class="td27">第三方支付余额（元）</td>
        <td  colspan=2><?php echo sprintf('%.2f',$model->third_party_alipay_balance / 100); ?></td>
    </tr>
    <tr height=50>
        <td class="td27">待收还款总额（元）</td>
        <td  colspan=2><?php echo sprintf('%.2f',$model->to_total_repayment / 100); ?></td>
    </tr>
    <tr height=50>
        <td class="td27">历史平台盈亏额（元）</td>
        <td  colspan=2><?php echo sprintf('%.2f',$model->historical_platform_profit / 100); ?></td>
    </tr>
    <tr><th class="partition" colspan="15">每日对账记录表（每日凌晨3点跑）</th></tr>
    <tr height=50>
        <td class="td27">日期</td>
        <td colspan=2><?php echo date('Y年m月d日',$model->date-86400); ?></td>
    </tr>
    <tr height=50>
        <td class="td27" rowspan=3>网站总额<br/><?php echo sprintf('%.2f',$model->site_total_money  / 100); ?>元</td>        
        <td width=150>余额 （元）</td>
        <td><?php echo sprintf('%.2f',$model->usable_money / 100); ?></td>
    </tr>
    <tr height=50>
        <td>活期 （元）</td>
        <td><?php echo sprintf('%.2f',$model->kdb_total_money / 100); ?></td>
    </tr>
    <tr height=50>
        <td>定期 （元）</td>
        <td><?php echo sprintf('%.2f',$model->projects_total_money / 100); ?></td>
    </tr>
    <tr height=50>
        <td class="td27" rowspan=2>实际资金账号</td>        
        <td width=80>商户号资金 （元）</td>
        <td><?php echo sprintf('%.2f',$model->merchant_number_money / 100); ?></td>
    </tr>
    <tr height=50>
        <td>待收益总额 （元）</td>
        <td><?php echo sprintf('%.2f',$model->to_total_repayment / 100); ?></td>
    </tr>
    <tr height=50>
        <td class="td27">盈利额 （元）</td>
        <td class="td27" colspan=2><?php echo sprintf('%.2f',$model->historical_platform_profit / 100); ?></td>
    </tr>
</table>