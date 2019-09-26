<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
?>
<script type="text/javascript">
	function lfChangeRun(){
		if (!$('#txtNowPw').val()){
			alert('현재 비밀번호를 입력하여 주십시오.');
			$('#txtNowPw').focus();
			return;
		}

		if (!$('#txtNewPw').val()){
			alert('새 비밀번호를 입력하여 주십시오.');
			$('#txtNewPw').focus();
			return;
		}

		if ($('#txtNewPw').val() != $('#txtPwConfirm').val()){
			alert('비밀번호 확인이 올바르지 않습니다. 확인하여 주십시오.');
			$('#txtPwConfirm').focus();
			return;
		}

		$.ajax({
			type:'POST'
		,	url:'../member/pwd_change_run.php'
		,	data:{
				'nowPw':$('#txtNowPw').val()
			,	'newPw':$('#txtNewPw').val()
			,	'pwConfirm':$('#txtPwConfirm').val()
			}
		,	success: function(result){
				if (result == 1){
					alert('정상적으로 처리되었습니다.\n다음 로그인부터 변경된 비밀번호로 로그인하여 주십시오.');
					self.close();
				}else if (result == 8){
					alert('현재 비밀번호가 올바르지 않습니다. 확인하여 주십시오.');
				}else if (result == 8){
					alert('처리중 오류가 발생하였습니다.\n잠시 후 다시 시도하여 주십시오.');
				}else{
					alert(result);
				}
			}
		});
	}
</script>
<div class="title title_border">비밀번호 변경</div>
<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="90px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th>현재 비밀번호</th>
			<td class="last"><input id="txtNowPw" type="text" style="width:100%;"></td>
		</tr>
		<tr>
			<th>새 비밀번호</th>
			<td class="last"><input id="txtNewPw" type="text" style="width:100%;"></td>
		</tr>
		<tr>
			<th>비밀번호 확인</th>
			<td class="last"><input id="txtPwConfirm" type="text" style="width:100%;"></td>
		</tr>
		<tr>
			<td class="bottom last" style="text-align:center; padding-top:20px;" colspan="2">
				<span class="btn_pack m"><button type="button" onclick="lfChangeRun();">변경</button></span>
				<span class="btn_pack m"><button type="button" onclick="self.close();">취소</button></span>
			</td>
		</tr>
	</tbody>
</table>
<?
	include_once('../inc/_footer.php');
?>