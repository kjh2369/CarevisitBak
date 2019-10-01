<?
	include_once('../inc/_myFun.php');

	$imgpath = $gHostImgPath.'/login/ci.gif';

	if ($gDomain == 'kacold.net'){
		$imgpath = $gHostImgPath.'/top/ci.png';
		if (!is_file($imgpath)) $imgpath = $gHostImgPath.'/top/ci.png';
	}

?>
<link href="../css/login.css" rel="stylesheet" type="text/css">
<div id="login_wrap">
		<!-- header -->
		<header id="login_t_box">
			<h1 class="login_ci"><span class="hidden">보건복지부 노인맞춤돌봄서비스</span></h1>
		</header>
		<!-- //header -->
		<!-- Container -->
		<main role="main" id="login_container">
			<article id="login_content">
				<div id="login_box">
					<h2 class="ctn_ci"><span class="hidden">nhm 로그인</span></h2>
					<form name="login" class="login">
						<fieldset id="login_c_box">
						<legend>아이디/비밀번호입력 </legend>
						<dl>
						<dt><label for="uid">아이디</label></dt>
						<dd class="uid"><input type="text"	name="uCode" id="uid"  placeholder="id" onkeydown="if(event.keyCode == 13 && this.value != ''){document.getElementById('uPass').focus();}" style="ime-mode:inactive;" /></dd>
						<dt><label for="upw">비밀번호</label> </dt>
						<dd class="upw mg_top10"><input type="password" name="uPass" id="upw" placeholder="password" onkeydown="if(event.keyCode == 13 && this.value != ''){_Login();}" /></dd>
						</dl>
						<input type="button" name="login_btn"  class="login_btn" value="로그인" onclick="_Login();"/>
					</fieldset>
					</form>
				</div>
			</article>
		</main>
		<!--// Container -->
</div>