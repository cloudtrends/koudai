<?php
use yii\helpers\Url;
use website\components\ApiUrl;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<meta name="keywords" content="口袋理财">
	<script type="text/javascript" src="<?php echo $this->absBaseUrl; ?>/js/jquery.min.js" ></script>
	<link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/style.css">
</head>

<body>
	<div class="wrap">
		<!-- 头部开始 -->
		<div id="header">
			<div class="content _relative">
				<span class="_hover"><img src="<?php echo $this->absBaseUrl; ?>/image/site/gl_phone.png"></span>
				<span class="_hover"><img src="<?php echo $this->absBaseUrl; ?>/image/site/gl_weixin.png"></span>
				<a href="###" class="_hover"><img src="<?php echo $this->absBaseUrl; ?>/image/site/gl_qq.png"></a>
				<a href="###" class="_hover"><img src="<?php echo $this->absBaseUrl; ?>/image/site/gl_microblog.png"></a>
				<ul id="nav_left">
					<li><span class="font11 color1">咨询热线：400-002-0802&nbsp;&nbsp;&nbsp;工作时间：9:00 - 19:00&nbsp;&nbsp;&nbsp;</span></li>
					<li class="_relative"><a><img index_vlaue="0" src="<?php echo $this->absBaseUrl; ?>/image/site/phone.jpg"></a></li>
					<li class="_relative"><a><img index_vlaue="1" src="<?php echo $this->absBaseUrl; ?>/image/site/weixin.jpg"></a></li>
					<li class="_relative"><a><img index_vlaue="2" src="<?php echo $this->absBaseUrl; ?>/image/site/qq.jpg"></a></li>
					<li class="_relative"><a><img index_vlaue="3" src="<?php echo $this->absBaseUrl; ?>/image/site/microblog.jpg"></a></li>
				</ul>
				<ul id="nav_right">
					<li class="first_child"><a href="###" class="font11 color1">帮助中心</a></li>
					<li><span class="line clear_inline_block"><img src="<?php echo $this->absBaseUrl; ?>/image/site/line.jpg"></span></li>
					<?php if (!empty($this->userName)):?>
						<li><a href="<?php echo Url::toRoute(['site/my-invest']); ?>" class="font11 color1">账户中心</a></li>
						<li><span class="font11 color1">消息&nbsp;<a class="font11 color7" id="msg"><?php echo $this->GetMsgCount() ? $this->GetMsgCount() : '1'; ?></a></span></li>
						<li><span class="font11 color7"><?php echo $this->GetUserName();?></span>&nbsp;<a href="javascript:loginout();" class="font11 color1">退出</a></li>
					<?php else:?>
						<li><a href="<?php echo Url::toRoute(['site/login']); ?>" class="font11 color1">登录</a></li>
					<?php endif;?>
				</ul>
			</div>
			<div class="clear"></div>
		</div>
		<div id="logo">
			<div class="content">
				<ul id="nav_left">
					<li><a href="<?php echo Url::toRoute(['site/index']); ?>">
						<img src="<?php echo $this->absBaseUrl; ?>/image/site/logo.jpg">
					</a></li>
				</ul>
				<ul id="nav_right">
					<li class="first_child"><a href="###" class="font44 color1">关于口袋</a></li>
					<li><a href="###" class="font44 color1">安全保障</a></li>
					<li><a href="###" class="font44 color1">转让专区</a></li>
					<li><a href="<?php echo Url::toRoute(['site/list']); ?>" class="font44 color1">产品列表</a></li>
					<li><a href="<?php echo Url::toRoute(['site/index']); ?>" class="font44 color1">首页</a></li>
				</ul>
			</div>
			<div class="clear"></div>
		</div>
		<!-- 头部结束 -->
		

		<!-- 主体部分开始 -->
		<div class="container">
			<?php echo $content; ?>
		</div>
		<!-- 主体部分结束 -->

		<!-- 底部开始 -->
		<div id="footer">
			<div class="content">
					<ul class="float_left">
						<li><span class="font1 color2">友情链接</span></li>
						<li id="links" class="m_top">
							<ul>
								<li><a class="font1 color2" href="http://www.wzdai.com/" target="_blank">温州贷</a></li>
								<li><a class="font1 color2" href="###" target="_blank">诚信网站</a></li>
								<li><a class="font1 color2" href="http://www.icbc.com.cn/" target="_blank">工商银行</a></li>
								<li><a class="font1 color2" href="###" target="_blank">支付宝</a></li>
								<li><a class="font1 color2" href="###" target="_blank">明鑫担保公司</a></li>
							</ul>
						</li>
					</ul>
					<ul class="float_center">
						<li><span class="font1 color2">全国服务热线</span></li>
						<li class="m_top"><span class="font2 color2">400-002-0802</span></li>
						<li><span class="font1 color2">( 工作日  09:00-19:00 )</span></li>
								
					</ul>
					<ul class="float_right">
						<li class="f_left"><span class="font1 color2">关注口袋理财官方微信<br>开启您的财富之门! </span></li>
						<li class="f_right"><img src="<?php echo $this->absBaseUrl; ?>/image/site/QR_code.jpg"></li>
					</ul>
					<div class="clear"></div>
			</div>
			<div class="content aligncenter">
				<span class="font1 color3">上海凌融网络科技有限公司   版权所有  2014  沪ICP备14052872号-1 </span>
			</div>
		</div>
		<!-- 底部结束 -->
	</div>
</body>
<script type="text/javascript">
	// alert(navigator.userAgent);
	function loginout(){
		var url = "<?php echo ApiUrl::toRoute(['user/logout'],true) ?>";
		$.ajax({
			url : url,
			type: 'POST',
			dataType: 'jsonp',
			jsonp: 'callback',
			success:function(data){
				if (data.code == 0 && data.result == true){
					location=location;
				}
			}
		});
	}

	var timeInterval=300,msgindex=0;
	if($("#header #msg").html() != '0'){
		self.setInterval(changeMsg,timeInterval);
	}
	function changeMsg(){
		msgindex++;
		if(msgindex % 2 == 0){
			$("#header #msg").addClass("color7");
			$("#header #msg").removeClass("color77");
		}else{
			$("#header #msg").addClass("color77");
			$("#header #msg").removeClass("color7");
		}
	}

	var index_vlaue = null,arr=new Array(),arr1=new Array();
	arr[0]="<?php echo $this->absBaseUrl; ?>/image/site/gl_phone.jpg";
	arr[1]="<?php echo $this->absBaseUrl; ?>/image/site/gl_weixin.jpg";
	arr[2]="<?php echo $this->absBaseUrl; ?>/image/site/gl_qq.jpg";
	arr[3]="<?php echo $this->absBaseUrl; ?>/image/site/gl_microblog.jpg";
	arr1[0]="<?php echo $this->absBaseUrl; ?>/image/site/phone.jpg";
	arr1[1]="<?php echo $this->absBaseUrl; ?>/image/site/weixin.jpg";
	arr1[2]="<?php echo $this->absBaseUrl; ?>/image/site/qq.jpg";
	arr1[3]="<?php echo $this->absBaseUrl; ?>/image/site/microblog.jpg";
	$("#header .content #nav_left li a img").mouseover(function(){
		index_vlaue = $(this).attr("index_vlaue");
		$(this).attr("src",arr[index_vlaue]);
		$("#header .content").find("._hover:eq("+index_vlaue+")").show();
	});

	$("#header .content #nav_left li a img").mouseout(function(){
		$(this).attr("src",arr1[index_vlaue]);
		$("#header .content").find("._hover:eq("+index_vlaue+")").hide();
	});

	$("#header .content ._hover").mouseover(function(){
		$("#header .content #nav_left").find("._relative:eq("+index_vlaue+")").find("img").attr("src",arr[index_vlaue]);
		$(this).show();
	});

	$("#header .content ._hover").mouseout(function(){
		$("#header .content #nav_left").find("._relative:eq("+index_vlaue+")").find("img").attr("src",arr1[index_vlaue]);
		$(this).hide();
	});
</script>
</html>