<?php

use yii\widgets\LinkPager;
use yii\helpers\Html;
use backend\components\widgets\ActiveForm;
use common\models\UserDailyProfits;

/**
 * @var backend\components\View $this
 */
$this->shownav('financial', 'menu_account_profits');
$this->showsubmenu('用户收益日志');

?>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>
<?php $form = ActiveForm::begin(['id' => 'searchform', 'method' => "get", 'options' => ['style' => 'margin-bottom:5px;']]); ?>
	
	用户ID：<input type="text" value="<?php echo Yii::$app->getRequest()->get('user_id', ''); ?>" name="user_id" class="txt" style="width:120px;">&nbsp;       
	用户名：<input type="text" value="<?php echo Yii::$app->getRequest()->get('username', ''); ?>" name="username" class="txt" style="width:120px;">&nbsp;
	姓名：<input type="text" value="<?php echo Yii::$app->getRequest()->get('realname', ''); ?>" name="realname" class="txt" style="width:120px;">&nbsp;
        收益日期：<input type="text" value="<?php echo Yii::$app->getRequest()->get('begintime', ''); ?>" name="begintime" onfocus="WdatePicker({startDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true,readOnly:true})"> 
        至<input type="text" value="<?php echo Yii::$app->getRequest()->get('endtime', ''); ?>"  name="endtime" onfocus="WdatePicker({startDcreated_atate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true,readOnly:true})"> 
        <input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>

<table class="tb tb2 fixpadding">
	<tr class="header">
		<th>用户ID</th>
		<th>用户名</th>
		<th>姓名</th>
		<th>收益日期</th>
		<th>今日结算金额</th>
		<th>收益金额</th>
		<th>累计收益</th>
		<th>项目类型</th>
		<th>项目ID</th>
		<th>项目名称</th>
		<th>项目投资ID</th>
		<th>创建时间</th>
	</tr>
	<?php foreach ($profitses as $value): ?>
	<tr class="hover">
		<td class="td25"><?php echo $value->user_id; ?></td>
		<td><?php echo $value->user->username; ?></td>
		<td><?php echo $value->user->realname; ?></td>
		<td><?php echo $value->date; ?></td>
		<td><?php echo sprintf('%.2f', $value->today_settle_money / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->lastday_profits / 100); ?></td>
		<td><?php echo sprintf('%.2f', $value->total_profits / 100); ?></td>
		<td><?php echo $value->project_type == UserDailyProfits::PROJECT_TYPE_PROJ ? '普通项目' : '口袋宝'; ?></td>
		<td><?php echo $value->project_type == UserDailyProfits::PROJECT_TYPE_PROJ ? $value->project_id : '-'; ?></td>
		<td><?php echo $value->project_type == UserDailyProfits::PROJECT_TYPE_PROJ ? $value->project_name : '-'; ?></td>
		<td><?php echo $value->project_type == UserDailyProfits::PROJECT_TYPE_PROJ ? $value->invest_id : '-'; ?></td>
		<td width="150"><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
	</tr>
	<?php endforeach; ?>
</table>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>