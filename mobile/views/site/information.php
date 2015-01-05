<?php
use yii\helpers\Url;
use mobile\components\ApiUrl;
?>

<link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/info.css">
<head><title>身份信息确认</title></head>
<body>
<div data-role="page">
    <div data-role="content" id="mcontent">
        <div id="t_head">
            <div>
                <img class="h_image" src="<?php echo $this->absBaseUrl; ?>/image/info.gif" />
            </div>
            <div class="h_text">监管部门规定，购买理财产品需提供实名信息以确保投资安全</div>
        </div>

        <input type="text" name="username" id="username" placeholder="请输入您的真实姓名">
        <input type="text" name="idcard" id="idcard" placeholder="请输入您的身份证号码" >

    </div>
    <div id="notification"></div>
    <a href="javascript:id_verify();" data-role="button" id="red_btn" >认 证</a>
</div>
</body>
<script type="text/javascript">
    function id_verify()
    {
        $('#notification').html("");

        if( ck_username() && ck_idcard() )
        {   var btn_id = '#red_btn';
            var btn_text = "认 证";
            var a_js_var = "javascript:id_verify();";
            var ajaxurl = "<?php echo ApiUrl::toRoute(['user/real-verify'],true); ?>";
            $.ajax({
                url : ajaxurl,
                type: 'POST',
                data : {
                    realname : $('#username').val(),
                    id_card : $('#idcard').val()
                },
                 beforeSend : function(){
                    $(btn_id).attr('href','#');
                    $(btn_id).html("载入中…");
                },
                complete : function(){
                    $(btn_id).attr('href', a_js_var);
                    $(btn_id).html(btn_text);  
                },
                dataType: 'jsonp',
                jsonp: 'callback',
                success:function(data){
                    if (data.code == 0 || data.message == "您已认证通过，无需重复认证"){
                        window.location.href = "<?php echo Url::toRoute(['site/bank'],true); ?>";
                    }
                    else
                    {
                        $('#notification').html(data.message);
                    }
                }
            });
        }

    }

    function ck_username(){
        if ( $('#username').val().length == 0 )
        {
            $('#notification').html("请输入您的真实姓名");
            return false;
        }
        return true;
    }

    function ck_idcard()
    {
        if ( $('#idcard').val().length == 0 )
        {
            $('#notification').html("请输入您的身份证号码");
            return false;
        }

        if ( !$('#idcard').val().match(/^[0-9]{14,17}[0-9xX]$/)  )
        {
            $('#notification').html("请输入15或18位身份证号码");
            return false;
        }
        return true;
    }




</script>
