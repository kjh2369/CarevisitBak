<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_body_header.php');

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}

	$today = Date('Y-m-d');
?>
<script type="text/javascript" src="./result.js"></script>
<script type="text/javascript">
	var timer = null;
	var timeCnt = 0;
	var limitSec = 300;

	$(document).ready(function(){
		//lfGetData();
		try{
			__init_object(document.getElementById('txtDate'));
		}catch(e){
		}
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./result_search.php'
		,	data:{
				'date':$('#txtDate').val()
			}
		,	beforeSend:function (){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function(){
				alert('error');
			}
		}).responseXML;
	}

	function lfTimer(){
		var mode = $('#btnAutoRun').attr('mode');
		var today = getToday();

		if (mode == 'STOP'){
			$('#btnAutoRun').attr('mode','START').text('자동조회중지('+limitSec+'초후 새로고침)');
			$('#txtDate').attr('disabled',true).val(today);
			$('#btnNormalRun').attr('disabled',true);
			$('#btnResultSearch').attr('disabled',true);
			lfGetData();
			timer = setInterval('lfAutoRun()', 1000);
		}else{
			$('#btnAutoRun').attr('mode','STOP').text('자동조회(5분간격)실행');
			$('#txtDate').attr('disabled',false);
			$('#btnNormalRun').attr('disabled',false);
			$('#btnResultSearch').attr('disabled',false);
			clearInterval(timer);
			timer = null;
		}
	}

	function lfAutoRun(){
		if (timeCnt >= limitSec){
			lfGetData();
			timeCnt = 0;
		}else{
			$('#btnAutoRun').text('자동조회 중지('+(limitSec - timeCnt)+'초후 새로고침)');
			timeCnt ++;
		}
	}
</script>
<div class="title title_border">당일실적(공단)</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">일자</th>
			<td><input id="txtDate" name="txtDate" type="text" value="<?=$today;?>" class="date"></td>
			<td class="left last">
				<span class="btn_pack m"><button id="btnAutoRun" onclick="lfTimer();" mode="STOP">자동조회(5분간격)실행</button></span>
				<span class="btn_pack m"><button id="btnNormalRun" onclick="lfGetData($('#txtDate').val());">수동조회</button></span>
				<span class="btn_pack m"><button id="btnResultSearch" onclick="lfSearch();">결과조회</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="60px">
		<col width="50px" span="2">
		<col width="70px">
		<col width="110px">
		<col width="1px">
		<col width="50px" span="2">
		<col width="60px">
		<col width="110px">
		<col width="50px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head" rowspan="2">서비스</th>
			<th class="head" colspan="4">케어비지트</th>
			<th class="head" rowspan="2"></th>
			<th class="head" colspan="4">공단</th>
			<th class="head" rowspan="2">상태</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">시작</th>
			<th class="head">종료</th>
			<th class="head">수급자</th>
			<th class="head">요양보호사</th>
			<th class="head">시작</th>
			<th class="head">종료</th>
			<th class="head">수급자</th>
			<th class="head">요양보호사</th>
		</tr>
	</thead>
	<tbody id="tbodyList">
		<tr>
			<td class="center last" colspan="20">::검색된 데이타가 없습니다.::</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<input id="orgNo" type="hidden" value="<?=$_SESSION['userCenterCode'];?>">
<?
	include_once('../inc/_body_footer.php');
	include_once('../inc/_footer.php');
?>