<?php
use yii\helpers\Url;
use mobile\components\ApiUrl;
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/info.css">
<head><title>添加银行卡</title></head>

<body>
<div data-role="page">
    <div data-role="content" id="mcontent">
        <div id="t_head">
            <div>
                <img class="h_image" src="<?php echo $this->absBaseUrl; ?>/image/info.gif" />
            </div>
            <div class="h_text">
               恭喜您通过实名认证，现在请您添加银行卡
            </div>
        </div>
        <label id="card_holder">持卡人: <?php echo $realname; ?></label>

        <select id="banklist">
            <option value="-1">请您选择银行</option>
        </select>

        <input  type="text" oninput="javascript:showBankCard()" name="bankcard" id="bankcard" placeholder="请输入银行卡号">

        <div id="notification"></div>
        <!--<input type="button" value="添 加" id="red_btn" onclick="bankSubmit()">-->
        <a href="javascript:bankSubmit();" readonly ="readonly" data-role="button" id="red_btn"  >添 加</a>
        <br><br>
        <div id="notice">
            1. 请您确保姓名、身份证、卡号跟银行预留一致<br>
            2. 验证银行卡，第三方支付需收取0.01元<br>
            3. 您的投资和取现同卡进出，保证资金安全<br>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    
    

    $(document).ready(function(){
      
        $.ajax({
            url : "<?php echo ApiUrl::toRoute(['user/support-banks'],true);?>",
            type: 'POST',
            dataType: 'jsonp',
            jsonp: 'callback',
            success:function(data)
            {
                if (data.code == 0){

                    for (var key in data.banks){
                        var bank = data.banks[key];
                        $("#banklist").append("<option value='"+bank.code+"'>"+bank.name+"</option>");
                    }

                }
            }
        });
    });
    
    
    
    function bankSubmit()
    {
        var bool = true;
        $('#notification').html("");
        var bank_id = $("#banklist").val();
        if( bank_id < 0)
        {
            $('#notification').html("请选择银行");
            bool = false;
        }

        var card = $("#bankcard").val();
        var real_card = card.replace(/ /g,"");

        if( real_card.length > 20 || real_card < 1)
        {
            $('#notification').html("请输入正确的银行卡号");
            bool = false;
        }
        
           //disabled
        //document.getElementById("red_btn").disabled=false; 
      
        if (bool){
            var btn_id = '#red_btn';
            var btn_text = "添 加";
            var a_js_var = "javascript:bankSubmit();";
            $.ajax({
                url : "<?php echo ApiUrl::toRoute(['user/test'],true);?>",
                type: 'POST',
                data : {
                    bank_card : real_card,
                    bank_id : bank_id
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
                jsonp : 'callback',                
                success : function(data){                                              
                    if (data.code == 0){
                         window.location.href = "<?php echo Url::toRoute(['site/setpwd'],true); ?>";
                    }
                    else
                    {                        
                        $('#notification').html(data.message);                        
                    }
                }
            });
        }
        
    }



    // 上一次修改结果
    var former_card = "";

    function showBankCard()
    {
        var cur_card = $("#bankcard").val();

        // 默认最后一个必须是数字或者空格  包括空格，长度不能超过 19 + 4
        var pattern = /^[0-9 ]{0,23}$/;
        if (!cur_card.match(pattern) )
        {
            // 如果不符合，则还原
            $("#bankcard").val(former_card);
            return;
        }


        var cur_format_card = "";

       // 替换掉中间的空格
        var real_card = cur_card.replace(/ /g,"");

        // 4个数字为一段
        var cnt = 4;
        for( var i = 0 ; i < real_card.length ; i++ )
        {
            if( i > 1 && i % cnt == 0)
            {
                cur_format_card += ' ' + real_card[i];
            }
            else
            {
                cur_format_card += '' + real_card[i];
            }
        }


        former_card = cur_format_card.trim();
        console.info(cur_format_card);
        $("#bankcard").val(cur_format_card);
        $("#bankcard").focus();

    }
  </script> 