<style style="text/css">
.box {position:relative;}
.download {display:inline-block;position:absolute;top:61%;left:10%;width:38%;height:9%;}
.android {left:52%;}
</style>

<div class="box">
	<img src="<?php echo Yii::$app->getRequest()->getAbsoluteBaseUrl(); ?>/image/page/download.jpg" width="100%">
	<a href="javascript:iosDownload();" class="download ios"></a>
	<a href="javascript:androidDownload();" class="download android"></a>
</div>

<script type="text/javascript">
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