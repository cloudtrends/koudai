<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use common\models\UserWithdraw;
use common\models\UserAccount;

/*
array (
    'id' => '50',
    'user_id' => '11',
    'money' => '1',
    'status' => '1',
    'review_username' => '',
    'review_time' => '0',
    'created_at' => '1418371768',
    'updated_at' => '1418371768',
)

array (
  'amount' => '1',
  'mer_date' => '20141212',
  'mer_id' => '25116',
  'order_id' => '201412121001',
  'ret_code' => '00131040',
  'ret_msg' => '余额不足，详情咨询4006125880',
  'trade_no' => '1412121758350185',
  'trade_state' => '3',
  'version' => '4.0',
  'sign' => 'fZgcReDSYGoXkmFUV+3HS9HKZ+k0ZmIpJw1YYWwDFAq0in8VEqyYVogBcQ/jazht772BtJxH7cWB3AgCWkRDa3kc7Zf/rB9oTzZKJCWESSA8xxAc7AirR06jZ6G1sZD/wVGnZatg8YZ+dIZfJGs2T8IFY/OL3Hlz5MwxxpEI6QM=',
  'sign_type' => 'RSA',
)
**/

$this->shownav('financial', 'menu_account_withdraw');
$this->showsubmenu('提现详情');

$result = $withdraw['result'];
$notify_result = $withdraw['notify_result'];
?>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>
<table class="tb tb2 fixpadding">
    <?php $form = ActiveForm::begin(['id' => 'review-form']); ?>

    <tr><th class="partition" colspan="15">基本信息
        </th></tr>
    <tr>
        <td class="td24">用户名：</td>
        <td width="300"><?php echo $withdraw['user_name'] . ' （' . $withdraw['user_realname'] . '）'; ?></td>
        <td class="td24">类型：</td>
        <td><?php echo $withdraw['type'] ?></td>
    </tr>
    <tr>
        <td class="td24">申请额度：</td>
        <td><?php echo $withdraw['money'] ?></td>
        <td class="td24">提现状态：</td>
        <td><?php echo $withdraw['status_desc'] ?></td>
    </tr>
    <tr>
        <td class="td24">申请时间：</td>
        <td><?php echo $withdraw['created_at'] ?></td>
        <td class="td24">记录ID：</td>
        <td><?php echo $withdraw['id'] ?></td>
    </tr>
    <!-- 暂定为审核过了就不能再审核了 -->
    <?php if ($withdraw['review_result'] == UserWithdraw::REVIEW_STATUS_NO): ?>
    <tr>
        <td class="td24">操作</td>
        <td><?php echo Html::radioList('operation', 1, [
                'approve' => "审核通过",
                'reject' => "驳回"
            ]); ?></td>
    </tr>
    <tr>
        <td class="td24">审核备注：</td>
        <td><?php echo Html::textarea('remark', '', ['style' => 'width:300px;']); ?></td>
    </tr>
    <tr>
        <td colspan="15">
            <input type="submit" value="提交" name="submit_btn" class="btn">
        </td>
    </tr>
    <?php endif;?>
    <?php ActiveForm::end(); ?>
    
    <?php if (!empty($result)): ?>
    <tr><th class="partition" colspan="15">申请提现返回结果</th></tr>
        <tr>
            <td class="td24">提现额度：</td>
            <td></td>
            <td class="td24">订单日期：</td>
            <td></td>
        </tr><tr>
            <td class="td24">提现订单ID：</td>
            <td></td>
            <td class="td24">联动交易ID：</td>
            <td></td>
        </tr><tr>
            <td class="td24">返回码：</td>
            <td></td>
            <td class="td24">返回消息：</td>
            <td></td>
        </tr><tr>
            <td class="td24">交易状态：</td>
            <td></td>
            <td class="td24"></td>
            <td></td>
    </tr>
    <?php endif;?>

    <?php if (!empty($notify_result)): ?>
    <tr><th class="partition" colspan="15">异步通知结果</th></tr>
    <tr>
        <td class="td24">提现额度：</td>
        <td><?php echo $notify_result['amount'] / 100; ?></td>
        <td class="td24">订单日期：</td>
        <td><?php echo $notify_result['mer_date']; ?></td>
    </tr><tr>
        <td class="td24">提现订单ID：</td>
        <td><?php echo $notify_result['order_id']; ?></td>
        <td class="td24">联动交易ID：</td>
        <td><?php echo $notify_result['trade_no']; ?></td>
    </tr><tr>
        <td class="td24">返回码：</td>
        <td><?php echo $notify_result['ret_code']; ?></td>
        <td class="td24">返回消息：</td>
        <td><?php echo $notify_result['ret_msg']; ?></td>
    </tr><tr>
        <td class="td24">交易状态：</td>
        <td><?php echo $notify_result['trade_state']; ?></td>
        <td class="td24">通知时间：</td>
        <td><?php echo date('Y-m-d H:i:s', $notify_result['notify_time']); ?></td>
    </tr>
    <?php endif;?>
</table>

<table class="tb tb2 fixpadding">
    <tr>
    	<th class="partition" colspan="15">用户资金流水</th>
    </tr>
    <tr>
    	<th colspan="15">
            <?php $form = ActiveForm::begin(['id' => 'searchform', 'method' => "get", 'options' => ['style' => 'margin-bottom:5px;']]); ?>
            操作类型：<?php echo Html::dropDownList('type', Yii::$app->getRequest()->get('type', ''), UserAccount::$tradeTypes, ['prompt' => '所有类型']); ?>&nbsp;
            按时间段：<input type="text" value="<?php echo Yii::$app->getRequest()->get('begintime', ''); ?>" name="begintime" onfocus="WdatePicker({startDate:'%y-%M-%d %H:%m:00',dateFmt:'yyyy-MM-dd HH:mm:00',alwaysUseStartDate:true,readOnly:true})"> 
            至<input type="text" value="<?php echo Yii::$app->getRequest()->get('endtime', ''); ?>"  name="endtime" onfocus="WdatePicker({startDcreated_atate:'%y-%M-%d %H:%m:00',dateFmt:'yyyy-MM-dd HH:mm:00',alwaysUseStartDate:true,readOnly:true})"> 
            <input type="submit" name="search_submit" value="过滤" class="btn">
            <?php ActiveForm::end(); ?>
        </th>
    </tr>
    <?php if (!empty($user_account_log_list)): ?>
        <tr class="header">
            <th>用户ID</th>
            <th>用户名</th>
            <th>操作类型</th>
            <th>操作金额</th>
            <th>总金额</th>
            <th>可用余额</th>
            <th>提现中金额</th>
            <th>投资中金额</th>
            <th>待收本金</th>
            <th>待收收益</th>
            <th>口袋宝总金额</th>
            <th width="150">操作时间</th>
        </tr>
        <?php foreach ($user_account_log_list as $value): ?>
        <tr class="hover">
            <td><?php echo $value['user_id']; ?></td>
            <td><?php echo $value['user']['username']; ?></td>  
            <td><?php echo $value['type']; ?></td>
            <td><?php echo sprintf('%.2f', $value['operate_money'] / 100); ?></td>
            <td><?php echo sprintf('%.2f', $value['total_money'] / 100); ?></td>
            <td><?php echo sprintf('%.2f', $value['usable_money'] / 100); ?></td>
            <td><?php echo sprintf('%.2f', $value['withdrawing_money'] / 100); ?></td>
            <td><?php echo sprintf('%.2f', $value['investing_money'] / 100); ?></td>
            <td><?php echo sprintf('%.2f', $value['duein_capital'] / 100); ?></td>
            <td><?php echo sprintf('%.2f', $value['duein_profits'] / 100); ?></td>
            <td><?php echo sprintf('%.2f', $value['kdb_total_money'] / 100); ?></td>
            <td><?php echo date('Y-m-d H:i:s', $value['created_at']); ?></td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
    <tr><th colspan="15"><div class="no-result">暂无记录</div></th></tr>
    <?php endif;?>
</table>
<?php if (!empty($user_account_log_list)): ?>
<?php echo LinkPager::widget(['pagination' => $pages]); ?>
<?php endif; ?>
