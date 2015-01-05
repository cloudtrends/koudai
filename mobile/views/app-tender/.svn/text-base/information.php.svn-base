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
               监管部门规定，购买理财产品需提供实名信息以确保投资安全
            </div>
        </div>
        <form>
            <input type="text" name="username" id="username" placeholder="请输入您的真实姓名">
            <input type="text" name="idcard" id="idcard" placeholder="请输入身份证号码">
        </form>
    
        
        <div id="mbutton">
            <a href="javascript:gologin();" data-role="button" id="login"  >下一步</a>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    function f_submit(){

        var ajaxurl = "<?php echo ApiUrl::toRoute(['user/real-verify'],true); ?>";
            $.ajax({
                url : ajaxurl,
                type: 'POST',
                dataType: 'jsonp',
                jsonp: 'Callback',
                success:function(data){
                    if (data.code == 0){
                        var regurl =  "<?php echo Url::toRoute(['app-tender/bank']); ?>" + "?realname="+$('#username').val()+ "?id_card="+$('#idcard').val();
                        window.location.href = regurl;
                    }
                }
            });
        } 
</script>
