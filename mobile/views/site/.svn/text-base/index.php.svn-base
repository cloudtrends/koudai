<?php
use mobile\components\ApiUrl;
use yii\helpers\Url;
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/tender.css">
<head><title>口袋理财</title></head>
<body>

<div data-role="page">
    <div data-role="content" id="mcontent">
        <div id="mheader">
            <img src="<?php echo $this->absBaseUrl; ?>/image/logo.gif" width="200" height="53">
        </div>
        <div id="mheader2">
            <img src="<?php echo $this->absBaseUrl; ?>/image/download.gif" width="100">
        </div>
        <div class="ctitle">口袋宝</div>
        <div class="pocan">
            <canvas id="myCanvas" width="220" height="220">
                Your browser does not support the HTML5 canvas tag.
            </canvas>
            <canvas id="myCanvas1" width="220" height="220">
                Your browser does not support the HTML5 canvas tag.
            </canvas>
        </div>
        <div class="btext">
            <img src="<?php echo $this->absBaseUrl; ?>/image/icon1.gif" class="mimage" height="16">&nbsp<span class="mtext" id="m_money"></span>&nbsp;
            <img src="<?php echo $this->absBaseUrl; ?>/image/icon2.gif" class="mimage" height="16"><span class="mtext">&nbsp随存随取</span>&nbsp;
           <img src="<?php echo $this->absBaseUrl; ?>/image/icon3.gif" class="mimage" height="16"> <span class="mtext" id="completeness"></span>
        </div>
        <div id="mbutton">
            <a href="javascript:kdbInvest();" data-role="button" id="red_btn"  >马上赚钱</a>
        </div>

    </div>
</div>
</body>

<script type="text/javascript">
    
    $(document).ready(function(){
        var ajaxurl = "<?php echo ApiUrl::toRoute(['koudaibao/info'],true);?>";
        $.ajax({
            url : ajaxurl,
            type: 'GET',
            dataType: 'jsonp',
            jsonp: 'callback',
            success:function(data)
            {
                var completeness = 0
                
                if (data.code == 0){
                    if(data.info.total_money != 0){
                        completeness = 100 * (data.info.total_money - data.info.remain_money) / data.info.total_money;
                        if(completeness < 0){
                            completeness = 0;
                        }
                    }
                    $('#m_money').html( data.info.min_invest_money /100 +"元起购");
                    $('#completeness').html(  completeness.toFixed(2) +"%");
                     
                     
                     /** draw */
                    $("#mcontent").css("height",document.body.clientHeight);
                    var canvas = document.getElementById("myCanvas");
                    var canvas1 = document.getElementById("myCanvas1");
                    var txt=completeness.toFixed(2);
                    var txt1= data.info.apr;
                    var txt2="%";

                    var context = canvas.getContext('2d');
                    function drawProcess() {

                        context.lineWidth = 13;
                        context.strokeStyle = 'rgba(213,213,213,1)';
                        context.arc(110, 110, 95, 0, Math.PI*2, false);
                        //不关闭路径路径会一直保留下去，当然也可以利用这个特点做出意想不到的效果

                        context.stroke();

                        //在中间写字
                        context.font = "bold 3em Arial";
                        context.fillStyle = '#ee4741';
                        context.textAlign = 'center';
                        context.textBaseline = 'middle';
                        context.fillText(txt1, 100, 110);


                        //在中间写字
                        context.font = "bold 1em Arial";
                        context.fillStyle = '#ee4741';
                        context.textAlign = 'center';
                        context.textBaseline = 'middle';
                        context.fillText(txt2, 170, 115);



                    }
                    drawProcess();
                    var context1 = canvas1.getContext('2d');
                    var process=parseInt(txt);
                    function draw (){
                        context1.clearRect(0,0,400,400); // clear canvas
                        context1.lineWidth = 13;
                        context1.strokeStyle = 'rgba(253,83,83,1)';
                        context1.arc(110, 110, 95, 1.5*Math.PI, 1.5*Math.PI+Math.PI * 2 * process / 100, false);
                        //不关闭路径路径会一直保留下去，当然也可以利用这个特点做出意想不到的效果
                        context1.stroke();
                    }
                    draw();
                    // setInterval(draw,1000);
                     
                     
                }
            }
        });
    });
    function kdbInvest(){
       
        window.location.href = "<?php echo Url::toRoute(['site/kdb-invest']);?>";
    }

    
</script>