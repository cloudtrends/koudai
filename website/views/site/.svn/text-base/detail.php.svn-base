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
                <div class="font1 detail_invest">账户余额</div>
                <div><span class="detail_project_box_text">0</span><span class="font1">元</span></div>
            </div>
            <div id="detail_progress">
               
                    <div class="detail_r_text" id="detail_r_text1">投资中</div>
                    <div class="detail_r_text" id="detail_r_text2">已满款</div>
                    <div class="detail_r_text" id="detail_r_text3">还款中</div>
                    <div class="detail_r_text" id="detail_r_text4">已还款</div>
                
            </div>
            <div id="detail_investlist">
                <div class="detail_r_text">投资明细</div>
                <div id="detail_investlist_title">
                    <span class="fl font1">投资人</span> 
                    <span class="fr font1">金额(元)&nbsp;&nbsp;&nbsp;</span>
                </div>
                <div id="detail_invest_log">
                </div>
                <div id="pages_info" class="paging">

                </div>
            </div>
            <div class="clear"></div>
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
              if(data.project.status == 3){
                  $("#detail_progress").attr("style"," background: #FFFFFF url(../image/site/progress-1.jpg) no-repeat center;");
                   $("#detail_r_text1").attr("style","color:#fd5353;");
              }else if(data.project.status == 5){
                  $("#detail_progress").attr("style"," background: #FFFFFF url(../image/site/progress-2.jpg) no-repeat center;");
                   $("#detail_r_text2").attr("style","color:#fd5353;");
              }else if(data.project.status == 7){
                  $("#detail_progress").attr("style"," background: #FFFFFF url(../image/site/progress-3.jpg) no-repeat center;");
                   $("#detail_r_text3").attr("style","color:#fd5353;");
              }else if(data.project.status == 8){
                  $("#detail_progress").attr("style"," background: #FFFFFF url(../image/site/progress-4.jpg) no-repeat center;");
                   $("#detail_r_text4").attr("style","color:#fd5353;");
              }
            }
           }
        });

        getInvestList(page);
    });

    var page = 1;
    var pageSize = 10;
    function getInvestList(page){
        var url2 = "<?php echo ApiUrl::toRoute(['project/invest-list'],true); ?>";
        var html2 ='';
        $.ajax({
            url : url2,
            type: 'GET',
            dataType: 'jsonp',
            jsonp: 'callback',
            data : {id : id,page:page,pageSize:pageSize},
            success:function(data){
                if( data.code == 0){
                     if (data.invests.length == 0){
                         html2 = '<div align="center">暂无数据</div>';
                     }
                    html2 +='<table  cellpadding=0 cellspacing=0>';
                    $.each(data.invests,function(index,value){
                        html2 +='<tr >';
                        html2 +='<td class="font1" style="width:160px; height:20px;">'+value.username+'</td>';
                        html2 +='<td rowspan="2" class="font1">'+value.invest_money+'</td>';
                        html2 +='</tr>';
                        html2 +='<tr>';
                        html2 +='<td class="font1 color3">'+StrToTime(value.created_at)+'</td>';
                        html2 +='</tr>';
                        html2 +='<tr  colspan="2" style="height: 10px;">';
                        html2 +='</tr>';
                    });
                    html2 +='</table>';
                    $('#detail_invest_log').html(html2);

                    var ajaxpage ="<?php echo Url::toRoute(['site/ajaxpages']);?>";
                    $.ajax({
                        url :ajaxpage,
                        type: 'POST',
                        dataType: 'json',
                        data: {pages:data.pages.totalCount,cur:page,pageSize:pageSize,methodName:'getInvestList',hiddenNumber:'1'},
                        success:function(data){
                            $('#pages_info').html(data);
                        }
                    });
                }else{
                    $('#detail_invest_log').html('<div align="center">数据加载失败</div>');
                }
            }
        });
    }

    function StrToTime(nS) {
		return (new Date(parseInt(nS) * 1000).getFullYear())+'.'
		+( ((new Date(parseInt(nS) * 1000).getMonth())+1) <10 
			? '0'+((new Date(parseInt(nS) * 1000).getMonth())+1) 
			: ((new Date(parseInt(nS) * 1000).getMonth())+1) )+'.'
		+( (new Date(parseInt(nS) * 1000).getDate()) <10 
			? '0'+(new Date(parseInt(nS) * 1000).getDate()) 
			: (new Date(parseInt(nS) * 1000).getDate()) );
	}
</script>