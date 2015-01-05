<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;
use backend\components\widgets\ActiveForm;
?>

<link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/web.css">
<head><title><?php echo $title; ?></title></head>
<body>
<div id="list">
<div data-role="page">
    <div data-role="content" id="lcontent">
        <?php foreach ($projects as $value): ?>
        <div class="list_box" >
            
            <div id="details">
                <p class="name"><?php echo $value['name']; ?></p>
                <p class="period">期限：<?php echo $value['period']. ' ' . ( $value['is_day'] ? '天' : '月'); ?> | 起投资金:<?php echo $value['min_invest_money']; ?>元</p>
                <div class="l_apr">
                    <p class="l_apr_text">预期年化</p>
                    <p><span class="l_apr_num"><?php echo $value['apr']; ?></span><span class="l_apr_symbol">%</span></p>
                </div>
                <div class="progress">
                    <div id="progress_in">
                        
                    </div>
                </div>
                <div>
                    <span class="progress_l">融资进度</span>
                    <span class="progress_m"><?php echo $progress=100*$value['success_money']/$value['total_money']; ?> %</span>
                    <span class="progress_r"><?php echo $value['success_number']; ?>人已投</span>                   
                </div>
                <div class="summary">
                    <?php echo $value['summary']; ?>
                </div>
            </div>
            <div class="l_button">
              
                <a href="#">立即投标</a>
            </div>
            
            
        </div>   
        
        <?php endforeach; ?>
        <div class="clear"></div>
        <div class="paging">
        <?php echo LinkPager::widget(['pagination' => $pages]); ?>
        </div>
    </div>
</div>
</div>
</body>
<script type="text/javascript">
     <?php foreach ($projects as $value): ?>
  $(document).ready(function(){
        var progress = '<?php echo 100*$value['success_money']/$value['total_money'];?>';
//        alert(progress);
       document.getElementById('progress_in').style.width = progress+'%';
                    });
 $(function () {
            $("#details").mousemove(function () {
                 $("#details").attr("style","border-color:#fd5353");
            });
            $("#details").mouseout(function () {
                $("#details").attr("style","border-color:#e7e7e7");
            });
        });      
 <?php endforeach; ?>
</script>