function changePassword(){
	 $('#bLayer').css("width", document.body.offsetWidth);

	if (document.body.scrollHeight > document.body.offsetHeight){
		$('#bLayer').css("height", document.body.scrollHeight);
	}else{
		$('#bLayer').css("height", document.body.offsetHeight);
	}

	var tableLeft = (parseInt(__replace($('#bLayer').css('width'), 'px', '')) - parseInt(__replace($('#passTable').css('width'), 'px', ''))) / 2+'px';
	var tableTop  = (parseInt(document.body.offsetHeight) - parseInt(__replace($('#passTable').css('height'), 'px', ''))) / 2+'px';
	
	passLayer.style.top = tableTop;
	passLayer.style.left = tableLeft;
	$('#passLayer').css('top', tableTop);
	$('#passLayer').css('left', tableLeft);
	$('#passLayer').css('width', $('#passTable').css('width'));
	$('#passLayer').css('height', $('#passTable').css('height'));
	$('#passLayer').show();
	$('#passTable').show();


	document.fPass.nowPass.style.imeMode = 'disabled';
	document.fPass.newPass1.style.imeMode = 'disabled';
	document.fPass.newPass2.style.imeMode = 'disabled';

	document.fPass.nowPass.focus();
}

function changePasswordExec(){
	if (document.fPass.nowPass.value == ''){
		alert('현재 비밀번호를 입력하여 주십시오.');
		document.fPass.nowPass.focus();
		return;
	}

	if (document.fPass.newPass1.value == ''){
		alert('새 비밀번호를 입력하여 주십시오.');
		document.fPass.newPass1.focus();
		return;
	}

	if (document.fPass.newPass2.value == ''){
		alert('새 비밀번호 확인을 입력하여 주십시오.');
		document.fPass.newPass2.focus();
		return;
	}

	if (document.fPass.newPass1.value != document.fPass.newPass2.value){
		alert('새 비밀번호와 비밀번호 확인이 일치하지 않습니다. 확인하여 주십시오.');
		document.fPass.newPass2.focus();
		return;
	}

	if (!confirm('비밀번호를 변경하시겠습니까?')){
		return;
	}
	
	var request = getHttpRequest('../inc/_check.php?gubun=changePassword&nowPass='+document.fPass.nowPass.value+'&newPass='+document.fPass.newPass1.value);

	switch(request){
		case 'X':
			alert('현재 비밀번호가 일치하지 않습니다. 확인 후 다시 시도하여 주십시오.');
			document.fPass.nowPass.focus();
			break;
		case 'N':
			alert('비밀번호 수정중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.');
			break;
		default:
			alert('다음 로그인부터 변경된 비밀번호로 로그인하여 주십시오.');
			changePasswordCancel();
	}
}

function changePasswordCancel(){
	bLayer.style.width = 0;
	bLayer.style.height = 0;
	passLayer.style.width = 0;
	passLayer.style.height = 0;
	passTable.style.display = 'none';
}

document.write('<div id="bLayer" style="z-index:0; filter:progid:DXImageTransform.Microsoft.Alpha( Opacity=50, FinishOpacity=0, Style=0, StartX=0,  FinishX=100, StartY=0, FinishY=100); position:absolute; top:0px; left:0px; height:0px; width:0px;"></div>');
document.write('<div id="passLayer" style="z-index:1; left:0; top:0; position:absolute; color:#000000;">');
document.write('	<table class="my_table my_border_blue" id="passTable" style="width:240px; height:60px; background-color:#ffffff; display:none;">');
document.write('	<tr>');
document.write('	<th colspan="2" class="title" style="width:210px;">비밀번호 변경</th>');
document.write('	</tr>');
document.write('	<tr>');
document.write('	<td class="noborder" colspan="2">');
document.write('		<form name="fPass" method="post">');
document.write('		<table style="width:100%;">');
document.write('		<tr>');
document.write('		<td style="width:50%; text-align:left; padding-left:10px;">현재 비밀번호</td>');
document.write('		<td style="width:50%; text-align:left;"><input name="nowPass" type="text"></td>');
document.write('		</tr>');
document.write('		<tr>');
document.write('		<td style="text-align:left; padding-left:10px;">새 비밀번호</td>');
document.write('		<td style="text-align:left;"><input name="newPass1" type="text"></td>');
document.write('		</tr>');
document.write('		<tr>');
document.write('		<td style="text-align:left; padding-left:10px;">비밀번호 확인</td>');
document.write('		<td style="text-align:left;"><input name="newPass2" type="text"></td>');
document.write('		</tr>');
document.write('		<tr>');
document.write('		<td style="text-align:center;" colspan="2">');
document.write('			<a href="#" onClick="changePasswordExec();"><img src="../image/btn9.gif"></a>');
document.write('			<a href="#" onClick="changePasswordCancel();"><img src="../image/btn_cancel.png"></a>');
document.write('		</td>');
document.write('		</tr>');
document.write('		</table>');
document.write('		</form>');
document.write('	</td>');
document.write('	</tr>');
document.write('	</table>');
document.write('</div>');