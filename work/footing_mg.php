<?
	include_once('../inc/_header.php');
	include_once("../inc/_login.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year	= Date('Y');
	$month	= IntVal(Date('m'))=='1' ? IntVal(Date('m')) : IntVal(Date('m'))-1;
	$popYn  = $_GET['popYn'];
	
	
?>
<script type="text/javascript">
	$(document).ready(function(){
		lfSearch();
	});

	
	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./footing_mg_search.php'
		,	data:{
				'year'	:$('#lblYYMM').attr('year')
			,	'month'	:$('#lblYYMM').attr('month')
			}
		,	beforeSend:function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();
				$('#beforeMonth').text('('+$('#lblYYMM').attr('month')+'월)');
				if($('#lblYYMM').attr('month') == '12'){
					$('#afterMonth').text('('+(parseInt($('#lblYYMM').attr('month'))-11)+'월)');
				}else {
					$('#afterMonth').text('('+(parseInt($('#lblYYMM').attr('month'))+1)+'월)');
				}
			}
		,	complete: function(){
			}
		,	error:function(request, status, error){
				$('#tempLodingBar').remove();

				alert('[ERROR No.03]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);

				return false;
			}
		}).responseXML;
	}
	
	function setCookie(name, value, expiredays ){
		var todayDate = new Date();
			todayDate.setDate( todayDate.getDate() + expiredays );

		document.cookie = name + "=" + escape( value ) + "; path=/; expires=" + todayDate.toGMTString() + ";"
	}

	function end(){
		var f = document.f;

		if (f.check.checked){
			setCookie('FOOT_POPUP','DONE',1);
		}

		self.close();
	}

	
</script>

<form name="f" method="post" action="">
<div style="float:left;" class="title title_border">진도 관리<span style="color:red;">(※ 2018.08.21부터 적용됩니다.)</span>
</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="100px">
		<col width="550px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">년월</th>
			<td class="last">
				<div>
					<div style="float:left; width:auto; margin-left:5px; margin-top:3px;"><img src="../image/btn/btn_pre_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),-1,'lfSearch()');" onmouseover="this.src='../image/btn/btn_pre_over.gif';" onmouseout="this.src='../image/btn/btn_pre_out.gif';"></div>
					<div style="float:left; width:auto; padding-left:5px; padding-right:5px; font-weight:bold;" id="lblYYMM" year="<?=$year;?>" month="<?=$month;?>"><?=$year;?></div>
					<div style="float:left; width:auto; margin-top:3px;"><img src="../image/btn/btn_next_out.gif" style="cursor:pointer;" onclick="__moveYear($('#lblYYMM'),1,'lfSearch()');" onmouseover="this.src='../image/btn/btn_next_over.gif';" onmouseout="this.src='../image/btn/btn_next_out.gif';"></div>
				</div>
			</td>
			<td class="last" style="padding-top:1px;"><?echo $myF->_btn_month($month,'__moveMonth(',',$("#lblYYMM"),"lfSearch()")');?></td>
			<td class="right last"></td>
		</tr>
	</tbody>
</table>
<div style="width:100%; margin:10px;">
<table class="my_table my_border_blue" style="width:100%;">
	<colgroup>
		<col width="200px">
		<col width="70px">
		<col width="50px">
		<col width="150px">
		<col width="150px">
		<col >
	</colgroup>
	<thead>
		<tr>
			<th class="head">업무명</th>
			<th class="head">바로가기</th>
			<th class="head">실행여부</th>
			<th class="head">최종일시<span id="beforeMonth" style="color:red; font-weight:bold;"></span></th>
			<th class="head">최종일시<span id="afterMonth" style="color:red; font-weight:bold;"></span></th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="ID_LIST"></tbody>
</table>
</div>
<?
if($popYn=='Y'){ ?>
	<div style="position:absolute; top:531px; width:100%; color:WHITE; background-color:#000;"><label><input type="checkbox" name="check" value="checkbox"  style="border:0;" onClick="end();"/>오늘 하루동안 열지않기</label></div>
	<div style="position:absolute; top:536px; left:90%; background-color:#000;"><a href="#" onclick="end();"><span style="color:WHITE;">닫기</span></a></div><?
} ?>
</form>
<?
	include_once('../inc/_footer.php');
?>