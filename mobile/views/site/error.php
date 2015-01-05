<?php
use yii\helpers\Url;
?>

<link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/tender.css">
<body>
<div data-role="page">
    <div data-role="content" id="mcontent">

        <div class="ctitle">
           页面出错咯<br>喝杯茶休息休息！
        </div>
        <div id="mbutton">
            <a href="javascript:go_home();" data-role="button" id="red_btn" >返回首页</a>
        </div>
    </div>

    <div id="afooter">
        <div class="bimage"></div>
    </div>
</div>


</body>

<script>

    function go_home()
    {
        window.location.href = "<?php echo Url::toRoute(['site/index']); ?>";
    }

</script>

