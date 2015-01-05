<?php
use yii\helpers\Url;
use mobile\components\ApiUrl;
?>
 <link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/tender.css">
<body style="width: 100px">
<div data-role="page">
    <div data-role="content" id="mcontent">
        <div id="t_header1">
            <h2>口袋宝</h2>
            <div class="t_text">
            <div class="t_ltext">预期年化:  <?php echo $apr?>% </div>   <div class="t_rtext">理财期限:  随取随存</div>
            </div>
            <div class="t_text">
            <div class="t_ltext">剩余金额:  <?php echo $remain_money?> </div>  <div class="t_rtext">起投金额:  <?php echo $min_invest_money?>元</div>
            </div>
        </div>
        <div id="asdf">
        <input type="text" name="money" id="money" placeholder="请输入购买金额" value="<?php echo $min_invest_money?>">
        </div>
        <div id="t_text2">
            口袋宝每日交易限额<?php echo $daily_withdraw_limit?>元
        </div>
        <div id="mbutton">
            <a href="javascript:f_submit();" data-role="button" id="login" >立即买入</a>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    var money = 0;
    function f_submit(){
        if (ch_money()){
            var ajaxurl = "<?php echo ApiUrl::toRoute(['koudaibao/invest-order'],true);?>";
            $.ajax({
                url : ajaxurl,
                type: 'POST',
                data : {money:money},
                dataType: 'json',
                //jsonp: 'callback',
                success:function(data){
                    if(data.code == 0){
                        var gourl =  "<?php echo Url::toRoute(['app-tender/confirm']);?>"+"?orderid="+data.order_id+"&money="+money;
                        window.location.href = gourl;
                    }else{
                        alert('下单失败');
                    }
                }
            });
        }
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
            alert('金额不能为零');
            return false;
        }
        if (money < minmoney){
            alert('金额不能少于起投金额');
            return false;
        }

        if ( money > remainmoney){
            alert('金额大于剩余金额');
            return false;
        }
        if (money > maxmoney){
            alert('金额不能大于当日交易金额');
            return false;
        }
        return true;
    }
</script>