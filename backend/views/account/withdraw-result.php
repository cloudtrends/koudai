<?php

use backend\components\widgets\ActiveForm;

/**
 * @var backend\components\View $this
 */
$this->shownav('financial', 'menu_account_withdraw');
$this->showsubmenu('付款查询');

?>

<?php $form = ActiveForm::begin(['id' => 'activity-form']); ?>
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
            <td class="td24">商户查询返回码：</td>
            <td colspan="2"><?php echo $result['ret_code']; ?></td>
        </tr>
        <tr>
            <td class="td24">商户查询结果：</td>
            <td colspan="2"><?php echo $result['ret_msg']; ?></td>
        </tr>
        <tr>
            <td class="td24">交易状态：</td>
            <td colspan="2"><?php echo $result['trade_state']; ?></td>
        </tr>
        <tr>
            <td class="td24">交易状态提示：</td>
            <td colspan="2">
                1-支付中 3-失败 4-成功<br/>
                11-待确认 <br/>
                12-已冻结,待财务审核 <br/>
                13-待解冻,交易失败 <br/>
                14-财务已审核,待财务付款 <br/>
                15-财务审核失败,交易失败 <br/>
                16-受理成功,交易处理中 <br/>
                17-交易失败退单中 <br/>
                18-交易失败退单成功 <br/>
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
            <td class="td24">提现金额：</td>
            <td colspan="2"><?php echo $result['money_order']; ?></td>
        </tr>
        <tr>
            <td class="td24">手续费：</td>
            <td colspan="2">1.00</td>
        </tr>
        <tr>
            <td class="td24">备注：</td>
            <td colspan="2"><?php echo $result['purpose']; ?></td>
        </tr>
        <?php if ($result['trade_state'] == '4'): ?>
            <tr>
                <td colspan="15">
                    <input type="submit" value="手动置为成功" name="submit_btn" class="btn"> 主要用于第三方支付异步通知出现问题时使用，请确认提现已到账才做此操作！
                </td>
            </tr>
        <?php endif; ?>
    </table>
<?php ActiveForm::end(); ?>