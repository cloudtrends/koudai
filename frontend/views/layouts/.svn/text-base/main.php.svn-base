<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
	<meta name="format-detection" content="telephone=no" />
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="#7CD88E">
	<title>口袋理财</title>
</head>
<style type="text/css">
html, body, div, span, h1, h2, h3, p, em, img, dl, dt, dd, ol, ul, li, table, tr,th,td, form,input,select {
	margin:0;
	padding:0;
}
body {
	min-width:320px;
	max-width:480px;
	min-height:100%;
	margin:0 auto;
}
/*下载客户端样式*/
#download_client{
		position:fixed;
		bottom: 0;
		width: 100%;
		max-width:480px;
	}
#download_client > a:first-child > div{
		width: 90%;
		float: left;
	}
#download_client > a:first-child + div{
		width: 10%;
		position: relative;
		float: right;
	}
#download_client > a:first-child + div>a:first-child{
		display: block;
		width: 100%;
		padding-top: 100%;
		position: absolute;
	}
.clear{
		clear: both;
	}
</style>
<body>
	<div class="container">
		<?php echo $content; ?>
	</div>
	
	<?php if (Yii::$app->getRequest()->get('isShare')): ?>
	<div id="download_client">
		<a href="javascript:downLoad()"><div><img src="<?php echo Yii::$app->getRequest()->getAbsoluteBaseUrl(); ?>/image/page/download1.png"></div></a>
		<div><a href="javascript:Close()"></a><a href="javascript:downLoad()"><img src="<?php echo Yii::$app->getRequest()->getAbsoluteBaseUrl(); ?>/image/page/close.png"></a></div>
		<div class="clear"></div>
	</div>
	<script type="text/javascript">
		var obj = document.getElementById("download_client");
		function Close(){
			obj.style.display = "none";
		}
	
		function downLoad() {
			if (window.browser.iPhone || window.browser.ipad || window.browser.ios) {
				iosDownload();
			} else {
				androidDownload();
			}
		}
		function iosDownload() {
			if (!window.browser.wx){
				window.location.href = "https://itunes.apple.com/cn/app/id953061503?mt=8";
				// window.location.href = "itms-services://?action=download-manifest&url=https://app.irongbao.com/iosdown/koudai/koudai.plist";
			}else{
				window.location.href = "http://mp.weixin.qq.com/mp/redirect?url=https%3A%2F%2Fitunes.apple.com%2Fcn%2Fapp%2Fid953061503%3Fmt%3D8";
				// window.location.href = "http://mp.weixin.qq.com/mp/redirect?url=itms-services%3A%2F%2F%3Faction%3Ddownload-manifest%26url%3Dhttps%3A%2F%2Fapp.irongbao.com%2Fiosdown%2Fkoudai%2Fkoudai.plist";
			}
		}
		function androidDownload() {
			if (!window.browser.wx){
				window.location.href = "http://www.koudailc.com/attachment/download/koudailicai.apk";
			}else{
				// 后面换成应用宝地址
				alert('请点击右上角按钮选择在浏览器中打开并下载！');
			}
		}
		window.onload = function(){
			var u = navigator.userAgent;
			window.browser = {};
			window.browser.iPhone = u.indexOf('iPhone') > -1;
			window.browser.android = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1;//android or uc
			window.browser.ipad = u.indexOf('iPad') > -1;
			window.browser.isclient = u.indexOf('lyWb') > -1;
			window.browser.ios = u.match(/Mac OS/); //ios
			window.browser.width = window.innerWidth;
			window.browser.height = window.innerHeight;
			window.browser.wx = u.match(/MicroMessenger/);
		}
	</script>
	<?php endif; ?>
</body>
</html>