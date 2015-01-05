<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\helpers\Html;

?>
<title>消息推送管理</title>
<script language="javascript" type="text/javascript" src="<?php echo $this->baseUrl ?>/js/My97DatePicker/WdatePicker.js"></script>
<table>
	<tr>
		<td class="msg_push_td_width_left">
			消息推送方式：
		</td>
		<td class="msg_push_td_width_right">
			<select id="msgPushType" onchange="selectMsgPushType()" class="msg_push_select_width">
				<option value="textMsg">短信推送</option>
				<option value="appMsg">手机推送</option>
			</select>
		</td>
	</tr>

	<!-- 推送内容 -->
	<tr>
		<td class="msg_push_td_width_left">
			推送内容：
		</td>
		<td class="msg_push_td_width_right">
			<textarea rows="3" cols="20" class="msg_push_textarea" id="msg_push_textarea" ></textarea>
		</td>
	</tr>

	<!-- 短信推送 -->
	<tr class="textMsg"  >
		<td class="msg_push_td_width_left">
			推送手机号：
		</td>
		<td class="msg_push_td_width_right">
			<input type="text" value="" class="msg_push_input_text_width" id="mobile" >
		</td>
	</tr>

	<!-- App推送 -->
	<tr class="appMsg" >
		<td class="msg_push_td_width_left">
			推送用户：
		</td>
		<td class="msg_push_td_width_right">
			<select id="appPushUserType" onchange="selectAppPushUser()" class="msg_push_select_width">
				<option value="toAll">所有用户</option>
				<option value="toIOS">IOS用户</option>
				<option value="toAndroid">Android用户</option>
				<option value="toUsers">指定用户</option>
			</select>
		</td>
	</tr>

	<tr class="appMsg appMsgUserList" >
		<td class="msg_push_td_width_left">
            推送设备ID:
		</td>
		<td class="msg_push_td_width_right">
			<input type="text" value="" class="msg_push_input_text_width" id="device" />
		</td>
	</tr>

	<tr >
		<td class="msg_push_td_width_left">
			预计发送时间：<br>
            (留空表示立即发送)
		</td>
		<td class="msg_push_td_width_right">
            <input type="text" id="expectTime" class="msg_push_input_text_width" onFocus="WdatePicker({startDate:'%y-%M-%d %H:%m:00',dateFmt:'yyyy-MM-dd HH:mm:00',alwaysUseStartDate:true,readOnly:true})" />
		</td>
	</tr>

    <tr >
        <td class="msg_push_td_width_left">
            操作：
        </td>
        <td class="msg_push_td_width_right">
            <button type="button" onclick="createMsgPushTask()">创建任务</button>
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

<script language="JavaScript">

    var mobileList; // 推送手机列表
    var mobileStr; // 推送手机列表
    var deviceList; // 推送设备列表
    var deviceStr; // 推送设备列表
    var msgPushType = $("#msgPushType").val(); // 消息推送累心
    var appPushUserType; // app推送用户类型
    var msgContent; // 发送消息内容
    var expectTime; // 预计发送时间

	// 初始隐藏部分控件
	$(".appMsg").hide();
	$(".textMsg").show();
	$(".appMsgUserList").hide();

	// 推送类型选择
	function selectMsgPushType()
	{
        msgPushType = $("#msgPushType").val();
		if ( msgPushType == "textMsg")
		{
			$(".appMsg").hide();
			$(".textMsg").show();
		}
		else
		{
			$(".appMsg").show();
			$(".textMsg").hide();
			selectAppPushUser();
		}
	}


	// 推送类型选择
	function selectAppPushUser()
	{
        appPushUserType = $("#appPushUserType").val();
		if ( appPushUserType == "toUsers")
		{
			$(".appMsgUserList").show();
		}
		else
		{
			$(".appMsgUserList").hide();
		}
	}

    // 创建消息推送任务
    function createMsgPushTask()
    {
    	// 检查输入的推送内容
        if ( false === _checkMsgContent())
        {
            return false;
        }

        // 检查短信推送目标
        if( false === _checkTextMsgPush() )
        {
            return false;
        }

        // 检查App推送目标
		if( false === _checkAppPush() )
        {
            return false;
        }

        $("#operator_results").text("");
        return _createMsgPushTask();
    }

    function _createMsgPushTask()
    {
        var url = "<?php echo $this->baseUrl ?>/index.php?r=msg-push%2Fcreate-task";
        var data = {
            "mobileList" : mobileList,
            "deviceList" : deviceList,
            "msgPushType" : msgPushType,
            "appPushUserType" : appPushUserType,
            "msgContent" : msgContent,
            "expectTime" : expectTime
        };

        $.post(url, data, function(result){
            $("operator_results").html(result);
        });
    }

    // -------------------------- 以下都是检查函数 ------------------------
    // 清除输入数据
    function clearInput()
    {
        $("#operator_results").text("");
        $(".msg_push_textarea").val("");
        $("#mobile").val("");
        $("#device").val("");
    }

    // 检查推送的内容
    function _checkMsgContent()
    {
        msgContent = $(".msg_push_textarea").val();
        if( msgContent.length == 0 )
        {
            $("#operator_results").text("请输入\"推送内容\"");
            $("#msg_push_textarea").focus();
            return false;
        }

        return true;
    }

    // 检查短信推送相关输入
    function _checkTextMsgPush()
    {
        if ($("#msgPushType").val() != "textMsg" )
            return true;

        // 检查手机号
        mobileStr = $("#mobile").val();
        if (mobileStr.length == 0)
        {
            $("#operator_results").text("请输入\"推送用户手机\"");
            $("#mobile").focus();
            return false;
        }

        mobileList = mobileStr.split(";");
        $.each(mobileList, function(idx, value) {
            // alert(idx+' '+value);
        });

        return true;
    }

    // 检查App推送相关输入
    function _checkAppPush()
    {
        if ($("#msgPushType").val() != "appMsg" )
            return true;

        // 检查设备ID
        appPushUserType = $("#appPushUserType").val();
        if ( appPushUserType == "toUsers")
        {
            deviceStr = $("#device").val();
            if (deviceStr.length == 0)
            {
                $("#operator_results").text("请输入\"推送设备ID\"");
                $("#device").focus();
                return false;
            }

            deviceList = deviceStr.split(";");
            $.each(deviceList, function(idx, value) {
                // alert(idx+' '+value);
            });
        }
	    return true;
    }

    function _checkExpectTime()
    {
        expectTime = $("#expectTime").val();
        if (expectTime.length == 0)
        {
            $("#operator_results").text("请输入\"预期发送时间\"");
            $("#device").focus();
            return false;
        }
	    return true;
    }

</script>