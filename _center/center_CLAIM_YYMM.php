<?
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$sql = 'SELECT	yymm
			FROM	cv_claim_yymm
			LIMIT	1';

	$yymm = $conn->get_data($sql);

	$sql = 'SELECT	val1
			FROM	cv_claim_set
			WHERE	gbn = \'01\'';

	$claimDt = $conn->get_data($sql);

	$sql = 'SELECT	val1, val2
			FROM	cv_claim_set
			WHERE	gbn = \'02\'';

	$row = $conn->get_array($sql);
	$bankDate = $row['val1'];
	$bankTime = $row['val2'];

	Unset($row);
?>
<script type="text/javascript">
	$(document).ready(function(){
		//lfSearch();
	});

	function lfApply(){
		if (!$('#txtClaimYymm').val()){
			alert('설정할 청구년월을 입력하여 주십시오.');
			$('#txtClaimYymm').focus();
			return;
		}

		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_apply.php'
		,	data:{
				'yymm':$('#txtClaimYymm').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
				}else{
					alert(result);
				}

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

	function lfApplyGbn(gbn){
		var val1 = '', val2 = '';

		if (gbn == '01'){
			if (!$('#txtClaimDt').val()){
				alert('CMS 입금반영일을 입력하여 주십시오.');
				$('#txtClaimDt').focus();
				return;
			}

			val1 = $('#txtClaimDt').val().split('-').join('');
		}else if (gbn == '02'){
			if (!$('#txtBankDt').val()){
				alert('무통장 입금반영일을 입력하여 주십시오.');
				$('#txtBankDt').focus();
				return;
			}

			if (!$('#txtBankTime').val()){
				alert('무통장 입금반영시간을 입력하여 주십시오.');
				$('#txtBankTime').focus();
				return;
			}

			val1 = $('#txtBankDt').val().split('-').join('');
			val2 = $('#txtBankTime').val().split(':').join('');
		}else{
			return;
		}

		$.ajax({
			type:'POST'
		,	url:'./center_<?=$menuId?>_set.php'
		,	data:{
				'gbn':gbn
			,	'val1':val1
			,	'val2':val2
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
				}else{
					alert(result);
				}

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

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="110px">
		<col width="50px">
		<col width="50px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">청구년월</th>
			<td class="" colspan="2">
				<input id="txtClaimYymm" type="text" class="yymm" value="<?=$myF->_styleYYMM($yymm);?>">
			</td>
			<td class="left last">
				<span class="btn_pack m"><button onclick="lfApply()">적용</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">CMS 입금반영일</th>
			<td class="" colspan="2">
				<input id="txtClaimDt" type="text" class="date" value="<?=$myF->dateStyle($claimDt);?>">
			</td>
			<td class="left last">
				<span class="btn_pack m"><button onclick="lfApplyGbn('01')">적용</button></span>
			</td>
		</tr>
		<tr>
			<th class="center">무통장 입금반영일</th>
			<td class="">
				<input id="txtBankDt" type="text" class="date" value="<?=$myF->dateStyle($bankDate);?>">
			</td>
			<td class="">
				<input id="txtBankTime" type="text" class="no_string" alt="time" value="<?=$myF->timeStyle($bankTime);?>">
			</td>
			<td class="left last">
				<span class="btn_pack m"><button onclick="lfApplyGbn('02')">적용</button></span>
			</td>
		</tr>
	</tbody>
</table>