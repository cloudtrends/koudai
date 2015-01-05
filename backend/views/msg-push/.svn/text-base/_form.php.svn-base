<?php
use backend\components\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(['id' => 'msg-task-form']); ?>
<title>消息推送管理</title>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>
<br>

<table>

    <tr style="<?php
        if ( $action == "add" ){
            echo "display:none";
        }
    ?>">
        <td class="msg_push_td_width_left">
            任务ID：
        </td>
        <td class="msg_push_td_width_right">
            <?php echo $form->field($model, 'task_id')->input("text", ["readonly" => "true"]); ?>
        </td>
    </tr>

	<tr>
		<td class="msg_push_td_width_left">
			消息推送方式：
		</td>
		<td class="msg_push_td_width_right">
			<?php echo $form->field($model, 'task_type')->listBox($taskType, ['size'=> 1]); ?>
		</td>
	</tr>

	<!-- 推送内容 -->
	<tr>
		<td class="msg_push_td_width_left">
			推送内容：<br>
                        (内容不得超过800英文字符或400非英文字符)
		</td>
		<td class="msg_push_td_width_right">
			<?php echo $form->field($model, 'msg_content')->textarea(); ?>
		</td>
	</tr>

	<!-- 短信推送 -->
	<tr class="textMsg"  >
		<td class="msg_push_td_width_left">
			收信列表：<br>
                        (单次发送不超过100个)
		</td>
		<td class="msg_push_td_width_right">
			<?php echo $form->field($model, "receiver_list")->textarea(); ?>
		</td>
	</tr>
        
        
	<tr >
		<td class="msg_push_td_width_left">
			预计发送时间：<br>
			(留空表示立即发送)
		</td>
		<td class="msg_push_td_width_right">
            <?php echo $form->field($model, 'expect_time')->input("text", ['onFocus' => "WdatePicker({startDate:'%y-%M-%d %H:%m:00',dateFmt:'yyyy-MM-dd HH:mm:00',alwaysUseStartDate:true,readOnly:true})"]); ?>
        </td>
	</tr>

	<tr >
		<td class="msg_push_td_width_left">
			操作：
		</td>
		<td class="msg_push_td_width_right">
			<button type="submit" onclick="">保存任务</button>
			<button type="button" onclick="clearInput()">清除</button>
		</td>
	</tr>
	<tr >
		<td class="msg_push_td_width_left">
			&nbsp;
		</td>
		<td class="msg_push_td_width_right">
			<div id="operator_results"></div>
		</td>
	</tr>
</table>
<?php ActiveForm::end(); ?>


