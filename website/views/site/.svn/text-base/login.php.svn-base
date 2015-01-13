<?php
use yii\helpers\Url;
use website\components\ApiUrl;
?>
<head><title><?php echo $title; ?></title></head>
<div id="login_wrap">
	<div class="content">
		<div class="l_left">
			<ul>
				<li id="left"><img src="<?php echo $this->absBaseUrl; ?>/image/site/page_reduce.jpg"/></li>
				<li><img id="obj" src ="<?php echo $this->absBaseUrl; ?>/image/site/gsy.jpg"/></li>
				<li id="right"><img src="<?php echo $this->absBaseUrl; ?>/image/site/page_add.jpg"/></li>
			</ul>
		</div>

		<div class="l_right aligncenter">
			<span class="font3 color2">登录</span>
			<form method="post" action="" class="clear_inline_block  alignright">
				<span id="notification" class="font1 color7"></span><br>
				<div class="clear"></div>
				<span class="font1 color5">账户</span>
				<input class="_input bg_input first_child" type="text" maxlength="11" name="username" id="username" placeholder="请输入注册的手机号码"/><br>
				<span class="font1 color5">密码</span>
				<input class="_input bg_input" type="password" name="password" id="password" placeholder="请输入您的登录密码"/><br>
				<a id="red_btn" class="_input _border bg_button aligncenter font444 color4" name="r_submit">登录</a>
				<div class="_input _border1 aligncenter">
					<span><a href="###" class="font1 color7">忘记密码</a></span>
					<span class="font1 color9 nth_child">|</span>
					<span><a href="<?php echo Url::toRoute(['site/register']); ?>" class="font1 color7">立即注册</a></span>
				</div>
			</form>
		</div>
		<div class="clear"></div>
	</div>
</div>
<script type="text/javascript">
	var ajax_url = "<?php echo ApiUrl::toRoute('user/login');?>";
	$("#red_btn").click(function(){
		var btn_id = '#red_btn';
		var btn_text = "登 录";
		var a_js_var = "javascript:void(0);";
		if (check_mobile() && check_pwd()){
			$.ajax({
				url : ajax_url,
				type: 'POST',
				dataType: "jsonp",
				jsonp: "callback",//传递给请求处理程序或页面的，用以获得jsonp回调函数名的参数名(一般默认为:callback)
				jsonpCallback:"flightHandler",//自定义的jsonp回调函数名称，默认为jQuery自动生成的随机函数名，也可以写"?"，jQuery会自动为你处理数据
				data : {
					username : $('#username').val(),
					password : $('#password').val(),
				},
				beforeSend : function(){
					$(btn_id).attr('href','#');
					$(btn_id).html("载入中…");
				},
				complete : function(){
					$(btn_id).attr('href', a_js_var);
					$(btn_id).html(btn_text);  
				},
				success : function(result){
					if( result.code == 0){
						//登陆成功 行为
						window.location.href = "<?php echo Url::toRoute(['site/index']);?>";
					}else{
						$('#notification').html(result.message);
					}
				}
			});
		}
	});


	function check_pwd(){
		if ('' == $.trim($('#password').val())){
			$('#notification').html('密码不能为空');
			return false;
		}else{
			$('#notification').html('');
		}
		return true;
	}

	function check_mobile(){
		if ('' == $.trim($('#username').val())){
			$('#notification').html('账号不能为空');
			return false;
		}else{
			$('#notification').html('');
		}
		var mobile_reg = /^[1]\d{10}$/;
		if (!mobile_reg.test($('#username').val())){
			$('#notification').html('账号不合法');
			return false;
		}else{
			$('#notification').html('');
		}
		return true
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
		$(".l_left").mouseover(function(){
			window.clearInterval(timing); //清楚定时器
		});
		$(".l_left").mouseout(function(){
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
		function changeImg(){
			if(curIndex == arr.length-1){
				curIndex = 0;
			}else{
				curIndex += 1;
			}
			$("#obj").attr("src", arr[curIndex]);
			changeButton();
		}
		function changeButton(){
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
