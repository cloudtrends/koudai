<?php

/**
 * @var backend\components\View $this
 */
$this->shownav('financial', 'menu_account_withdraw');
$this->showsubmenu('付款查询');

?>

<table class="tb tb2 fixpadding">
	<tr>
        <td class="td24">商户端订单号：</td>
        <td colspan="2"><?php echo $result['order_id']; ?></td>
    </tr>
	<tr>
        <td class="td24">订单日期：</td>
        <td colspan="2"><?php echo $result['mer_date']; ?></td>
    </tr>
	<tr>
        <td class="td24">ret_code：</td>
        <td colspan="2"><?php echo $result['ret_code']; ?></td>
    </tr>
    <tr>
        <td class="td24">ret_msg：</td>
        <td colspan="2"><?php echo $result['ret_msg']; ?></td>
    </tr>
    <tr>
        <td class="td24">交易状态：</td>
        <td width="140"><?php echo $result['trade_state']; ?></td>
        <td>
        	1-支付中 3-失败 4-成功<br/>
        	11：待确认 12：已冻结,待财务审核 13: 待解冻,交易失败 14：财务已审核，待财务付款<br/>
        	15: 财务审核失败，交易失败 16：受理成功，交易处理中 17: 交易失败退单中 18：交易失败退单成功
        </td>
    </tr>
    <tr>
        <td class="td24">支付平台交易号：</td>
        <td colspan="2"><?php echo $result['trade_no']; ?></td>
    </tr>
    <tr>
        <td class="td24">付款日期：</td>
        <td colspan="2"><?php echo $result['transfer_date']; ?></td>
    </tr>
    <tr>
        <td class="td24">付款对账日期：</td>
        <td colspan="2"><?php echo $result['transfer_settle_date']; ?></td>
    </tr>
    <tr>
        <td class="td24">提现金额：</td>
        <td colspan="2"><?php echo $result['amount'] / 100; ?></td>
    </tr>
    <tr>
        <td class="td24">手续费：</td>
        <td colspan="2"><?php echo $result['fee'] / 100; ?></td>
    </tr>
    <tr>
        <td class="td24">备注：</td>
        <td colspan="2"><?php echo $result['purpose']; ?></td>
    </tr>
</table>