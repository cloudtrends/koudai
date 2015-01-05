<?php
use yii\helpers\Url;
use mobile\components\ApiUrl;
?>
<head><title>投标</title></head>
<link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/tender.css">

<body>
<div data-role="page">
    <div data-role="content">
        <label ><div class="t_text">口袋宝</div></label>
        <label ><div class="t_ltext" >预期年化: <?php echo $apr?>% </div></label>
        <label ><div class="t_rtext" >起投金额: <?php echo $min_invest_money?>元</div></label>
        <label ><div class="t_ltext" >剩余金额: <?php echo $remain_money?> </div></label>
        <label ><div class="t_rtext" >理财期限: 随取随存 </div></label>
    </div>

    <input data-clear-btn="true" type="text" name="money" id="money" placeholder="请输入购买金额"  />

    <div id="t_text2">
        • 口袋宝每日交易限额<?php echo intval($daily_withdraw_limit)?>元
    </div>
    <div id="notification"></div>
    <div id="mbutton">
        <a href="javascript:f_submit();" data-role="button" id="red_btn" >立即买入</a>
    </div>

</div>

</body>
<script type="text/javascript">
    var money = 0;
    function f_submit(){
        var btn_id = '#red_btn';
        var btn_text = "立即买入";
        var a_js_var = "javascript:f_submit();";
        if (ch_money()){
            $.ajax({
                url : "<?php echo ApiUrl::toRoute(['koudaibao/invest-order'],true);?>",
                type: 'POST',
                data : {
                    money : money
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
                    if(data.code == 0){
                        window.location.href = "<?php echo Url::toRoute(['site/kdb-confirm']);?>"+"?orderid="+data.order_id+"&money="+money;;
                    }else{
                        var message;
                        if(data.message == "您还没有实名认证")
                        {
                            message ='您还没有实名认证 <a href="javascript:go_id_verify()">点我认证</a>';
                        }
                        else if(data.message == "您还没有绑定银行卡")
                        {
                            message ='您还没有添加银行卡 <a href="javascript:go_bank()">点我添加</a>';
                        }
                        else{
                            message = data.message;
                        }
                        $('#notification').html(message);

                    }
                }
            });
        }
    }

    function go_id_verify(){
        window.location.href = "<?php echo Url::toRoute(['site/information']);?>";
    }
    function go_bank(){
        window.location.href = "<?php echo Url::toRoute(['site/bank']);?>";
    }

    var reg = /[^/0-9.]/g;  //只能输入字母正则
    $("#money").keyup(function(){
        $(this).val($(this).val().replace(reg,''));
    }).bind("paste",function(){  //CTR+V事件处理
        $(this).val($(this).val().replace(reg,''));
    });

    function ch_money(){
        var maxmoney = Number('<?php echo $daily_withdraw_limit?>');
        var minmoney = Number('<?php echo $min_invest_money?>');
        var remainmoney = Number('<?php echo $remain_money?>');
        money = Number($.trim($('#money').val()));
        if ('' == money || 0 == money){
            $('#notification').html('金额不能为零');
            return false;
        }
        if (money < minmoney){
            $('#notification').html('金额不能少于起投金额');
            return false;
        }

        if ( money > remainmoney){
            $('#notification').html('金额大于剩余金额');
            return false;
        }
        if (money > maxmoney){
            $('#notification').html('金额不能大于当日交易金额');
            return false;
        }
        return true;
    }
</script>
