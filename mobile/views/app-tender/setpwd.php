<?php
use yii\helpers\Url;
use mobile\components\ApiUrl;
?>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/tender.css">
<body>
<div data-role="page">
    <div data-role="content" id="mcontent">
        <div id="t_head">   
            <div class="h_image">
            <image src="<?php echo $this->absBaseUrl; ?>/image/info.gif" width="35"  />
            </div>
            <div class="h_text">
                恭喜您完成银行卡绑定，现请设置交易密码以保证您的每一笔资金变动安全
            </div>
        </div>
        <form>
            <input type="password" name="password" id="password" placeholder="请输入8位交易密码">
            <input type="password" name="repassword" id="repassword" placeholder="请确认交易密码">
        </form>
          <div id="t_text2">
        交易密码由6~16位数字和字母组成，字母区分大小写
        </div>
        <div id="mbutton">
            <a href="javascript:f_submit();" data-role="button" id="fsave">完成</a>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    function f_submit(){        
        if (check_password()&& check_repassword()){
            var ajaxurl = "<?php echo ApiUrl::toRoute(['user/set-paypassword'],true); ?>";
            $.ajax({
                url : ajaxurl,
                type: 'POST',
                dataType: 'jsonp',
                jsonp: 'Callback',
                success:function(data){
                    if (data.code == 0){
                        var regurl =  "<?php echo Url::toRoute(['app-tender/information']); ?>" + "?password="+$('#password').val();
                        window.location.href = regurl;
                    }
                }
            });
        }
        

    }

    function check_password(){
        if ('' == $.trim($('#password').val())){
            alert('密码不能为空');
            return false;
        }
        var password_reg = /^[a-zA-Z0-9]{6,16}$/;
        if (!password_reg.test($('#password').val())){
            alert('密码必须由6-16位数字和字母组成');
            return false;
        }
        return true;
    }
    function check_repassword(){
        if ($('#repassword').val() != $('#password').val()){
            alert('重复密码不真确'); 
            return false;
        }
        return true;
    }
</script>
