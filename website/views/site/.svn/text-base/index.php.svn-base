<?php
use yii\helpers\Url;
use website\components\ApiUrl;
?>
<head><title><?php echo $title; ?></title></head>
<div id="index_wrap">
	<div id="scroll">
		<div class="content">
		<?php if( empty($this->userName) ): ?>
			<div class="scroll_form alignright">
				<div id="title">
					<span class="font3 color8 first_child">快速注册</span>
					<span class="font1 color5">已有账号？</span>
					<span><a href="<?php echo Url::toRoute(['site/login']); ?>" class="font1 color7">登录</a></span>
				</div>
				<form method="post" action="" class="clear_inline_block">
					<!-- <span id="notification" class="font1 color7"></span><br> -->
					<span class="font1 color5">手机号</span><input class="scroll_input bg_input" type="text" maxlength="11" name="phone" id="phone"/><br>
					<span class="font1 color5">验证码</span><input class="scroll_input scroll_input_width bg_input" type="password" name="msgcode" id="msgcode"/>
					<a id="get_reg_code" class="scroll_getcode bg_button aligncenter font11 color4">点击获取</a><br>
					<span class="font1 color5">创建密码</span><input class="scroll_input bg_input" type="password" name="password" id="password"/><br>
					<span class="font1 color5">确认密码</span><input class="scroll_input bg_input" type="password" name="re_password" id="re_password"/><br>
					<a id="red_btn" class="scroll_button bg_button aligncenter font4 color4">立即注册</a>
				</form>
			</div>
		<?php else: ?>
			<!-- 设计中 -->
		<?php endif; ?>
		</div>
	</div>
	<div id="kdb">
		<div class="content">
			<div class="kdb_video">
				<!-- 
				当前，video 元素支持三种视频格式： 
				格式 IE Firefox Opera Chrome Safari 
				Ogg No 3.5+ 10.5+ 5.0+ No 
				MPEG 4 9.0+ No No 5.0+ 3.0+ 
				WebM No 4.0+ 10.6+ 6.0+ No
				-->
				<video width="305" height="230" controls>  
					<source src="<?php echo $this->absBaseUrl; ?>/video/12-29kdb.mp4" type="video/mp4"/>
					<embed SRC="<?php echo $this->absBaseUrl; ?>/video/12-29kdb.avi" type="audio/x-pn-realaudio-plugin" autostart=false loop=false width=305 height=230></embed>
				</video>
			</div><br>
			<a href="<?php echo Url::toRoute(['site/list']); ?>"><div class="kdb_button bg_button aligncenter font4 color4">马上投资</div></a>
		</div>
	</div>
	<div id="projectlist">
		<div class="content">
			<span class="font2 color2">定期 · 投资项目 </span>
			<div id="p2p_list"></div>
			<a href="<?php echo Url::toRoute(['site/list']); ?>" class="f_right clear font1 color10">查看全部项目</a>
		</div>
		<div class="clear"></div>
	</div>
	<div id="investlist">
		<div class="content">
			<div id="invest_log"></div>
		</div>
	</div>
</div>
<script type="text/javascript">
	var mobile;
	$("#index_wrap #phone").change(function(){
		//获取选择的值
		mobile = $(this).val();
	});

	$("#index_wrap #get_reg_code").click(function(){
		var $_this = $(this);
		var url = "<?php echo ApiUrl::toRoute('user/reg-get-code');?>";
		if( !$("#index_wrap #get_reg_code").hasClass("bg_button1") ){
			$.ajax({
				url : url,
				type : 'POST',
				jsonp: "callback",
				jsonpCallback:"flightHandler",
				data : {
					phone : mobile
				},
				success:function(data){
					if(data.code == 0){
						$('#notification').html("验证码已发送到" + mobile);
						var total = 60;
						$_this.html(total+"秒重新获取");
						$_this.addClass("bg_button1");
						var interId = setInterval(function () {
							total--;
							$_this.html(total + "秒重新获取");
							if (total <= 0) {
								clearInterval(interId);
								$_this.html("重新获取");
								$_this.removeClass("bg_button1");
							}
						} , 1000);
					}else{
						$('#notification').html(data.message);
					}
				}
			});
		}
	});
	

	$("#index_wrap #red_btn").click(function(){
		var url = "<?php echo ApiUrl::toRoute('user/register');?>";
		var btn_id = '#red_btn';
		var btn_text = "立即注册";
		var a_js_var = "javascript:regsubmit();";
		if (check_tel() && check_yzm() && check_pwd() && check_re_pwd()){
			$.ajax({
				url : url,
				type: 'POST',
				jsonp: "callback",
				jsonpCallback:"flightHandler",
				data : {
							phone : mobile,
							password : $('#password').val(),
							code : $('#msgcode').val(),
						},
				beforeSend : function(){
					$(btn_id).attr('href','#');
					$(btn_id).html("载入中…");
				},
				complete : function(){
					$(btn_id).attr('href', a_js_var);
					$(btn_id).html(btn_text);  
				},
				success:function(data){
					if(data.code == 0){
						window.location.href = "<?php echo Url::toRoute('site/reg-success');?>";
					}else{
						$('#notification').html(data.message);
					}
				}
			});
		}
	});

	function check_yzm(){
		if ( '' != $.trim($('#msgcode').val()) ){
			$('#notification').html('');
		}else{
			$('#notification').html('验证码不能为空');
			return false;
		}
		return true;
	}

	function check_pwd(){
		if ($('#password').val().length >= 6 && $('#password').val().length <=16){
			$('#notification').html('');
		}else{
			$('#notification').html('密码为6-16位字符或数字');
			return false;
		}
		return true;
	}

	function check_re_pwd(){
		if( '' == $.trim($('#re_password').val()) ){
			$('#notification').html('请确认您的登录密码');
			return false;
		}else if ($('#re_password').val() != $('#password').val()){
			$('#notification').html('两次密码不一致');
			return false;
		}else{
			$('#notification').html('');
		}	
		return true;
	}

	function check_tel(){
		var mobile_reg = /^[1]\d{10}$/;
		if( mobile_reg.test($('#phone').val()) ){
			$('#notification').html('');
		}else if ( '' == $.trim($('#phone').val()) ){
			$('#notification').html('手机号不能为空');
			return false;
		}else{
			$('#notification').html('手机号不合法');
			return false;
		}
		return true;
	}
</script>
<script>
	var pageSize1 = 6;
	var url1 = "<?php echo ApiUrl::toRoute('project/p2p-list'); ?>?page=1&pageSize="+pageSize1;
	var html ='';
	$.ajax({
		url : url1,
		type: 'POST',
		dataType: 'jsonp',
		jsonp: 'callback',
		jsonpCallback:"flightHandler",
		success:function(data){
			if( data.code == 0){
				$.each(data.projects,function(index,value){
					if(value.is_novice == 1){
						html +='<div class="p2p_details" style="background:#FFFFFF url(<?php echo $this->absBaseUrl; ?>/image/site/xinshou.png) no-repeat right top;"><a href="<?php echo Url::toRoute(['site/detail']); ?>?id='+value.id+'">';
					}else{
						html +='<div class="p2p_details"><a href="<?php echo Url::toRoute(['site/detail']); ?>?id='+value.id+'">';
					}
					html +='<div class="details_content">';
					html +='<p class="font2 color2 first_child">'+value.name+'</p>';
					html +='<p class="font1 color2">期限：'+value.period+(value.is_day==1 ? '天':'月')+'&nbsp;&nbsp;|&nbsp;&nbsp;起投资金：'+value.min_invest_money+'元</p>';
					html +='<div class="l_apr">';
					html +='<p class="font1 color6">预期年化<br><br></p>';
					html +='<p><span class="font5 color10">'+value.apr+'</span><span class="font1 color10">%</span></p>';
					html +='</div><div id="progress"> <div class="progress_in" style="width:'+ value.success_percent +'%;"></div></div><div class="summary">';
					html +='<span class="font1 color6">融资进度 </span>';
					html +='<span class="font1 color10">'+ value.success_percent +'%</span>';
					html +='<span class="font1 color6 f_right">'+ value.success_number+'人已投</span>';
					html +='</div><div class="font1 color6" style="overflow:hidden;height:65px;">'+(value.summary.length >=65 ? value.summary.substring(0,65)+'...' : value.summary)+'</div></div>';
					if(value.success_percent == 100){
						html +='<div class="bg_button3 aligncenter font4 color4">已满 · 查看详情</div></a></div>';
					}else{
						html +='<div class="bg_button2 aligncenter font4 color4">马上投资</div></a></div>';
					}
				});
				$('#p2p_list').html(html);
				// 兼容IE7-8，代替nth-child(3n-1)
				for(var i = 0 ;i < $("#p2p_list > .p2p_details").length ;i++){
					if( (i+2) % 3 == 0 ){
						$("#p2p_list > .p2p_details:eq("+i+")").css("margin","auto 34px");
					}
				}
			}
		}
	});

	var pageSize2 = 5;
	var url2 = "<?php echo ApiUrl::toRoute('project/invest-log'); ?>?page=1&pageSize="+pageSize2;
	var html2 ='';
	$.ajax({
		url : url2,
		type: 'POST',
		dataType: 'jsonp',
		jsonp: 'callback',
		jsonpCallback:"flightHandler",
		success:function(data){
			if( data.code == 0){
				html2 +='<table class="invest_details_table" cellpadding=0 cellspacing=0>';
				html2 +='<tr style="border-bottom: 1px solid #EAEAEA;">';
				html2 +='<td style="width:100px; height: 50px;">投资人</td>';
				html2 +='<td style="text-align:center;">投资项目</td>';
				html2 +='<td>投资金额</td>';
				html2 +='<td style="width:90px;">投资日期</td>';
				html2 +='</tr>';
				$.each(data.invests,function(index,value){
					html2 +='<tr class="invest_details_td">';
					html2 +='<td style="height:45px;">'+value.username+'</td>';
					html2 +='<td style="text-align:center;">'+value.project_name+'</td>';
					html2 +='<td>'+value.invest_money+'元</td>';
					html2 +='<td>'+StrToTime(value.created_at)+'</td>';
					html2 +='</tr>';
				});
				html2 +='</table>';
				$('#invest_log').html(html2);
			}
		}
	});

	function StrToTime(nS) {
		return (new Date(parseInt(nS) * 1000).getFullYear())+'.'
		+( ((new Date(parseInt(nS) * 1000).getMonth())+1) <10 
			? '0'+((new Date(parseInt(nS) * 1000).getMonth())+1) 
			: ((new Date(parseInt(nS) * 1000).getMonth())+1) )+'.'
		+( (new Date(parseInt(nS) * 1000).getDate()) <10 
			? '0'+(new Date(parseInt(nS) * 1000).getDate()) 
			: (new Date(parseInt(nS) * 1000).getDate()) );
	}
</script>
