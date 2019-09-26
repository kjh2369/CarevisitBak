<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_login.php');
	include_once("../inc/_myFun.php");

	$code = $_SESSION['userCenterCode'];

	if ($type == '1'){
		$title = '수입내역등록';
	}else if ($type == '2'){
		$title = '수입내역수정';
	}else if ($type == '11'){
		$title = '지출내역등록';
	}else if ($type == '12'){
		$title = '지출내역수정';
	}else{
		include('../inc/_http_home.php');
		exit;
	}

	if ($type == '1' || $type == '2'){
		$gbn = 'I';
		$field = 'income';
	}else{
		$gbn = 'E';
		$field = 'outgo';
	}

	$today = Date('Y-m-d');
	$year  = Date('Y');
	$month = Date('m');
	$day   = Date('d');

	if ($type == '2'){
	}else{
		$sql = 'SELECT IFNULL(MAX(CAST(proof_no AS unsigned)),0) + 1
				  FROM center_'.$field.'
				 WHERE org_no     = \''.$code.'\'
				   AND proof_year = \''.$year.'\'';
		$proofNo = $conn->get_data($sql);

		if (StrLen($proofNo) < 5){
			for($i=StrLen($proofNo)+1; $i<=5; $i++){
				$proofNo = '0'.$proofNo;
			}
		}
	}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('#txtAmt').unbind('change').bind('change',function(){
			var liAmt = __str2num($(this).val());
			var liVat = 0;
			var liTot = 0;

			if ($('#optVatY').attr('checked')){
				liVat = Math.floor(liAmt / 10);
			}

			liTot = liAmt + liVat;

			$('#lblVat').text(__num2str(liVat));
			$('#lblTot').text(__num2str(liTot));
		});

		$('input:radio[id^="optVat"]').unbind('click').bind('click',function(){
			$('#txtAmt').change();
		});

		$('body').focus();

		__init_form(document.f);
	});

	function lfCategory(){
		var objModal = new Object();
		var url      = '../find/_find_ie.php';
		var style    = 'dialogWidth:900px; dialogHeight:500px; dialogHide:yes; scroll:no; status:no';

		objModal.result = false;
		objModal.gbn = '<?=$gbn;?>';

		window.showModalDialog(url, objModal, style);

		if (!objModal.result) return;

		$('#lblCd1').text(objModal.cd1);
		$('#lblNm1').text(objModal.nm1);
		$('#lblCd2').text(objModal.cd2);
		$('#lblNm2').text(objModal.nm2);
		$('#lblCd3').text(objModal.cd3);
		$('#lblNm3').text(objModal.nm3);
		$('.clsCateGbn').show();

		lfGetProofNo(objModal.cd1+objModal.cd2+objModal.cd3);
	}

	function lfGetProofNo(asCd){
		$.ajax({
			type :'POST'
		,	url  :'./acct_search.php'
		,	data :{
				'type':'<?=$type;?>_3'
			,	'gbn':'<?=$gbn;?>'
			,	'date':$('#regDt').val().split('-').join('')
			,	'itemCd':asCd
			}
		,	beforeSend:function(){
			}
		,	success:function(proofNo){
				$('#proofY').text($('#regDt').val().substring(0,4));
				$('#proofM').text($('#regDt').val().substring(5,7));
				$('#proofD').text($('#regDt').val().substring(8,10));
				$('#proofNo').text(proofNo);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfSave(){
		if (!$('#regDt').val()){
			alert('일자를 입력하여 주십시오.');
			$('#regDt').focus();
			return;
		}

		if (!$('#lblCd3').text()){
			alert('항목을 선택하여 주십시오.');
			lfCategory();
			return;
		}

		if (__str2num($('#txtAmt').val()) == 0){
			alert('금액을 입력하여 주십시오.');
			$('#txtAmt').focus();
			return;
		}

		var lsProofY  = $('#proofY').text();
		var lsProofM  = $('#proofM').text();
		var lsProofD  = $('#proofD').text();
		var lsProofNo = $('#proofNo').text();

		$.ajax({
			type :'POST'
		,	url  :'./acct_save.php'
		,	data :{
				'type':'<?=$type;?>'
			,	'entDt':$('#txtEntDt').val()
			,	'seq':$('#txtSeq').val()
			,	'regDt':$('#regDt').val()
			,	'proofY':lsProofY
			,	'proofM':lsProofM
			,	'proofD':lsProofD
			,	'proofNo':lsProofNo
			,	'itemCd':$('#lblCd1').text()+$('#lblCd2').text()+$('#lblCd3').text()
			,	'vatYn':$('#optVatY').attr('checked') ? 'Y' : 'N'
			,	'amt':__str2num($('#txtAmt').val())
			,	'vat':__str2num($('#lblVat').text())
			,	'bizId':$('#txtBizId').val()
			,	'bizGroup':$('#txtBizGroup').val()
			,	'bizType':$('#txtBizType').val()
			,	'item':$('#txtItem').val()
			,	'other':$('#txtOther').val()
			}
		,	beforeSend:function(){
				$('#divTemp').after('<div id=\'tempLodingBar\' style=\'position:absolute; width:auto; top:270px; left:470; text-align:center; padding:20px; background-color:#ffffff; border:3px solid #cccccc;\'>'+__get_loading()+'</div></center></div>').show();
			}
		,	success:function(result){
				if (result.length == 5){
					alert('정상적으로 처리되었습니다.');
				}else if (result == 1){
					alert('정상적으로 처리되었습니다.');
					opener.result = true;
					self.close();
				}else if (result == 7){
					alert('입력하신 증빙서번호가 중복됩니다.\n확인 후 다시 입력하여 주십시오.');
				}else if (result == 9){
					alert('오류가 발생하였습니다. 잠시후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}

				if (result.length == 5){
					$('#regDt').val('<?=$today;?>');
					$('#proofY').val('<?=$year;?>');
					$('#proofM').val('<?=$month;?>');
					$('#proofD').val('<?=$day;?>');
					$('#proofNo').val(result);
					$('#lblCd1').text('');
					$('#lblCd2').text('');
					$('#lblCd3').text('');
					$('#lblNm1').text('');
					$('#lblNm2').text('');
					$('#lblNm3').text('');
					$('.clsCateGbn').hide();
					$('#optVatN').attr('checked',true);
					$('#txtAmt').val('0');
					$('#lblVat').text('0');
					$('#lblTot').text('0');
					$('#txtBizId').val('');
					$('#txtBizGroup').val('');
					$('#txtBizType').val('');
					$('#txtItem').val('');
					$('#txtOther').val('');
				}

				$('#tempLodingBar').remove();
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border"><?=$title;?></div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="90px">
		<col width="25px">
		<col width="50px">
		<col width="70px">
		<col width="90px">
		<col width="40px">
		<col width="70px">
		<col width="40px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left">일자</th>
			<td class="left" colspan="2"><input id="regDt" name="txt" type="text" value="<?=$today;?>" class="date" style="margin:0;" onchange="lfGetProofNo($('#lblCd1').text()+$('#lblCd2').text()+$('#lblCd3').text());"></td>
			<th class="left">증빙서번호</th>
			<td class="left last" colspan="5">
				<div id="proofY" style="float:left; width:auto;"></div>
				<div id="proofM" style="float:left; width:auto;"></div>
				<div id="proofD" style="float:left; width:auto;"></div>
				<div id="proofNo" style="float:left; width:auto;"></div>
			</td>
		</tr>
		<tr>
			<th class="left">항목</th>
			<td class="left last"><span class="btn_pack find" onclick="lfCategory();"></span></td>
			<td class="left last bold" colspan="7">
				<span id="lblCd1"></span><span class="clsCateGbn" style="margin-left:5px; display:none;">: </span><span id="lblNm1"></span><span class="clsCateGbn" style="margin-left:5px; display:none;">></span>
				<span id="lblCd2"></span><span class="clsCateGbn" style="margin-left:5px; display:none;">: </span><span id="lblNm2"></span><span class="clsCateGbn" style="margin-left:5px; display:none;">></span>
				<span id="lblCd3"></span><span class="clsCateGbn" style="margin-left:5px; display:none;">: </span><span id="lblNm3"></span>
			</td>
		</tr>
		<tr>
			<th class="left">금액</th>
			<td class="" colspan="2"><input id="txtAmt" name="txt" type="text" value="0" class="number" style="width:100%;"></td>
			<th class="left">부가세구분</th>
			<td class="left">
				<input id="optVatY" name="opt" type="radio" value="Y" class="radio"><label for="optVatY">유</label>
				<input id="optVatN" name="opt" type="radio" value="N" class="radio" checked><label for="optVatN">무</label>
			</td>
			<th class="left">부가세</th>
			<td class="left"><span id="lblVat" class="bold">0</span></td>
			<th class="left">합계</th>
			<td class="left last"><span id="lblTot" class="bold">0</span></td>
		</tr>
		<tr>
			<th class="left">사업자등록번호</th>
			<td class="" colspan="4"><input id="txtBizId" name="txt" type="text" value="" alt="taxid"></td>
			<th class="left">업태</th>
			<td class=""><input id="txtBizGroup" name="txt" type="text" value="" style="width:70px;"></td>
			<th class="left">업종</th>
			<td class="last"><input id="txtBizType" name="txt" type="text" value="" style="width:70px;"></td>
		</tr>
		<tr>
			<th class="left">적요</th>
			<td class="" colspan="4"><textarea id="txtItem" name="txts" style="width:100%; height:45px;"></textarea></td>
			<th class="left">비고</th>
			<td class="last" colspan="4"><textarea id="txtOther" name="txts" style="width:100%; height:45px;"></textarea></td>
		</tr>
		<tr>
			<td class="center bottom last" colspan="9">
				<span class="btn_pack m"><button type="button" onclick="lfSave();">저장</button></span>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
			<td class="bottom last"></td>
		</tr>
	</tfoot>
</table>
<input id="txtEntDt" name="txth" type="hidden" value="">
<input id="txtSeq" name="txth" type="hidden" value="">
<?
	include_once("../inc/_db_close.php");
?>