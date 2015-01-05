<?php
use yii\helpers\Url;
use backend\components\widgets\ActiveForm;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>登录管理中心</title>
	<link href="<?php echo $this->baseUrl; ?>/image/admincp.css?t=2014120301" rel="stylesheet" type="text/css" />
	<script src="<?php echo $this->baseUrl; ?>/js/jquery.min.js" type="text/javascript"></script>
</head>
<body>
<script type="text/JavaScript">
if (self.parent.frames.length != 0) {
	self.parent.location=document.location;
}
function refreshCaptcha() {
	$.ajax({
		url: '<?php echo Url::toRoute(['main/captcha', 'refresh' => 1]); ?>',
		dataType: 'json',
		success: function(data){
			$('#loginform-verifycode-image').attr('src', data.url);
		}
	});
}
</script>
<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
<table class="logintb">
	<tr>
		<td class="login" style="width:210px;">
			<h1>口袋理财管理中心</h1>
		</td>
		<td>
			<p style="color:red;"><?php echo $model->hasErrors() ? array_shift($model->getFirstErrors()) : ''; ?></p>
			<p class="logintitle">用户名：</p>
			<p class="loginform"><input type="text" class="txt" name="LoginForm[username]" value="<?php echo $model->username; ?>"></p>
			<p class="logintitle">密&#12288;码：</p>
			<p class="loginform"><input type="password" class="txt" name="LoginForm[password]" value="<?php echo $model->password; ?>"></p>
			<p class="logintitle">验证码：</p>
			<p class="loginform" style="height:30px;width:200px;">
				<input type="text" name="LoginForm[verifyCode]" value="<?php echo $model->verifyCode; ?>" class="txt" id="loginform-verifycode" style="width:60px;vertical-align:top;">
				<img onclick="refreshCaptcha();" title="点击刷新验证码" src="<?php echo Url::toRoute(['main/captcha', 'v' => uniqid()]); ?>" id="loginform-verifycode-image">
			</p>
			<p class="loginnofloat"><input type="submit" class="btn" value="登录" name="submit_btn"></p>
		</td>
	</tr>
</table>
<?php ActiveForm::end(); ?>
<table class="logintb">
<tr>
	<td colspan="2" class="footer">
		<div class="copyright">
			<p></p>
			<p></p>
		</div>
	</td>
</tr>
</table>
</body>
</html>
