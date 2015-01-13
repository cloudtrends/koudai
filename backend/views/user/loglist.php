<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;
use backend\components\widgets\ActiveForm;
use common\models\UserLoginLog;

/**
 * @var backend\components\View $this
 */

$this->shownav('user', 'menu_user_login_log');
$this->showsubmenu('登陆日志');
?>

<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>
<?php $form = ActiveForm::begin(['id' => 'searchform','method'=>'get', 'options' => ['style' => 'margin-bottom:5px;']]); ?>
            用户ID：<input type="text" value="<?php echo Yii::$app->getRequest()->get('user_id', ''); ?>" name="user_id" class="txt" style="width:60px;">&nbsp;
            登录类型：<?php echo Html::dropDownList('type', Yii::$app->getRequest()->get('type', ''), UserLoginLog::$types, ['prompt' => '所有类型']); ?>&nbsp;
            登录时间：<input type="text" value="<?php echo Yii::$app->getRequest()->get('begintime', ''); ?>" name="begintime" onfocus="WdatePicker({startDate:'%y-%M-%d %H:%m:00',dateFmt:'yyyy-MM-dd HH:mm:00',alwaysUseStartDate:true,readOnly:true})"> 
        至<input type="text" value="<?php echo Yii::$app->getRequest()->get('endtime', ''); ?>"  name="endtime" onfocus="WdatePicker({startDcreated_atate:'%y-%M-%d %H:%m:00',dateFmt:'yyyy-MM-dd HH:mm:00',alwaysUseStartDate:true,readOnly:true})"> 
	<input type="submit" name="search_submit" value="过滤" class="btn">
<?php ActiveForm::end(); ?>

<form name="listform" method="post">
	<table class="tb tb2 fixpadding">
		<tr class="header">
			<th>ID</th>
			<th>用户ID</th>
			<th>登录类型</th>
			<th>来源信息</th>
			<th>登陆时间</th>
			<th>登陆ip</th>
		</tr>
		<?php foreach ($user_login_logs as $value): ?>
		<tr class="hover">
			<td class="td25"><?php echo $value->id; ?></td>
			<td><?php echo $value->user_id; ?></td>
			<td><?php echo UserLoginLog::$types[$value->type]; ?></td>
			<td>
				<?php 
					$source = unserialize($value->source);
					echo '终端版本:' . ($source['clientType'] == '' ? '未知' : $source['clientType']) .
						 '，app版本:' . ($source['appVersion'] == '' ? '未知' : $source['appVersion']) .
						 '，设备名称:' . ($source['deviceName'] == '' ? '未知' : $source['deviceName']) .
						 '，设备os版本:' . ($source['osVersion'] == '' ? '未知' : $source['osVersion']);
				?>
			</td>
			<td><?php echo date('Y-m-d H:i:s', $value->created_at); ?></td>
			<td class="td23"><?php echo $value->created_ip; ?></td>
		</tr>
		<?php endforeach; ?>
	</table>
            <?php if (empty($user_login_logs)): ?>
                <div class="no-result">暂无记录</div>   
            <?php endif; ?>
</form>

<?php echo LinkPager::widget(['pagination' => $pages]); ?>