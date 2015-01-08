<?php
use yii\helpers\Url;
use website\components\ApiUrl;
use common\models\User;
use common\helpers\StringHelper;
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
			<div class="content">
				<ul id="nav_left">
					<li><span class="font11 color1">咨询热线：400-002-0802&nbsp;&nbsp;&nbsp;&nbsp;工作时间： 9：00 - 19：00&nbsp;&nbsp;&nbsp;&nbsp;</span></li>
					<li><a href="###"><img src="<?php echo $this->absBaseUrl; ?>/image/site/phone.jpg"></a></li>
					<li><a href="###"><img src="<?php echo $this->absBaseUrl; ?>/image/site/weixin.jpg"></a></li>
					<li><a href="###"><img src="<?php echo $this->absBaseUrl; ?>/image/site/qq.jpg"></a></li>
					<li><a href="###"><img src="<?php echo $this->absBaseUrl; ?>/image/site/microblog.jpg"></a></li>
				</ul>
				<ul id="nav_right">
					<li class="first_child"><a href="###" class="font11 color1">帮助中心</a></li>
					<li><span class="line">|</span></li>
					<li><a href="###" class="font11 color1">账户中心</a></li>
					<?php if (!empty(Yii::$app->user->identity['username'])):?>
						<li><?php echo StringHelper::blurPhone(Yii::$app->user->identity['username']);?> <a href="javascript:loginout();" class="font11 color1">退出</a></li>
					<?php else:?>
						<li><a href="<?php echo Url::toRoute(['site/login']); ?>" class="font11 color1">登录</a></li>
					<?php endif;?>
					<li><a href="###" class="font11 color1">消息<span class="font11 color7">2</span></a></li>
					
				</ul>
			</div>
			<div class="clear"></div>
		</div>
		<div id="logo">
			<div class="content">
				<ul id="nav_left">
					<li><a href="<?php echo Url::toRoute(['site/index']); ?>">
						<img src="<?php echo $this->absBaseUrl; ?>/image/site/logo.png">
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
						<li class="f_right"><img src="<?php echo $this->absBaseUrl; ?>/image/site/QR_code1.png"></li>
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

</script>
</html>