<?php
use yii\helpers\Url;
use website\components\ApiUrl;
?>
<head><title><?php echo $title; ?></title></head>
<body>
	<div id="register_wrap">
		<div class="content">
			<div class="r_left">
				<ul>
					<li id="left"><img src="<?php echo $this->absBaseUrl; ?>/image/site/page_reduce.jpg"/></li>
					<li><img id="obj" src ="<?php echo $this->absBaseUrl; ?>/image/site/gsy.jpg"/></li>
					<li id="right"><img src="<?php echo $this->absBaseUrl; ?>/image/site/page_add.jpg"/></li>
				</ul>
			</div>

			<div class="aligncenter r_right">
				<span class="font3 color2">免费注册口袋理财</span>
				<form method="post" action="" class="clear_inline_block  alignright">
					<span id="notification" class="font1 color7"></span><br>
					<div class="clear"></div>
					<span class="font1 color5">手机号</span>
					<input class="_input bg_input first_child" type="text" maxlength="11" name="phone" id="phone" placeholder="请输入您的手机号码"/><br>
					<span class="font1 color5">验证码</span>
					<input class="_input _w_input bg_input" type="password" name="msgcode" id="msgcode" placeholder="请输入手机验证码"/>
					<a id="get_reg_code" class="_w_input1 bg_button aligncenter font111 color4">点击获取</a><br>
					<span class="font1 color5">创建密码</span>
					<input class="_input bg_input" type="password" name="password" id="password" placeholder="6-16位数字和字母组成"/><br>
					<span class="font1 color5">确认密码</span>
					<input class="_input bg_input" type="password" name="re_password" id="re_password" placeholder="请确认您的登录密码 "/><br>
					<a id="red_btn" class="_input _border bg_button aligncenter font444 color4">立即注册</a><br>
					<div class="_input _border1 aligncenter">
						<span class="font1 color5">已有账号？</span>
						<span><a href="<?php echo Url::toRoute(['site/login']); ?>" class="font1 color7">登录</a></span>
					</div>
				</form>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</body>
<script type="text/javascript">
	var mobile;
	$(function(){
		

		$("#register_wrap #phone").change(function(){
			//获取选择的值
			mobile = $(this).val();
		});

		
			
		$("#get_reg_code").click(function(){
			var $_this = $(this);
			var url = "<?php echo ApiUrl::toRoute('user/reg-get-code');?>";
			if( !$("#get_reg_code").hasClass("bg_button1") ){
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
		

		$("#red_btn").click(function(){
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
							window.location.href = "<?php echo Url::toRoute(['site/index']);?>";
						}else{
							$('#notification').html(data.message);
						}
					}
				});
			}
		});
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
	$(document).ready(function(){
		var curIndex=0,timeInterval=1500,arr=new Array(),arr1=new Array();
		arr[0]="<?php echo $this->absBaseUrl; ?>/image/site/gsy.jpg";
		arr[1]="<?php echo $this->absBaseUrl; ?>/image/site/sqsc.jpg";
		arr[2]="<?php echo $this->absBaseUrl; ?>/image/site/kzr.jpg";
		arr1[0]="<?php echo $this->absBaseUrl; ?>/image/site/page_reduce1.jpg";
		arr1[1]="<?php echo $this->absBaseUrl; ?>/image/site/page_add1.jpg";
		arr1[2]="<?php echo $this->absBaseUrl; ?>/image/site/page_reduce.jpg";
		arr1[3]="<?php echo $this->absBaseUrl; ?>/image/site/page_add.jpg";
		changeButton();
		var timing = self.setInterval(changeImg,timeInterval);//定时切换图片
		$(".r_left").mouseover(function(){
			window.clearInterval(timing); //清楚定时器
		});
		$(".r_left").mouseout(function(){
			timing = self.setInterval(changeImg,timeInterval);//定时切换图片
		});
		$("#left").click(function(){
			if(curIndex == 0){
				curIndex = 0;
			}else{
				curIndex -= 1;
			}
			$("#obj").attr("src", arr[curIndex]);
			changeButton();
		});
		$("#right").click(function(){
			if(curIndex == arr.length-1){
				curIndex = arr.length-1;
			}else{
				curIndex += 1;
			}
			$("#obj").attr("src", arr[curIndex]);
			changeButton();
		});
		function changeImg()
		{
			if(curIndex == arr.length-1){
				curIndex = 0;
			}else{
				curIndex += 1;
			}
			$("#obj").attr("src", arr[curIndex]);
			changeButton();
		}
		function changeButton()
		{
			if($("#obj").attr("src") == arr[0]){
				$("#left img").attr("src", arr1[0]);
				$("#left img").attr("title", "第一张");
				$("#left").css("cursor","no-drop");
			}else{
				$("#left img").attr("src", arr1[2]);
				$("#left img").attr("title", "向左滚");
				$("#left").css("cursor","pointer");
			}
			if($("#obj").attr("src") == arr[arr.length-1]){
				$("#right img").attr("src", arr1[1]);
				$("#right img").attr("title", "最后一张");
				$("#right").css("cursor","no-drop");
			}else{
				$("#right img").attr("src", arr1[3]);
				$("#right img").attr("title", "向右滚");
				$("#right").css("cursor","pointer");
			}
		}
	})
</script>
