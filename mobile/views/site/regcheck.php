<?php
use yii\helpers\Url;
use mobile\components\ApiUrl;
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/login.css">

<body>
<div data-role="page">
    <div data-role="content" id="acontent">
        <form method="post" action="">
            <div data-role="fieldcontain">
                <div>
                    <input type="password" name="msgcode" id="msgcode" class="phoneli reinput" placeholder="请输入收到的验证码">
                    <div onclick = "javascript:getRegCode();" style="cursor:pointer" class="regcode">点击获取</div>
                </div>

                <input type="password" name="password" id="password" placeholder="请输入密码" class="phoneli reinput">

                <label><input type="checkbox" value="checkbox" id="ch_xy" checked="checked" />我同意《平台使用协议》《账户使用协议》 </label>
            </div>
            <div id="notification"></div>
        </form>
    </div>
    <div><a href="javascript:regsubmit();" data-role="button" id="red_btn" data-shadow="false" >提交注册</a></div>
    <div id="afooter">
        <div class="bimage"></div>
    </div>
</div>
</body>
<script type="text/javascript">
    var ajaxurl = "<?php echo Url::toRoute('app-ajax/ajax');?>";
    var mobile = '<?php echo $mobile;?>';
    var ch_pwd = false;
    var ch_YZM = false;
    $(document).ready(function(){

    });

    function regsubmit(){
        var btn_id = '#red_btn';
        var btn_text = "提交注册";
        var a_js_var = "javascript:regsubmit();";
        var ch_xy = ch_xieyi();
        var url = "<?php echo ApiUrl::toRoute('user/register');?>";
        var dataArr = '';
        <?php if (!empty($ValidCode)):?>
            dataArr =  {phone : mobile,
                password : $('#password').val(),
                code : $('#msgcode').val(),
                valid_code:"<?php echo $ValidCode?>"};
        <?php else:?>
            dataArr =  {phone : mobile,
                password : $('#password').val(),
                code : $('#msgcode').val(),
                type : 0,
                contact_id : '<?php echo $OPENID;?>'};
        <?php endif;?>

        if (ch_YZM && ch_pwd && ch_xy){
            $.ajax({
                url : url,
                type: 'POST',
                data : dataArr,
                beforeSend : function(){
                    $(btn_id).attr('href','#');
                    $(btn_id).html("载入中…");
                },
                complete : function(){
                    $(btn_id).attr('href', a_js_var);
                    $(btn_id).html(btn_text);  
                },
                success:function(data){
                    if(data.code == 0)
                    {
                        window.location.href = "<?php echo Url::toRoute(['site/index']);?>";
                    }
                    else
                    {
                        $('#notification').html(data.message);
                    }
                }
            });
        }
    }

    function ch_xieyi(){
        var bool = false;
        if ($('#ch_xy').is(':checked')){
            $('#notification').html();
            bool = true;
        }else{
            $('#notification').html('请勾选同意协议');
            bool = false;
        }
        return bool;
    }


    $('#msgcode').blur(function(){
        if ( '' != $.trim($('#msgcode').val()) ){
            $('#notification').html('');
            ch_YZM = true;
        }else{
            ch_YZM = false;
            $('#notification').html('验证码不能为空');
        }
    });

    $('#password').blur(function(){
        if ($('#password').val().length >= 6 && $('#password').val().length <=16){
            ch_pwd = true;
            $('#notification').html('');
        }else{
            ch_pwd = false;
            $('#notification').html('密码为6-16位字符或数字');
        }
    });


    function getRegCode(){
        var url = "<?php echo ApiUrl::toRoute('user/reg-get-code');?>";
        $.ajax({
            url : url,
            type : 'POST',
            data : {
                phone : mobile
            },
            success:function(data){
                if(data.code == 0){
                    $('#notification').html("验证码已发送到" + mobile);
                }else{
                    $('#notification').html(data.message);
                }
            }
        });
    }

    function getMobileYZM(){
        getRegCode();
    }

    function regetvcode ($obj) {
        var total = 60;
        $obj.attr("value",total+"秒重新获取").attr("disabled","disabled");
        $obj.addClass('getMobileYZM-Wait');
        var interId = setInterval(function () {
            total--;
            $obj.attr("value",total + "秒重新获取");

            if (total <= 0) {
                clearInterval(interId);
                $obj.attr("value"," 点击获取").removeAttr("disabled");
                //$obj.removeClass('getMobileYZM-Wait');
            }

        } , 1000);
    }

</script>


