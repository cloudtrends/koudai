<?php
use website\components\ApiUrl;
use yii\helpers\Url;
use yii\helpers\Html;


?>

<link rel="stylesheet" type="text/css" href="<?php echo $this->absBaseUrl; ?>/css/web.css">
<head><title><?php echo $title; ?></title></head>
<body>
    <div class="detail">
        <div data-role="content" id="dcontent">   
            <div id="detail_project">
                <div id="detail_project_name">
                    <span id="detail_project_name_l"></span>
                    <span id="detail_project_name_r"><span id="detail_project_name_r1"></span>人已投</span>
                </div>
                <div id="detail_project_m">
                    <div class="detail_project_box" id="detail_project_box1">
                        预期年化<br>
                        <span class="detail_project_box_text" id="detail_project_box_text1"></span><span class="font1">%</span>
                        
                    </div>
                    <div class="detail_project_box" id="detail_project_box2">
                        期限<br>
                         <span class="detail_project_box_text" id="detail_project_box_text2"></span><span class="font1" id="detail_project_box_text3"></span>
                        
                    </div>
                    <div class="detail_project_box" id="detail_project_box3">
                        起投金额<br>
                        <span class="detail_project_box_text" id="detail_project_box_text4"></span><span class="font1">元</span>
                    </div>
                </div>
                <div id="detail_project_progress">
                    <span class="detail_project_progress_font1">融资进度</span><span class="detail_project_progress_font2"><span id="detail_project_progress_text1"></span>%</span>&nbsp;
                    <span class="detail_project_progress_font1">剩余可投金额</span><span id="detail_project_progress_text2" class="detail_project_progress_font2"></span>&nbsp;
                    <span class="detail_project_progress_font1 fr">项目投资总额<span id="detail_project_progress_text3" class="detail_project_progress_font1 fr"></span></span>
                    <div id="detail_project_progress_out">
                        <div id="detail_project_progress_in">
                        
                        </div>
                    </div>
                </div>
                <div id="detail_project_text">
                    到期本息还款，募集成功后次日开始计息
                </div>
                <div id="desc">
                项目概述
                </div>
            </div>
            <div id="detail_invest">
                账户余额
            </div>
            <div id="detail_progress">
                项目进度
            </div>
        </div>
    </div>
</body>
<script type="text/javascript">
      
    var id = Number("<?php echo $id?>");
    
    var ajaxurl = "<?php echo ApiUrl::toRoute(['project/detail'],true); ?>";
   
    $(document).ready(function(){        
        $.ajax({
            url : ajaxurl,
            type: 'GET',
            dataType: 'jsonp',
            jsonp: 'callback',
            data : {
                    id : id
                },
            success:function(data)
            {
              if (data.code == 0){
                  var remaining =data.project.total_money -data.project.success_money;
                  $('#detail_project_name_l').html(data.project.name);
                  $('#detail_project_name_r1').html(data.project.success_number);
                  $('#detail_project_box_text1').html(data.project.apr);
                  $('#detail_project_box_text2').html(data.project.period);
                  $('#detail_project_box_text3').html(data.project.is_day==1 ? '天':'个月');
                  $('#detail_project_box_text4').html(data.project.min_invest_money);
                  $('#detail_project_progress_text1').html(data.project.success_percent);
                  $('#detail_project_progress_text2').html(remaining);
                  $('#detail_project_progress_text3').html(data.project.total_money);
                  $("#detail_project_progress_in").attr("style","width:"+ data.project.success_percent +"%;");
              }else{
                     alert("get data error");
                 }
            }
        });
    });
</script>