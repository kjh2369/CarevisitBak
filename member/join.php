<?
	include_once('../inc/_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_db_open.php');

?>
<script language='javascript'>
<!--
//주민번호체크 후 상세등록화면으로 이동
function join(){
	var f = document.f;

	if(!f.agree_1.checked){
		alert("약관에 동의하지 않으셨습니다.");
		f.agree_1.focus();
		return false;
	}
	
	if(f.code.value == ''){
		alert('기관기호를 입력하십시오.');
		f.code.focus();
		return;
	}

	if(f.name.value == ''){
		alert('이름을 입력하십시오.');
		f.name.focus();
		return;
	}

	if(f.mem_no.value == ''){
		alert('사번을 입력하십시오.');
		f.mem_no.focus();
		return;
	}

	document.join_ok.code.value = f.code.value;
	document.join_ok.name.value = f.name.value;
	document.join_ok.mem_no.value = f.mem_no.value;
	document.join_ok.submit();
}

window.onload = function(){
	__init_form(document.f);
}

-->
</script>
<style>
	/* 회원가입
	======================================== */
	.m_name_box { width:734px; height:70px; border:1px solid #e8e8e8; margin-top:3px; padding-top:24px; padding-left:24px;}
	.m_title  {font-size:12px; font-weight:bold; BACKGROUND-COLOR: #F7F7F7; text-align:left; padding-left:15px;  }

	/* 이용약관 */
	.meb_box {width:738px; height:300px; border:1px solid #e8e8e8; padding:10px;}
	.m_box {overflow-y: scroll; height:300px;}

</style>
<script language='javascript' src='../js/script.js'></script>
<div id="top_box">
	<div class="top_ci">
		<a href="#" onclick="__go_menu('');"><img src="<?=$gHostImgPath;?>/top/ci.png" /></a>
	</div>
</div>
<iframe name="backform" width="0" height="0" frameborder="0" border="0"></iframe>
<form name="f" method="post">
<table style="clear:both; border:none;" width="1024px">
	<tr>
		<td align="center" class="noborder">
			<div style="width:734px; text-align:left;">
				<!--이용약관-->
				<div>
					<div style="height:30px; font-size:18; font-weight:bold; color:#1b57b3; margin-top:10px; border-bottom:1px solid #a6c0f3;">회원가입</div>
					<div class="title" style="margin-top:30px;">이용약관</div>
					<div class="meb_box" style="border:1px solid #a6c0f3;">
						<div class="m_box" style="background-color:#f7f7f7;">
							<p><b>제 1장 총칙</b></p>

							<p><b>제1조 (목적)</b><br/>
							케어비지트(www.carevisit.net) 이용고객 약관(이하, "본 약관"이라 합니다)은 개인 또는 기업(이하 "이용고객" 또는 "회원"<br/>
							이 라 합니다.)이 케어비지트(이하 "회사"이라 합니다)에서 제공하는 인터넷 관련 서비스(이하 "서비스"라 합니다)를 이용함에 <br/>
							있 어 이용고객과 회사의 권리, 의무 및 책임사항을 규정함을 목적으로 합니다.</p>

							<p><b>제2조 (이용약관의 효력 및 변경)</b><br/>
							(1) 이 약관은 케어비지트(www.carevisit.net)에서 온라인으로 공시함으로써 효력을 발생하며, 합리적인 사유가 발생할<br/>
							경우 관련 법령에 위배되지 않는 범위 안에서 개정될 수 있습니다. 개정된 약관은 온라인에서 공지함으로써 효력을 발휘하며, 이용고객<br/>
							의 권 리 또는 의무 등 중요한 규정의 개정은 사전에 공지합니다.<br/>
							(2) 회사는 합리적인 사유가 발생될 경우에는 이 약관을 변경할 수 있으며, 약관을 변경할 경우에는 지체없이 이를 사전에 공시합니다.</p>

							<p><b>제38조 (재판권 및 준거법)</b><br/>
							(1) 이 약관에 명시되지 않은 사항은 전기통신사업법 등 관계법령과 상관습에 따릅니다.<br/>
							(2) 회사의 유료 서비스 이용 회원의 경우 회사가 별도로 정한 해당 서비스의 약관 및 정책에 따릅니다.<br/>
							(3) 서비스 이용으로 발생한 분쟁에 대해 소송이 제기되는 경우 회사의 본사 소재지를 관할하는 법원을 관할 법원으로 합니다.</p>

							<p><b>[부칙]</b><br/>
							(시행일) 본 약관은 2008년 9월 1일부터 적용됩니다. 2008년 9월 17일부터 시행되던 종전의 약관은 본 약관으로 대체합니다. </p>

						</div>
					</div>
					<div  style="margin-top:5px;">
						<input type="checkbox" class="checkbox" name="agree_1">위의 약관에 동의합니다.
					</div>
				</div>
				<div class="m_name_box" style="border:1px solid #a6c0f3; margin-top:30px;">
					<span style="font-size:12px; font-weight:bold;">기관기호&nbsp;
						<input type="text" name="code" maxlength="15" style="background-color:#f4f4f4; ime-mode:disabled;" >&nbsp;&nbsp;
					</span>
					<span style="font-size:12px; font-weight:bold;">이름&nbsp;
						<input type="text" name="name" size="20" style="background-color:#f4f4f4;">&nbsp;&nbsp;
					</span>
					<span style="font-size:12px; font-weight:bold;">사번&nbsp;
						<input type="text" name="mem_no" value="" style="width:60px; background-color:#f4f4f4;">
					</span>
					<!--span style="font-size:12px; font-weight:bold;">주민등록번호&nbsp;
						<input type="text" name="m_jumin1" value="" class="phone" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 6){document.f.m_jumin2.focus();}" maxlength="6" style="width:60px; background-color:#f4f4f4;"> - <input type="password" class="phone" onKeyDown="__onlyNumber(this);" name="m_jumin2" value="" maxlength="7" style="width:60px; background-color:#f4f4f4;">
					</span-->
				</div>
				<!--가입-->
				<div align="center" style="margin-top:50px;">
					<a onclick="join();"><img src="../member/img/btn_join.gif" /></img></a>
					<a onclick="location.href='../main/logout_ok.php';"><img src="../member/img/btn_can.gif" /></img></a>
				</div>
			</div>
		</td>
	<tr>
</table>
<div id="main_copy" style="margin-top:30px;">
	<div>COPYRIGHT(C) 2011 GOODEOS ALL RIGHTS RESERVED</div>
</div>
</form>
<form name="join_ok" method="post" target="backform" action="join_ok.php">
<input name="code" type="hidden" value="">
<input name="name" type="hidden" value="">
<input name="mem_no" type="hidden" value="">
</form>
<?
	include_once('../inc/_db_close.php');
	include_once('../inc/_footer.php');
?>