<?php
use mobile\components\ApiUrl;
use yii\helpers\Url;
?>
 <link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/tender.css">
<body>
<div data-role="page">
    <div data-role="content" id="mcontent">
        <div id="t_header">
            <h2>口袋宝</h2>
            <div class="t_text">
            <div class="t_ltext">买入金额（元）</div><div class="t_rtext"><?php echo $money?></div>
            </div>

        </div>
        <form>
             <label id="pay_type_label"> 付款方式 </label>
             <label ><input id="usable_money_title" type="checkbox" name="paytype" value="1" id="ch_ye" checked="checked" onchange="javascript:payment(this)" />余额：<span id="usable_money"></span> 元 </label>
             <label id="bank_info" ><input type="checkbox" name="paytype" value="2" disabled="disabled" id="ch_bank" checked="checked" />
                 <span id="bankname">银行卡</span>（尾号<span id="tailno">057</span>）：<span id="bank_pay"></span> 元</label>
             <input type="password" name="password" id="password" placeholder="请输入6位交易密码">
        </form>
        
        <div id="mbutton">
            <a href="javascript:f_pay();" data-role="button" id="f_pay"  >立即买入</a>
        </div>
        <div id="b64origin"></div>
        <div id="b64urlencode"></div>
        <div id="b64"></div>

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
            var b = new Base64();
            var sign = "money="+money
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
                    sign: sign
                },
                dataType: 'jsonp',
                jsonp: 'callback',
                success:function(data){
                    if (data.code == 0){
                        var gourl =  "<?php echo Url::toRoute(['app-tender/success','money' => $money]);?>";
                        window.location.href = gourl;
                    }else{
                        alert(data.message);
                    }
                }
            });
        }
    }

    function check_pwd(){
        if ('' == $.trim($('#password').val())){
            alert('密码不能为空');
            return false;
        }
        var password_reg = /^[0-9]{6}$/;
        if (!password_reg.test($('#password').val())){
            alert('请输入6位交易密码');
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