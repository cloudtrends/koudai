<?php
use yii\helpers\Url;
use website\components\ApiUrl;
?>
<head><title><?php echo $title; ?></title></head>
<body>
	<div id="index_wrap">
		<div id="scroll">
			<div class="content">
				<div class="scroll_form alignright">
					<div id="title">
						<span class="font3 color8 first_child">快速注册</span>
						<span class="font1 color5">已有账号？</span>
						<span><a href="<?php echo Url::toRoute(['site/login']); ?>" class="font1 color7">登录</a></span>
					</div>
					<form method="post" action="" class="clear_inline_block">
						<span class="font1 color5">手机号</span><input class="scroll_input bg_input" type="text" name="phone"/><br>
						<span class="font1 color5">手机验证码</span><input class="scroll_input scroll_input_width bg_input" type="text" name="code"/>
						<a href="###"><div class="scroll_getcode bg_button aligncenter font11 color4">重新发送</div></a><br>
						<span class="font1 color5">创建密码</span><input class="scroll_input bg_input" type="text" name="cre_password"/><br>
						<span class="font1 color5">确认密码</span><input class="scroll_input bg_input" type="text" name="password"/><br>
						<a href="###"><div class="scroll_button bg_button aligncenter font4 color4" name="i_r_submit">立即注册</div></a>
					</form>
				</div>
			</div>
		</div>
		<div id="kdb">
			<div class="content">
				<div class="kdb_video">
					<!-- 
					当前，video 元素支持三种视频格式： 
					格式 IE Firefox Opera Chrome Safari 
					Ogg No 3.5+ 10.5+ 5.0+ No 
					MPEG 4 9.0+ No No 5.0+ 3.0+ 
					WebM No 4.0+ 10.6+ 6.0+ No
					-->
					<video height="230" width="305" controls>  
						<source src="<?php echo $this->absBaseUrl; ?>/video/12-29kdb.mp4" type="video/mp4"/>
						<embed SRC="<?php echo $this->absBaseUrl; ?>/video/12-29kdb.avi" type="audio/x-pn-realaudio-plugin" autostart=false loop=false width=305 height=230></embed>
					</video>
				</div><br>
				<a href="###"><div class="kdb_button bg_button aligncenter font4 color4">马上投资</div></a>
			</div>
		</div>
		<div id="projectlist">
			<div class="content">
				<span class="font2 color2">定期 ▪ 投资项目 </span>
				<div id="p2p_list"></div>
				<a href="<?php echo Url::toRoute(['site/list']); ?>" class="f_right font1 color10">查看全部项目</a>
			</div>
			<div class="clear"></div>
		</div>
		<div id="investlist">
			<div class="content">
				<div id="invest_log"></div>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	// $(function(){
		
	// });

	var pageSize1 = 6;
	var url1 = "<?php echo ApiUrl::toRoute('project/p2p-list'); ?>?page=1&pageSize="+pageSize1;
	var html ='';
	$.ajax({
		url : url1,
		type: 'POST',
		dataType: 'jsonp',
		jsonp: 'callback',
		jsonpCallback:"flightHandler",
		success:function(data){
			if( data.code == 0){
				$.each(data.projects,function(index,value){
					html +='<div class="p2p_details">';
					html +='<div class="details_content">';
					html +='<p class="font2 color2 first_child">'+value.name+'</p>';
					html +='<p class="font1 color2">期限：'+value.period+(value.is_day==1 ? '天':'月')+'&nbsp;&nbsp;|&nbsp;&nbsp;起投资金：'+value.min_invest_money+'元</p>';
					html +='<div class="l_apr">';
					html +='<p class="font1 color6">预期年化<br><br></p>';
					html +='<p><span class="font5 color10">'+value.apr+'</span><span class="font1 color10">%</span></p>';
					html +='</div><div id="progress"> <div class="progress_in" style="width:'+ value.success_percent +'%;"></div></div><div class="summary">';
					html +='<span class="font1 color6">融资进度 </span>';
					html +='<span class="font1 color10">'+ value.success_percent +'%</span>';
					html +='<span class="font1 color6 f_right">'+ value.success_number+'人已投</span>';
					html +='</div><div class="font1 color6">'+value.summary+'</div></div><a href="###"><div class="bg_button2 aligncenter font4 color4">马上投资</div></a></div>';
				});
				$('#p2p_list').html(html);
			}
		}
	});

	var pageSize2 = 5;
	var url2 = "<?php echo ApiUrl::toRoute('project/invest-log'); ?>?page=1&pageSize="+pageSize2;
	var html2 ='';
	$.ajax({
		url : url2,
		type: 'POST',
		dataType: 'jsonp',
		jsonp: 'callback',
		jsonpCallback:"flightHandler",
		success:function(data){
			if( data.code == 0){
				html2 +='<table class="invest_details_table" cellpadding=0 cellspacing=0>';
				html2 +='<tr style="border-bottom: 1px solid #EAEAEA;">';
				html2 +='<td style="width:100px; height: 50px;">投资人</td>';
				html2 +='<td style="text-align:center;">投资项目</td>';
				html2 +='<td>投资金额</td>';
				html2 +='<td style="width:90px;">投资日期</td>';
				html2 +='</tr>';
				$.each(data.invests,function(index,value){
					html2 +='<tr class="invest_details_td">';
					html2 +='<td style="height:45px;">'+value.username+'</td>';
					html2 +='<td style="text-align:center;">'+value.project_name+'</td>';
					html2 +='<td>'+value.invest_money+'元</td>';
					html2 +='<td>'+StrToTime(value.created_at)+'</td>';
					html2 +='</tr>';
				});
				html2 +='</table>';
				$('#invest_log').html(html2);
			}
		}
	});

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
