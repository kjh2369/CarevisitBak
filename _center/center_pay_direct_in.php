<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo		= $_POST['orgNo'];
	$issueDt	= $_POST['issueDt'];
	$issueSeq	= $_POST['issueSeq'];

	if ($orgNo && $issueDt && $issueSeq){
		$sql = 'SELECT	*
				FROM	cv_pay_in
				WHERE	org_no		= \''.$orgNo.'\'
				AND		issue_dt	= \''.$issueDt.'\'
				AND		issue_seq	= \''.$issueSeq.'\'
				AND		del_flag	= \'N\'';

		$rowData = $conn->get_array($sql);
	}

	if (!$rowData['in_gbn']) $rowData['in_gbn'] = '1';
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		$('input:radio[name="optInGbn"]').unbind('click').bind('click', function(){
			if ($(this).val() == '1'){
				//$('#txtCmsno, #cboOutStat').attr('disabled', false);
				$('#OBJ_USEYYMM').show();
				$('#TBL_USEHIS').hide();
			}else{
				//$('#txtCmsno, #cboOutStat').attr('disabled', true);
				$('#OBJ_USEYYMM').hide();
				$('#TBL_USEHIS').show();
			}
		});

		$('input:radio[name="optInGbn"]:checked').click();
		$('#cboInBank').change();

		if ($('#txtOrgNo').val()) $('#txtOrgNo').change();
	});

	function lfInit(){
		$('input:text').val('');
		$('select').val('1');
	}

	function lfSave(){
		if (!$('#txtOrgNo').val()){
			alert('기관기호를 입력하여 주십시오.');
			$('#txtOrgNo').focus();
			return;
		}

		if (!$('#txtIssueDt').val()){
			alert('입/출금일자를 입력하여 주십시오.');
			$('#txtIssueDt').focus();
			return;
		}

		if ($('input:radio[name="optInGbn"]:checked').val() == '1' && !$('#txtCmsno').val()){
			alert('CMS 번호를 입력하여 주십시오.');
			$('#txtCmsno').focus();
			return;
		}

		var data = {};

		$('input:text').each(function(){
			data[$(this).attr('id')] = $(this).val();
		});

		data['orgIssueDt'] = '<?=$issueDt;?>';
		data['orgIssueSeq'] = '<?=$issueSeq;?>';

		data['cboContCom'] = $('#cboContCom option:selected').text();
		data['cboOutStat'] = $('#cboOutStat option:selected').text();
		data['cboInBank'] = $('#cboInBank option:selected').text();
		data['optInGbn'] = $('input:radio[name="optInGbn"]:checked').val();

		data['usehis'] = '';

		$('#TBL_USEHIS_DTL tr').each(function(){
			if ($('#txtBankYymm', this).val() && $('#txtBankAmt', this).val()){
				data['usehis'] += (data['usehis'] ? '?' : '');
				data['usehis'] += 'yymm='+$('#txtBankYymm', this).val().split('-').join('');
				data['usehis'] += '&amt='+$('#txtBankAmt', this).val().split(',').join('');
			}
		});

		if (data['optInGbn'] == '1'){
			if (!$('#txtUseYymm').val()){
				alert('사용년월을 입력하여 주십시오.');
				$('#txtUseYymm').focus();
				return;
			}
		}else{
			if (!data['usehis']){
				alert('사용내역을 등록하여 주십시오.');
				return;
			}
		}

		$.ajax({
			type:'POST'
		,	async:false
		,	url:'./center_pay_direct_in_save.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfCenterName(){
		if (!$('#txtOrgNo').val()) return;

		$.ajax({
			type:'POST'
		,	url:'./fun.php'
		,	data:{
				'findGbn':'101'
			,	'orgNo':$('#txtOrgNo').val()
			}
		,	beforeSend:function(){
			}
		,	success:function(orgName){
				$('#ID_ORG_NAME').text(orgName);
			}
		,	error:function(){
			}
		}).responseXML;
	}

	function lfDelete(){
		if (!confirm('정말로 삭제하시겠습니까?')) return;

		$.ajax({
			type:'POST'
		,	async:false
		,	url:'./center_pay_direct_in_del.php'
		,	data:{
				'orgNo':'<?=$orgNo;?>'
			,	'issueDt':'<?=$issueDt;?>'
			,	'issueSeq':'<?=$issueSeq;?>'
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (!result){
					alert('정상적으로 처리되었습니다.');
					self.close();
				}else{
					alert(result);
				}
			}
		,	error:function(){
			}
		}).responseXML;
	}
</script>
<div class="title title_border">입금내역등록</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="80px">
		<col width="135px">
		<col width="50px">
		<col width="130px">
		<col width="65px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">기관기호</th>
			<td>
				<input id="txtOrgNo" type="text" value="<?=$orgNo;?>" style="width:100px;" onchange="lfCenterName();" <?=$orgNo ? 'readonly' : '';?>>
			</td>
			<th class="center">기관명</th>
			<td class="left" colspan="4"><div id="ID_ORG_NAME"></div></td>
		</tr>
		<tr>
			<th class="center">입/출금일시</th>
			<td>
				<input id="txtIssueDt" type="text" value="<?=$myF->dateStyle($rowData['issue_dt']);?>" class="date" style="margin-right:0;">
				<input id="txtIssueTime" type="text" value="<?=$myF->timeStyle($rowData['issue_time']);?>" class="no_string" maxlength="6" style="margin-left:0; width:50px; margin-left:-5px;">
			</td>
			<th class="center">구분</th>
			<td>
				<label><input name="optInGbn" type="radio" class="radio" value="1" <?=$rowData['in_gbn'] == '1' ? 'checked' : '';?>>CMS</label>
				<label><input name="optInGbn" type="radio" class="radio" value="2" <?=$rowData['in_gbn'] == '2' ? 'checked' : '';?>>무통장</label>
			</td>
			<th class="center">CMS 번호</th>
			<td colspan="3">
				<input id="txtCmsno" type="text" value="<?=$rowData['cms_no'];?>" style="width:100%;">
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="50px">
		<col width="70px">
		<col width="100px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left" colspan="7">- 청구내역</th>
		</tr>
		<tr id="OBJ_USEYYMM">
			<th class="center">사용년월</th>
			<td colspan="5"><?
				if ($rowData['in_gbn'] == '1'){
					$sql = 'SELECT	use_yymm
							FROM	cv_pay_in_dtl
							WHERE	org_no		= \''.$orgNo.'\'
							AND		issue_dt	= \''.$issueDt.'\'
							AND		issue_seq	= \''.$issueSeq.'\'
							AND		del_flag	= \'N\'';

					$useYymm = $conn->get_data($sql);
				}?>
				<input id="txtUseYymm" type="text" value="<?=$myF->_styleYymm($useYymm);?>" class="yymm">
			</td>
		</tr>
		<tr>
			<th class="center">청구일자</th>
			<td>
				<input id="txtClaimDt" type="text" value="<?=$myF->dateStyle($rowData['claim_dt']);?>" class="date">
			</td>
			<th class="center">청구금액</th>
			<td>
				<input id="txtClaimAmt" type="text" value="<?=number_format($rowData['claim_amt']);?>" class="number" style="width:100%;" onchange="$('#txtInAmt').val(__str2num($(this).val()));">
			</td>
			<th class="center">계약회사</th>
			<td>
				<select id="cboContCom" style="width:auto;">
					<option value="1" <?=$rowData['cont_com'] == '케어비지트' ? 'selected' : '';?>>케어비지트</option>
					<option value="2" <?=$rowData['cont_com'] == '굿이오스' ? 'selected' : '';?>>굿이오스</option>
					<option value="3" <?=$rowData['cont_com'] == '지케어' ? 'selected' : '';?>>지케어</option>
				</select>
			</td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="50px">
		<col width="70px">
		<col width="100px">
		<col width="70px">
		<col width="100px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left" colspan="7">- 출금내역</th>
		</tr>
		<tr>
			<th class="center">출금상태</th>
			<td>
				<select id="cboOutStat" style="width:auto;">
					<option value="1" <?=$rowData['out_stat'] != '출금실패' ? 'selected' : '';?>>출금성공</option>
					<option value="2" <?=$rowData['out_stat'] == '출금실패' ? 'selected' : '';?>>출금실패</option>
				</select>
			</td>
			<th class="center">출금은행</th>
			<td>
				<input id="txtOutBank" type="text" value="<?=$rowData['out_bank'];?>" style="width:100%;">
			</td>
			<th class="center">계좌번호</th>
			<td>
				<input id="txtOutAcctNo" type="text" value="<?=$rowData['out_acct_no'];?>" style="width:100%;">
			</td>
			<td></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col width="100px">
		<col width="70px">
		<col width="150px">
		<col width="70px">
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left" colspan="7">- 입금내역</th>
		</tr>
		<tr>
			<th class="center">입금은행</th>
			<td>
				<select id="cboInBank" style="width:auto;" onchange="$('#txtInAcctNo').val($('#cboInBank option:selected').attr('acctNo'));">
					<option value="3" acctNo="301-0164-4623-31" <?=$rowData['in_bank'] != '개인(농협)' ? 'selected' : '';?>>법인(케어비지트)</option>
					<option value="2" acctNo="" <?=$rowData['in_bank'] == '개인(농협)' ? 'selected' : '';?>>개인(농협)</option>
				</select>
			</td>
			<th class="center">계좌번호</th>
			<td>
				<input id="txtInAcctNo" type="text" value="<?=$rowData['in_acct_no'];?>" style="width:100%;">
			</td>
			<th class="center">입금금액</th>
			<td>
				<input id="txtInAmt" type="text" value="<?=number_format($rowData['in_amt']);?>" class="number" style="width:100%;">
			</td>
			<td></td>
		</tr>
	</tbody>
</table>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="70px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="left" colspan="2">- 비고</th>
		</tr>
		<tr>
			<th class="center">기록사항</th>
			<td>
				<input id="txtAcctLog" type="text" value="<?=$rowData['acct_log'];?>" style="width:100%;">
			</td>
		</tr>
		<tr>
			<th class="center">비고</th>
			<td>
				<input id="txtOther" type="text" value="<?=$rowData['remark'];?>" style="width:100%;">
			</td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">
	function UseHisRowAdd(){
		var html = '';

		html =	'<tr>'
			 +	'<td class="center"><input id="txtBankYymm" type="text" value="" class="yymm"></td>'
			 +	'<td class="center"><input id="txtBankAmt" type="text" value="0" class="number" style="width:100%;"></td>'
			 +	'<td class="center"><div class="left"><span class="btn_pack small"><button onclick="UseHisRowRemove(this);">삭제</button></span></div></td>'
			 +	'</tr>';

		if ($('#TBL_USEHIS_DTL tr').length > 0){
			$('#TBL_USEHIS_DTL tr:first').before(html);
		}else{
			$('#TBL_USEHIS_DTL').html(html);
		}

		$('#TBL_USEHIS_DTL tr:first input:text').each(function(){
			__init_object(this);
		});
	}

	function UseHisRowRemove(obj){
		var obj = __GetTagObject(obj, 'TR');
		$(obj).remove();
	}
</script>
<div id="TBL_USEHIS">
	<table class="my_table" style="width:100%;">
		<colgroup>
			<col width="70px">
			<col width="70px">
			<col>
		</colgroup>
		<thead>
			<tr>
				<th class="head" colspan="3">
					<div style="float:left; width:auto; margin-top:2px; margin-left:5px;">- 사용내역</div>
					<div style="float:right; width:auto; margin-right:5px;">
						<span class="btn_pack m"><button onclick="UseHisRowAdd();">사용내역 추가</button></span>
					</div>
				</th>
			</tr>
			<tr>
				<th class="head">사용년월</th>
				<th class="head">입금금액</th>
				<th class="head">비고</th>
			</tr>
		</thead>
	</table>
	<div style="overflow-x:hidden; overflow-y:scroll; height:150px; border-bottom:1px solid #CCCCCC;">
		<table class="my_table" style="width:100%;">
			<colgroup>
				<col width="70px">
				<col width="70px">
				<col>
			</colgroup>
			<tbody id="TBL_USEHIS_DTL"><?
				if ($rowData['in_gbn'] == '2'){
					$sql = 'SELECT	use_yymm, in_amt
							FROM	cv_pay_in_dtl
							WHERE	org_no		= \''.$orgNo.'\'
							AND		issue_dt	= \''.$issueDt.'\'
							AND		issue_seq	= \''.$issueSeq.'\'
							AND		del_flag	= \'N\'';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<tr>
						<td class="center"><input id="txtBankYymm" type="text" value="<?=$myF->_styleYYMM($row['use_yymm']);?>" class="yymm"></td>
						<td class="center"><input id="txtBankAmt" type="text" value="<?=number_format($row['in_amt']);?>" class="number" style="width:100%;"></td>
						<td class="center"><div class="left"><span class="btn_pack small"><button onclick="UseHisRowRemove(this);">삭제</button></span></div></td>
						</tr><?
					}

					$conn->row_free();
				}?>
			</tbody>
		</table>
	</div>
</div>

<div class="center" style="margin-top:10px;"><?
	if (!$orgNo){?>
		<span class="btn_pack m"><button onclick="lfInit();">신규</button></span><?
	}?>
	<span class="btn_pack m"><button onclick="lfSave();">저장</button></span><?
	if ($orgNo){?>
		<span class="btn_pack m"><button onclick="lfDelete();">삭제</button></span><?
	}?>
	<span class="btn_pack m"><button onclick="self.close();">닫기</button></span>
</div>
<?
	include_once('../inc/_footer.php');
?>