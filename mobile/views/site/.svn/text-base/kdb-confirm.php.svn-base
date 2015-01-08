<?php
use mobile\components\ApiUrl;
use yii\helpers\Url;
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/tender.css">
<head><title>确认投标</title></head>
<body>
    <div data-role="page">
        <div data-role="content">
            <label ><div class="t_text">口袋宝</div></label>
            <label ><div class="ltext">买入金额(元): </div></label>
            <label > <div class="rtext"> <?php echo $money ?> </div></label>
        </div>
        <div>
            <label id="pay_type_label"> 付款方式 </label>
            <label id="rest_money" ><input id="usable_money_title" type="checkbox" name="paytype" value="1" id="ch_ye" checked="checked" onchange="javascript:payment(this)" />余额：<span id="usable_money"></span> 元 </label>
            <label id="bank_info" ><input type="checkbox" name="paytype" value="2" disabled="disabled" id="ch_bank" checked="checked" />
                <span id="bankname">银行卡</span>（尾号<span id="tailno">057</span>）：<span id="bank_pay"></span> 元</label>
        </div>
        <div id="asdf">
            <input type="password" name="password" id="password" placeholder="请输入6位交易密码">
        </div>
        <div id="cap" style="display: none;">
            <input type="text" name="captcha" id="captcha"  placeholder="请输入收到的验证码">            
        </div>
        <div id="notification"></div>
        <div id="mbutton">
            <a href="javascript:f_pay();" data-role="button" id="red_btn" >确认投标</a>
        </div>

    </div>
</body>
<script src="<?php echo $this->absBaseUrl; ?>/js/base64.js"></script>
<script type="text/javascript">
    var u_money = 0;
    var money = Number("<?php echo $money?>");
    var ajaxurl = "<?php echo ApiUrl::toRoute(['account/get'],true);?>";
    var use_remain = 1;    //是否用余额，1或0
    $(document).ready(function(){
        $.ajax({
            url : ajaxurl,
            type: 'POST',
            dataType: 'jsonp',
            jsonp: 'callback',
            success:function(data)
            {
                if (data.code == 0){
                    
                    u_money = Number( data.usable_money / 100 );
                    $('#usable_money').html(u_money.toFixed(2));

                    if (data.banks.length > 0)
                    {
                        $('#tailno').html(data.banks[0].tail_number);
                        $('#bankname').html(data.banks[0].bank_name);
                    }
                    else
                    {
                        $('#bank_info').hide();
                    }

                    bankmoney(u_money);
                }
            }
        });
    });

    function f_pay(){
        if (check_pwd()){
            var btn_id = '#red_btn';
            var btn_text = "确认投标";
            var a_js_var = "javascript:f_pay();";
            var b = new Base64();
            var sign = "captcha="+$('#captcha').val()
                +"&money="+money
                +"&order_id="+"<?php echo $orderid;?>"
                +"&pay_password="+$('#password').val()
                +"&use_remain="+use_remain
                +"**kdlc**";
            //sign = encodeURIComponent(sign);
            sign = b.encode(sign);
            var url = "<?php echo ApiUrl::toRoute(['koudaibao/invest'],true);?>";
            $.ajax({
                url : url,
                type: 'POST',
                data : {
                    use_remain : use_remain,
                    money : money,
                    pay_password : $('#password').val(),
                    order_id:"<?php echo $orderid;?>",
                    captcha:$('#captcha').val(),
                    sign: sign
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
                    if (data.code == 0)
                    {
                        window.location.href = "<?php echo Url::toRoute(['site/success','money' => $money]);?>";
                    }
                    if (data.message == "需要验证码，已经发送至您的手机"){
                        $('#notification').html(data.message);
                        document.getElementById('cap').style.display = 'block';
                    }
                    else
                    {
                        $('#notification').html(data.message);
                    }
                }
            });
        }
    }

    function check_pwd(){

        var password_reg = /^[0-9]{6}$/;
        if (!password_reg.test($('#password').val())){
            $('#notification').html('请输入6位交易密码');
            return false;
        }
        return true
    }

    function payment($obj){
        if ($obj.checked){
            use_remain = 1;
            bankmoney(u_money);
        }else{
            use_remain = 0;
            $('#bank_pay').html(money.toFixed(2));
        }
    }

    //计算银行卡支付金额
    function bankmoney(usable_money){
        var bank_pay = 0;
        if (usable_money >= money){
            bank_pay = 0
        }else{
            bank_pay = money - usable_money;
        }
        $('#bank_pay').html(bank_pay.toFixed(2));
    }
</script>