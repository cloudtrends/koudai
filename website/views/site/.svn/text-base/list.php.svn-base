<?php
use website\components\ApiUrl;
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
        <div id="list-search" name="list-search">
            <?php foreach ($search as $search_key => $search_val): ?>
                <div class="list-search-s">
                    <div class="list-search-t"><?php echo $title_ch[$search_key] ?>:</div>
                    <?php foreach ($search_val as $search_val_key => $search_val_val): ?>
                        <?php if($search_val_key == 0 ):?>
                            <div id="<?php echo $search_key?>_<?php echo $search_val_key?>" style="background:#fd5353;border-radius:5px;" class="<?php echo $search_key?> list-search-n"><a style="color:#fff;" href="javascript:search('<?php echo $search_key; ?>','<?php echo $search_val_key; ?>')"><?php echo $search_val_val; ?></a></div>
                        <?php else:?>
                            <div id="<?php echo $search_key?>_<?php echo $search_val_key?>" class="<?php echo $search_key?> list-search-n"><a href="javascript:search('<?php echo $search_key; ?>','<?php echo $search_val_key; ?>')"><?php echo $search_val_val; ?></a></div>
                        <?php endif;?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
                    
        <div id="list_info" >
            
        </div>

        <div class="clear"></div>
        <div class="paging" id="footer_info">            
        </div>
    </div>
</div>
</div>
</body>
<script type="text/javascript">
     //
  $(document).ready(function(){
      search();
   });      

var params = new Array();
 function search(key,sth){
     if ('page' == $.trim(key)){
         location.href = "#list-search";
     }

    
    var idx = key+'_'+sth;
    
    $("."+key).attr("style","background:#fff; float:left;width:80px;height:30px;text-align:center; margin-left: 10px;");
     $("."+key +">a").attr("style","color:#000;");
    $("#"+idx).attr("style","background:#fd5353;border-radius:5px;");
     $("#"+idx +">a").attr("style","color:#fff;");
    
     params[key] = sth;
     var paramsStr = '';
     for (index in params){
         if ('0' != $.trim(params[index])){
             paramsStr += "&" + index + "=" + params[index];
         }
     }

     if (params['page'] == null || 'page' != $.trim(key) ){
         params['page'] = 1;
     }


      var url = "<?php echo ApiUrl::toRoute(['project/website-list'],true) ?>"+"?1"+paramsStr;
      $.ajax({          
                url : url,
                type: 'POST',        
                dataType: 'jsonp',
                jsonp: 'callback',
                success:function(data){
                 if (data.code == 0){
                     $('#list_info').html('<div align="center">数据加载中...</div>');
                     var html ='';
                     if (data.projects.length == 0){
                         html = '<div align="center">暂无数据</div>';
                     }

                     if (data.projects.length > 0){
                         $.each(data.projects,function(index,value){
                             html +='<div class="list_box" >';
                             html +='<div class="details">';
                             html +='<p class="name">'+value.name+'</p>';
                             html +='<p class="period">期限：'+value.period+(value.is_day==1 ? '天':'月')+'&nbsp;&nbsp;|&nbsp;&nbsp;起投资金：'+value.min_invest_money+'元</p>';
                             html +='<div class="l_apr">';
                             html +='<p class="l_apr_text">预期年化</p>';
                             html +='<p><span class="l_apr_num">'+value.apr+'</span><span class="l_apr_symbol">%</span></p>';
                             html +='</div><div class="progress"> <div id="progress_in" style="width:'+ value.success_percent +'%;"></div></div><div>';
                             html +='<span class="progress_l">融资进度</span>';
                             html +='<span class="progress_m">'+ value.success_percent +'%</span>';
                             html +='<span class="progress_r">'+ value.success_number+'人已投</span>';
                             html +='</div><div class="summary">'+value.summary+'</div></div><div class="l_button"><a href="'+'<?php echo Url::toRoute(['site/detail']); ?>'+'?id='+ value.id +'">立即投标</a></div></div>';
                         });
                     }

                     $('#list_info').html(html);

                      var url2 ="<?php echo Url::toRoute(['site/ajax']);?>";
                      $.ajax({
                            url :url2,
                            type: 'POST',
                            dataType: 'json',
                            data: {url:url2 , pages:data.pages.totalCount,cur:params['page']},
                            success:function(data){
                                $('#footer_info').html(data);
                            }
                        });

                    bindDetail();
                 }else{
                     $('#list_info').html('<div align="center">数据加载失败</div>');
                 }
               }
            });
 }
        function bindDetail()
        {
            $(".details").mousemove(function () {               
                 $(this).attr("style","border-color:#fd5353");
            });
            $(".details").mouseout(function () {
                $(this).attr("style","border-color:#e7e7e7");
            });
        }
        
       
        
</script>