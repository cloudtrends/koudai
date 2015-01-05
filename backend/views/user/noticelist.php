<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;
use backend\components\widgets\ActiveForm;
use common\models\NoticeSms;

/**
 * @var backend\components\View $this
 */

$this->shownav('user', 'menu_user_notice');
$this->showsubmenu('消息列表');
?>

<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>
<?php $form = ActiveForm::begin(['id' => 'searchform','method'=>'get', 'options' => ['style' => 'margin-bottom:5px;']]); ?>
            用户ID：<input type="text" value="<?php echo Yii::$app->getRequest()->get('user_id', ''); ?>" name="user_id" class="txt" style="width:60px;">&nbsp;
            类型：<?php echo Html::dropDownList('type', Yii::$app->getRequest()->get('type', ''), NoticeSms::$status, ['prompt' => '所有类型']); ?>&nbsp;
            创建时间：<input type="text" value="<?php echo Yii::$app->getRequest()->get('begintime', ''); ?>" name="begintime" onfocus="WdatePicker({startDate:'%y-%M-%d %H:%m:00',dateFmt:'yyyy-MM-dd HH:mm:00',alwaysUseStartDate:true,readOnly:true})">
        至<input type="text" value="<?php echo Yii::$app->getRequest()->get('endtime', ''); ?>"  name="endtime" onfocus="WdatePicker({startDcreated_atate:'%y-%M-%d %H:%m:00',dateFmt:'yyyy-MM-dd HH:mm:00',alwaysUseStartDate:true,readOnly:true})"> 
	<input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>

<form name="listform" method="post">
	<table class="tb tb2 fixpadding">
		<tr class="header">
			<th>ID</th>
			<th>用户ID</th>
			<th>消息类型</th>
			<th>发送状态</th>
			<th>读取状态</th>
			<th>消息内容</th>
            <th>创建时间</th>
		</tr>
		<?php foreach ($NoticeData as $value): ?>
		<tr class="hover">
			<td class="td25"><?php echo $value->id; ?></td>
			<td><?php echo $value->user_id; ?></td>
			<td><?php echo NoticeSms::$status[$value->type]; ?></td>
			<td><?php echo NoticeSms::$send_status[$value->status]; ?></td>
            <td><?php echo NoticeSms::$read_status[$value->is_read]; ?></td>
            <td><?php echo $value->remark; ?></td>
			<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
            <?php if (empty($NoticeData)): ?>
                <div class="no-result">暂无记录</div>   
            <?php endif; ?>
</form>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>