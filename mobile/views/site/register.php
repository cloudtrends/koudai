<?php
use yii\helpers\Url;
use mobile\components\ApiUrl;
?>
<body>
<div data-role="page">
    <div data-role="content" id="acontent">
        <form method="post" action="">
            <div data-role="fieldcontain">
                <div>
                    <input type="text" name="username" id="username" placeholder="请输入手机号码" class="phoneli reinput">
                    <div class="iuser"></div>
                </div>
                <div><a href="javascript:void(0);" data-role="button" id="f_submit" data-shadow="false" >下一步</a></div>
                <div id="notification" style="text-align: center; margin-top: 5px;font-size: 0.5em;font-weight: bolder;"></div>
            </div>
        </form>
    </div>
    <!-- <div data-role="footer">
        <h1>页脚</h1>
    </div> -->
    <div id="afooter">
        <div class="bimage"></div>
    </div>
</div>
</body>
<script type="text/javascript">
    $(document).ready(function(){
        $('#f_submit').click(function(){
            var regurl = '';
            <?php if (!empty($ValidCode)):?>
                regurl =  "<?php echo Url::toRoute(['site/regcheck','ValidCode'=>$ValidCode]);?>" + "&mobile="+$('#username').val();
            <?php else:?>
                regurl =  "<?php echo Url::toRoute(['site/regcheck','OPENID'=>$OPENID,'type'=>$type]);?>" + "&mobile="+$('#username').val();
            <?php endif;?>

            var c_mobile = check_mobile();
            if (c_mobile){
                window.location.href = regurl;
            }
        });
    });


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


