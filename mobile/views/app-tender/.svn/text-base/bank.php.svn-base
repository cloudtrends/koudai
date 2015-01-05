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
               恭喜您通过实名认证，现请绑定您的个人银行卡，您的购买与提现资金会返回至该卡。绑定的过程中，第三方支付会扣取0.01元。
            </div>
        </div>
        <div id="username">
            用户名 林佳
        </div>
        <form>
            <select>
                <option value ="0">--请选择银行--</option>
                <option value ="1">工商银行</option>
                <option value ="2">中国银行</option>
                <option value="3">交通银行</option>
                <option value="4">建设银行</option>
                
            </select>

           
            <input type="text" name="idcard" id="idcard" placeholder="请输入身份证号码">
        </form>
    
        
        <div id="mbutton">
            <a href="javascript:f_submit();" data-role="button" id="login"  >完成</a>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
 function f_submit(){

        var ajaxurl = "<?php echo ApiUrl::toRoute(['user/bind-card'],true); ?>";
            $.ajax({
                url : ajaxurl,
                type: 'POST',
                dataType: 'jsonp',
                jsonp: 'Callback',
                success:function(data){
                    if (data.code == 0){
                        var regurl =  "<?php echo Url::toRoute(['app-tender/setpwd']); ?>" ;
                        window.location.href = regurl;
                    }
                }
            });
        }
  </script> 