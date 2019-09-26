<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_page_list.php");
	include_once("../inc/_body_header.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	if ($gDomain == 'kdolbom.net'){
		include('../inc/_error.php');
		include_once("../inc/_body_footer.php");
		include_once("../inc/_footer.php");
		exit;
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		setTimeout('lfSearch()',200);
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url	:'./change_request_search.php'
		,	data:{

			}
		,	beforeSend:function(){
				var left = ($(document).width() - 200) / 2;
				var top  = ($(document).height() - 20) / 2;

				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:'+top+'px; left:'+left+'px; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function (html){
				$('#tbodyList').html(html);
				$('#tempLodingBar').remove();
			}
		,	error:function (request, status, error){
				alert('[ERROR]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
</script>
<div class="title title_border">일정변경요청조회</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="50px">
		<col width="90px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">구분</th>
			<td class="">
				<select id="cboGbn" style="width:auto;">
					<option value="1">전체</option>
					<option value="2" selected>미처리건</option>
					<option value="3">미전송건</option>
					<option value="4">처리완료</option>
					<option value="5">전송완료</option>
				</select>
			</td>
			<th class="center">자동전송여부</th>
			<td class="">
				<select id="cboSendYn" style="width:auto;">
					<option value="Y">자동전송</option>
					<option value="N" selected>수동전송</option>
				</select>
			</td>
			<td class="left last">
				<span class="btn_pack m"><button type="button" onclick="lfSearch();">새로고침</button></span>
				<span class="btn_pack m"><button type="button" onclick="">수동전송</button></span>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="70px">
		<col width="100px">
		<col width="40px">
		<col width="60px">
		<col width="100px">
		<col width="60px" span="3">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head" rowspan="2">No</th>
			<th class="head">계획시간</th>
			<th class="head" rowspan="2">변경전수가</th>
			<th class="head" rowspan="2">요청<br>구분</th>
			<th class="head">시작시간</th>
			<th class="head" rowspan="2">변경후수가</th>
			<th class="head" rowspan="2">요청시간</th>
			<th class="head" rowspan="2">결과시간</th>
			<th class="head" rowspan="2">전송시간</th>
			<th class="head last" rowspan="2">비고</th>
		</tr>
		<tr>
			<th class="head">수급자명</th>
			<th class="head">종료시간</th>
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
<?
	include_once("../inc/_body_footer.php");
	include_once("../inc/_footer.php");
?>