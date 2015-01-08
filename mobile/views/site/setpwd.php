<?php
use yii\helpers\Url;
use mobile\components\ApiUrl;
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/info.css">
<head><title>设置交易密码</title></head>   
<body>
    <div data-role="page">
        <div data-role="content" id="mcontent">
            <div id="t_head">   
                <div>
                    <img class="h_image" src="<?php echo $this->absBaseUrl; ?>/image/info.gif" />
                </div>
                <div class="h_text">
                    恭喜您完成银行卡绑定，现请设置交易密码以保证您的资金安全
                </div>
            </div>
        </div>
        <input type="password" name="password" id="password" placeholder="请输入交易密码">
        <input type="password" name="repassword" id="repassword" placeholder="请确认交易密码">     
        <div id="t_text2">
            交易密码由6~16位数字和字母组成，字母区分大小写
        </div>
        <div id="notification"></div>      
        <a href="javascript:setpwd();" data-role="button" id="red_btn">完 成</a>      
    </div>
</body>
<script type="text/javascript">
    function setpwd(){    
        $('#notification').html("");
        if (check_password()&& check_repassword()){
            var ajaxurl = "<?php echo ApiUrl::toRoute(['user/set-paypassword'],true); ?>";
            var btn_id = '#red_btn';
            var btn_text = "完 成";
            var a_js_var = "javascript:setpwd();";
            $.ajax({
                url : ajaxurl,
                type: 'POST',
                dataType: 'jsonp',
                data : { password : $('#password').val() },
                   beforeSend : function(){
                    $(btn_id).attr('href','#');
                    $(btn_id).html("载入中…");
                },
                complete : function(){
                    $(btn_id).attr('href', a_js_var);
                    $(btn_id).html(btn_text);  
                },
                jsonp: 'callback',
                success:function(data){     
                    if (data.code == 0){
                        var regurl =  "<?php echo Url::toRoute(['site/index']); ?>";
                        window.location.href = regurl;
                    }else{
                        $('#notification').html(data.message);
                    }
                }
            });
        }
        

    }

    function check_password(){
        if ('' == $.trim($('#password').val())){
            $('#notification').html("密码不能为空");
            return false;
        }      
        var password_reg = /^[a-zA-Z0-9]{6,16}$/;
        if (!password_reg.test($('#password').val())){
            $('#notification').html('密码必须由6-16位数字和字母组成');
            return false;
        }
        return true;
    }
    function check_repassword(){
        if ($('#repassword').val() != $('#password').val()){
            $('#notification').html('两次输入密码不一致'); 
            return false;
        }
        return true;
    }
</script>
