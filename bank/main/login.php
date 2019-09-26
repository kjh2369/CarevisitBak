	<fieldset>
	<legend>로그인</legend>
		<form id="f" name="f" method="post" action="#" onsubmit="lfLogin();">
			<div id="login">
				<ul>
				<li style="margin-bottom:3px;"><label for="id"><img src="../img/txt_id.gif"  alt="아이디 입력"/></label><input id="id" name="txt" type="text"></li>
				<li><label for="pass"><img src="../img/txt_pw.gif"  alt="비밀번호 입력"/></label><input id="pass" name="txt" type="password"></li>
				</ul>
				<!--div class="btn_ok"><input type="image" src="images/btn_login.gif"  title="로그인" /></div-->
				<div class="btn_ok"><a style="cursor:default;" onclick="lfLogin();"><img src="../img/btn_login.gif" title="로그인" alt="로그인" /></a></div>
			</div>
		</form>
	</fieldset>

<!--form id="f" name="f" method="post">
	<div style="width:auto; margin:20px;">
		<table>
			<colgroup>
				<col width="60px">
				<col width="80px">
			</colgroup>
			<tr style="height:25px;">
				<td style="text-align:center; border:1px solid #cccccc;">아이디</td>
				<td style="text-align:center; border:1px solid #cccccc;"><input id="id" name="txt" type="text" style="width:70px; border:none;"></td>
			</tr>
			<tr style="height:25px;">
				<td style="text-align:center; border:1px solid #cccccc;">패스워드</td>
				<td style="text-align:center; border:1px solid #cccccc;"><input id="pass" name="txt" type="password" style="width:70px; border:none;"></td>
			</tr>
			<tr style="height:25px;">
				<td style="text-align:center; border:1px solid #cccccc;" colspan="2">
					<a style="cursor:default;" onclick="lfLogin();">로그인</a>
				</td>
			</tr>
		</table>
	</div>
</form-->

<script type="text/javascript">
$(document).ready(function(){
	$('#id').css('ime-mode','inactive').focus();
});

$('input[name="txt"]').unbind('keydown').bind('keydown',function(e){
	if (e.keyCode == 13){
		if ($(this).val() == ''){
			return false;
		}

		if ($(this).attr('id') == 'pass'){
			lfLogin();
		}else{
			$('#pass').focus();
		}
	}
});

function lfLogin(){
	$.ajax({
		type :'POST'
	,	async:false
	,	url  :'./login_ok.php'
	,	data :{
			'id':$('#id').val()
		,	'pass':$('#pass').val()
		}
	,	beforeSend: function(){
		}
	,	success: function(result){
			if (result == 1){
				location.replace('../index.html');
			}else if (result == 9){
				alert('아이디 및 패스워드를 다시 확인 후 이용하여 주십시오.');
				$('input[name="txt"]').val('');
				$('#id').focus();
			}else{
				alert(result);
			}
		}
	});
}
</script>