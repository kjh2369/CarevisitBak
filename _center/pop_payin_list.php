<?
	include_once('../inc/_header.php');
	include_once('../inc/_myFun.php');

	$orgNo = $_GET['orgNo'];

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
		$('#ID_BODY').height(__GetHeight($('#ID_BODY')));
		lfSearch();
	});

	function lfSearch(){
		$.ajax({
			type:'POST'
		,	url:'./center_PAY_IN_LIST_search.php'
		,	data:{
				'orgNo':'<?=$orgNo;?>'
			,	'drawMode':'ALL'
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(html){
				$('#ID_BODY tbody').html(html);
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

	function lfModify(obj, orgNo, date, seq){
		var objModal = new Object();

		objModal.result = false;

		showModalDialog('./pop_payin_remark.php?orgNo='+orgNo+'&date='+date+'&seq='+seq, objModal, 'dialogWidth:400px; dialogHeight:300px; dialogHide:yes; scroll:no; status:yes');

		if (objModal.result){
			$(obj).text(objModal.remark);
		}
	}
</script>
<div class="title title_border">
	<div style="float:left; width:auto;">입금내역</div>
	<div style="float:left; width:auto; margin-left:50px; color:000000;">※CMS(<?=$myF->dateStyle($claimDt, 'KOR');?>), 무통장(<?=$myF->dateStyle($bankDt, 'KOR').' '.$myF->timeStyle($bankTime);?>)의 입금분이 반영되었습니다.</div>
</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="40px">
		<col width="80px">
		<col width="90px">
		<col width="70px">
		<col width="70px">
		<col width="60px">
		<col width="60px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="head">No</th>
			<th class="head">기관기호</th>
			<th class="head">기관명</th>
			<th class="head">청구일자</th>
			<th class="head">입금일자</th>
			<th class="head">시간</th>
			<th class="head">입금구분</th>
			<th class="head">입금금액</th>
			<th class="head">출금상태</th>
			<th class="head">출금은행</th>
			<th class="head">입금은행</th>
			<th class="head last">비고</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="center top last" colspan="12">
				<div id="ID_BODY" style="overflow-x:hidden; overflow-y:scroll; height:100px;">
					<table class="my_table" style="width:100%;">
						<colgroup>
							<col width="40px">
							<col width="75px">
							<col width="85px">
							<col width="70px">
							<col width="70px">
							<col width="60px">
							<col width="60px">
							<col width="65px">
							<col width="65px">
							<col width="65px">
							<col width="65px">
							<col>
						</colgroup>
						<tbody></tbody>
					</table>
				<div>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>