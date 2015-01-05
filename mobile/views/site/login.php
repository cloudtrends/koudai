<?php
use yii\helpers\Url;
use mobile\components\ApiUrl;
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/login.css">

<body id="weixin">
<div data-role="page">
    <div data-role="content" id="acontent">
        <form method="post" action="">
            <div data-role="fieldcontain">
                <div>
                    <input type="text" name="username" id="username" placeholder="请输入手机号码" class="phoneli reinput">
                    <div class="iuser"></div>
                </div>
                <div>
                    <input type="password" name="password" id="password" class="phoneli reinput" placeholder="请输入密码">
                    <div class="ipwd"></div>
                    <div onclick = "javascript:goRegister(2);" style="cursor:pointer" class="forget">注册</div>
                </div>
                <div id="notification"></div>

            </div>
        </form>
    </div>
    <a href="javascript:void(0);" data-role="button" id="red_btn" data-shadow="false" >登 录</a>
    <div id="afooter">
        <div class="bimage"></div>
    </div>
</div>
</body>
<script type="text/javascript">
    var ajax_url = "<?php echo ApiUrl::toRoute('user/login');?>";
    $(function(){
        $("#red_btn").click(function(){
            var btn_id = '#red_btn';
            var btn_text = "登 录";
            var a_js_var = "javascript:void(0);";
            if (check_mobile() && check_pwd()){
                $.ajax({
                    url : ajax_url,
                    type: 'POST',
                    data : {
                        username : $('#username').val(),
                        password : $('#password').val(),
                        OPENID : '<?php echo $OPENID;?>'
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
                            //登陆和绑定成功 行为
                            window.location.href = "<?php echo Url::toRoute(['site/index','OPENID' => $OPENID]);?>";
                        }else{
                            $('#notification').html(result.message);
                        }
                    }
                });
            }
        });

    });

    function goRegister(type){
        var RegisterUrl = "<?php echo Url::toRoute(['site/register','OPENID'=>$OPENID]);?>" + "&type="+type;
        window.location.href = RegisterUrl;
    }


    function check_pwd()
    {
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
            $('#notification').html('手机号不能为空');
            return false;
        }else{
            $('#notification').html('');
        }
        var mobile_reg = /^[1]\d{10}$/;
        if (!mobile_reg.test($('#username').val())){
            $('#notification').html('手机号不合法');
            return false;
        }else{
            $('#notification').html('');
        }
        return true
    }
</script>


