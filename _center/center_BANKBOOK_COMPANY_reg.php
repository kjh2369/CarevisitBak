<?
	include_once('../inc/_header.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	/*
	 *	본사계좌등록
	 */
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input:text').each(function(){
			__init_object(this);
		});

		self.focus();
	});

	window.onunload = function(){
		//opener.lfResult('<?=$type;?>');
	}

	function lfSave(){
		if (!$('#txtAcctNo').val()){
			alert('계좌번호를 입력하여 주십시오.');
			$('#txtAcctNo').focus();
			return;
		}

		if (!$('#txtFromDt').val()){
			alert('적용일자를 입력하여 주십시오.');
			$('#txtFromDt').focus();
			return;
		}

		if (!$('#txtToDt').val()){
			alert('종료일자를 입력하여 주십시오.');
			$('#txtToDt').focus();
			return;
		}

		if ($('#txtFromDt').val() > $('#txtToDt').val()){
			alert('적용일자가 종료일자보다 큽니다.\n확인 후 다시 입력하여 주십시오.');
			$('#txtToDt').focus();
			return;
		}

		if (!$('#cboBankCd').val()){
			alert('온행을 선택하여 주십시오.');
			$('#cboBankCd').focus();
			return;
		}

		if (!$('#txtHolder').val()){
			alert('예금주명을 입력하여 주십시오.');
			$('#txtHolder').focus();
			return;
		}

		$('input:hidden').each(function(){
			var id = $(this).attr('id');
			var val = $(this).val();

			data[id] = val;
		});

		$('input:text').each(function(){
			var id = $(this).attr('id');
			var val = $(this).val();

			data[id] = val;
		});

		$('select').each(function(){
			var id = $(this).attr('id');
			var val = $(this).val();

			data[id] = val;
		});

		$.ajax({
			type:'POST'
		,	url:'./center_BANKBOOK_COMPANY_save.php'
		,	data:data
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.');
				}else if (result == 9){
					alert('데이타 처리중 오류가 발생하였습니다.\n잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
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
<div class="title title_border">본사계좌 등록 및 변경</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>계좌번호</th>
			<td><input id="txtAcctNo" type="text" value="" style="width:100%;"></td>
		</tr>
		<tr>
			<th>적용일자</th>
			<td><input id="txtFromDt" type="text" value="" class="date"></td>
		</tr>
		<tr>
			<th>종료일자</th>
			<td><input id="txtToDt" type="text" value="" class="date"></td>
		</tr>
		<tr>
			<th>은행명</th>
			<td>
				<select id="cboBankCd" style="width:auto;"><?
					$sql = 'SELECT	code
							,		name
							FROM	bankcode
							ORDER	BY code';

					$conn->query($sql);
					$conn->fetch();

					$rowCnt = $conn->row_count();

					for($i=0; $i<$rowCnt; $i++){
						$row = $conn->select_row($i);?>
						<option value="<?=$row['code'];?>"><?=$row['name'];?></option><?
					}

					$conn->row_free();?>
				</select>
			</td>
		</tr>
		<tr>
			<th>예금주</th>
			<td><input id="txtHolder" type="text" value="" style="width:100%;"></td>
		</tr>
		<tr>
			<th>계좌구분</th>
			<td>
				<select id="cboAcctGbn" style="width:auto;">
					<option value="1">개인</option>
					<option value="2">법인</option>
				</select>
			</td>
		</tr>
		<tr>
			<th>주민번호</th>
			<td><input id="txtJumin" type="text" value="" style="width:100%;" maxlength="13"></td>
		</tr>
		<tr>
			<th>등록번호</th>
			<td><input id="txtBizno" type="text" value="" style="width:100%;" maxlength="10"></td>
		</tr>
		<tr>
			<td class="center bottom last" style="padding-top:10px; padding-bottom:20px;" colspan="2">
				<span class="btn_pack m"><button onclick="lfSave();">저장</button></span>
				<span class="btn_pack m"><button onclick="self.close();">닫기</button></span>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>