<?
	include_once('../inc/_header.php');
	include_once('../inc/_login.php');

	$code	= $_SESSION['userCenterCode'];
?>
<script type="text/javascript">
	var opener = null;

	$(document).ready(function(){
		opener = window.dialogArguments;
		opener.result	= false;

		__init_form(document.f);
	});

	function lfChkPw(){
		var pw	= $('#txtPw').val();

		if (!pw){
			alert('로그인 비밀번호를 입력하여 주십시오.');
			$('#txtPw').focus();
			return false;
		}

		$.ajax({
			type:'POST'
		,	url:'./_find_pw_exec.php'
		,	data:{
				'code'	:'<?=$code;?>'
			,	'pass'	:pw
			}
		,	beforeSend:function(){
			}
		,	success:function(result){
				if (result == 1){
					opener.result	= true;
					self.close();
				}else if (result == 9){
					alert('입력하신 비밀버호가 일치하지 않습니다.\n확인 후 다시 시도하여 주십시오.');
					$('#txtPw').val('').focus();
					return false;
				}else{
					alert(result);
					return false;
				}
			}
		,	complete:function(result){
			}
		,	error:function (){
			}
		}).responseXML;
	}
</script>

<form id="f" name="f" method="post" onsubmit="return lfChkPw();">

<div id="title" class="title title_border">비밀번호 입력</div>

<table class="my_table" style="width:100%;">
	<colgroup>
		<col width="60px">
		<col>
	</colgroup>
	<tbody>
		<tr>
			<th class="center">비밀번호</th>
			<td class="last"><input id="txtPw" name="txt" type="password" style="width:100%;"></td>
		</tr>
		<tr>
			<td class="center bottom last" colspan="2">
				<span class="btn_pack m"><button type="button" onclick="lfChkPw(); return false;">확인</button></span>
			</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td class="center bottom last" colspan="2">
				<div class="left">※로그인 비밀번호를 입력하여 주십시오.</div>
			</td>
		</tr>
	</tfoot>
</table>

</form>

<?
	include_once('../inc/_footer.php');
?>