<?
/*
* 아이디찾기
기관코드,이름,주민등록번호를 입력
입력유무체크
*/
include_once('../inc/_header.php');
include_once('../inc/_myFun.php');
include_once('../inc/_ed.php');
include_once('../inc/_db_open.php');

$id = $_GET['id'];

if($gDomain == 'dwcare.com'){
	$imgsrc = '../img/top/ci.png';
}else {
	$imgsrc = '../admin_img/carevisit/top/ci.png';
}

?>
<script language='javascript'>
	
	//입력 유무 체크 후 id_find_ok폼으로 입력 코드,이름, 주민을 넘긴다.
	function id_find(){
		var f = document.f;
		if(!f.code.value){
			alert("기관코드를 입력해 주십시오.");
			f.code.focus();
			return;
		}
		if(!f.name.value){
			alert("이름를 입력해 주십시오.");
			f.name.focus();
			return;
		}
		if(!f.jumin1.value || !f.jumin2.value){
			alert("주민번호를 입력해 주십시오.");
			f.jumin.focus();
			return;
		}
		
		document.id_find_ok.code.value = f.code.value;
		document.id_find_ok.name.value = f.name.value;
		document.id_find_ok.jumin1.value = f.jumin1.value;
		document.id_find_ok.jumin2.value = f.jumin2.value;
		document.id_find_ok.submit();
	}
	
	function pwd_find(){
		var f = document.f;
		
		if(!f.p_code.value){
			alert("기관코드를 입력해 주십시오.");
			f.p_code.focus();
			return;
		}
		
		if(!f.p_id.value){
			alert("아이디를 입력해 주십시오.");
			f.p_id.focus();
			return;
		}
		if(!f.p_name.value){
			alert("이름를 입력해 주십시오.");
			f.p_name.focus();
			return;
		}
		if(!f.p_jumin1.value || !f.p_jumin2.value){
			alert("주민번호를 입력해 주십시오.");
			f.p_jumin1.focus();
			return;
		}

		document.pwd_find_ok.p_code.value = f.p_code.value;
		document.pwd_find_ok.p_id.value = f.p_id.value;
		document.pwd_find_ok.p_name.value = f.p_name.value;
		document.pwd_find_ok.p_jumin1.value = f.p_jumin1.value;
		document.pwd_find_ok.p_jumin2.value = f.p_jumin2.value;
		document.pwd_find_ok.submit();
	}

	window.onload = function(){
	__init_form(document.f);
	}
</script>
<script language='javascript' src='../js/script.js'></script>
<div id="top_box">
	<div class="top_ci">
		<a href="#" onclick="__go_menu('');"><img src="<?=$imgsrc;?>" /></a>
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
					<div style="height:30px; font-size:18; font-weight:bold; color:#1b57b3; margin-top:10px; border-bottom:1px solid #a6c0f3;">아이디/비밀번호 찾기</div>
						<div style="width:734px; text-align:center; margin-top:200px;">
							<table style="border:none;">
								<tr>
									<td class="noborder" style="padding-left:30px;">
										<div style="width:232px float:left;">
											<table border=0 style="border:1px solid #0e69b0;">
												<colgroup>
													<col width="90px">
													<col width="142px">
												</colgroup>
												<tr>
													<td style="text-align:left; height:30px; padding-left:5px; border:1px solid #a6c0f3; font-weight:bold;">기관코드</td>
													<td style="border:1px solid #a6c0f3;">
														<input name="code" type="text" style="width:133px; background-color:#f4f4f4;">
													</td>
												</tr>
												<tr>
													<td style="text-align:left; height:30px; padding-left:5px; border:1px solid #a6c0f3; font-weight:bold;">이름</td>
													<td style="border:1px solid #a6c0f3;">
														<input name="name" type="text" style="width:133px; background-color:#f4f4f4;">
													</td>
												</tr>
												<tr>
													<td style="text-align:left; height:30px; padding-left:5px; border:1px solid #a6c0f3; font-weight:bold;">주민등록번호</td>
													<td style="border:1px solid #a6c0f3;">
														<input type="text" name="jumin1" value="" class="phone" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 6){document.f.jumin2.focus();}" maxlength="6" style="width:60px; background-color:#f4f4f4;"> - <input type="password" class="phone" onKeyDown="__onlyNumber(this);" name="jumin2" value="" maxlength="7" style="width:60px; background-color:#f4f4f4;">
													</td>
												</tr>
											</table>
											<div align="center" style="margin-top:30px;"><button style="width:100px;" onclick="id_find();">아이디찾기</button><button style="width:100px;" onclick="location.href='../main/logout_ok.php';">취소</button></div>
											<? 
											if ($id != ''){
												?>
												<div align="left" style="margin-top:30px; border:1px solid #0e69b0; font-weight:bold; font-size:12pt; ">아이디 : <?=$id;?></div>
												<? 
											} 
											?>
										</div>
									</td>
									<td class="noborder" style="padding-left:30px;">
										<div style="width:232px">
											<table border=0 style="border:1px solid #0e69b0;">
												<colgroup>
													<col width="90px">
													<col width="142px">
												</colgroup>
												<tr>
													<td style="text-align:left; height:30px; padding-left:5px; border:1px solid #a6c0f3; font-weight:bold;">기관코드</td>
													<td style="border:1px solid #a6c0f3;">
														<input name="p_code" type="text" style="width:133px; background-color:#f4f4f4;">
													</td>
												</tr>
												<tr>
													<td style="text-align:left; height:30px; padding-left:5px; border:1px solid #a6c0f3; font-weight:bold;">아이디</td>
													<td style="border:1px solid #a6c0f3;">
														<input name="p_id" type="text" style="width:133px; background-color:#f4f4f4;">
													</td>
												</tr>
												<tr>
													<td style="text-align:left; height:30px; padding-left:5px; border:1px solid #a6c0f3; font-weight:bold;">이름</td>
													<td style="border:1px solid #a6c0f3;">
														<input name="p_name" type="text" style="width:133px; background-color:#f4f4f4;">
													</td>
												</tr>
												<tr>
													<td style="text-align:left; height:30px; padding-left:5px; border:1px solid #a6c0f3; font-weight:bold;">주민등록번호</td>
													<td style="border:1px solid #a6c0f3;">
														<input type="text" name="p_jumin1" value="" class="phone" onKeyDown="__onlyNumber(this);" onKeyUp="if(this.value.length == 6){document.f.p_jumin2.focus();}" maxlength="6" style="width:60px; background-color:#f4f4f4;"> - <input type="password" class="phone" onKeyDown="__onlyNumber(this);" name="p_jumin2" value="" maxlength="7" style="width:60px; background-color:#f4f4f4;">
													</td>
												</tr>
											</table>
											<div align="center" style="margin-top:30px;"><button style="width:110px;" onclick="pwd_find();">비밀번호찾기</button><button style="width:110px;" onclick="location.href='../member/login.php';">취소</button></div>
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
				<div>
			<div>
		</td>
	<tr>
</table>



<div id="main_copy" style="margin-top:200px;">
	<div>COPYRIGHT(C) 2011 GOODEOS ALL RIGHTS RESERVED</div>
</div>
</form>
<form name="id_find_ok" method="post" target="backform" action="id_find_ok.php">
<input name="code" type="hidden" value="">
<input name="name" type="hidden" value="">
<input name="jumin1" type="hidden" value="">
<input name="jumin2" type="hidden" value="">
</form>
<form name="pwd_find_ok" method="post" target="backform" action="pwd_find_ok.php">
<input name="p_code" type="hidden" value="">
<input name="p_id" type="hidden" value="">
<input name="p_name" type="hidden" value="">
<input name="p_jumin1" type="hidden" value="">
<input name="p_jumin2" type="hidden" value="">
<?
	include_once('../inc/_db_close.php');
	include_once('../inc/_footer.php');
?>