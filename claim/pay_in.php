<?
	include_once("../inc/_header.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");
	include_once("../inc/_body_header.php");

	$orgNo	= $_SESSION['userCenterCode'];

	$sql = 'SELECT	val1
			FROM	cv_claim_set
			WHERE	gbn = \'01\'';

	$claimDt = $conn->get_data($sql);

	$sql = 'SELECT	val1, val2
			FROM	cv_claim_set
			WHERE	gbn = \'02\'';

	$row = $conn->get_array($sql);

	$bankDt = $row['val1'];
	$bankTime = $row['val2'];

	Unset($row);
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
			lfSearch();
		});
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./pay_in_search.php'
		,	data:{
				'fromDt':$('#txtFromDt').val()
			,	'toDt':$('#txtToDt').val()
			,	'outStat':$('#cobOutStat').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_LIST').html(html);
				$('#tempLodingBar').remove();
			}
		,	error: function (request, status, error){
				alert('[ERROR No.02]'
					 +'\nCODE : ' + request.status
					 +'\nSTAT : ' + status
					 +'\nMESSAGE : ' + request.responseText);
			}
		});
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">사용료 납부내역</div>
	<div style="float:left; width:auto; margin-left:50px; color:000000;">※CMS(<?=$myF->dateStyle($claimDt, 'KOR');?>), 무통장(<?=$myF->dateStyle($bankDt, 'KOR').' '.$myF->timeStyle($bankTime);?>)의 입금분이 반영되었습니다.</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="177px">
		<col width="70px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">검색기간</th>
			<td><input id="txtFromDt" type="text" value="" class="date"> ~ <input id="txtToDt" type="text" value="" class="date"></td>
			<th class="center">출금상태</th>
			<td>
				<select id="cobOutStat" style="width:auto;">
					<option value="">전체</option>
					<option value="1">출금성공</option>
					<option value="2">출금실패</option>
				</select>
			</td>
			<td class="left last">
				<div style="float:left; width:auto;"><span class="btn_pack m"><button onclick="lfSearch();">조회</button></span></div>
			</td>
		</tr>
	</tbody>
</table>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="70px">
		<col width="80px">
		<col width="60px">
		<col width="50px">
		<col width="70px">
		<col width="100px">
		<col width="50px">
		<col width="110px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">청구일자</th>
			<th class="head">청구금액</th>
			<th class="head">입금일자</th>
			<th class="head">시간</th>
			<th class="head">구분</th>
			<th class="head">금액</th>
			<th class="head">상태</th>
			<th class="head">은행명</th>
			<th class="head">계좌번호</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody id="ID_LIST"></tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<?
	include("../inc/_body_footer.php");
	include("../inc/_footer.php");
?>