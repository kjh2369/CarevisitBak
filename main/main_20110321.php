<?
	include("../inc/_header.php");

	if (isset($_SESSION["userCode"])){
		include("../inc/_body_header.php");
		?>
			<div id="center_body"></div>
		<?
		include("../inc/_body_footer.php");

		if ($_GET["gubun"] == "centerReg"){
		?>
			<script>
				_centerReg('reg','<?=$_GET["mCode"];?>','<?=$_GET["mKind"];?>','','');
			</script>
		<?
		}else if($_GET["gubun"] == "centerList"){
		?>
			<script>
				_centerList();
			</script>
		<?
		}else if($_GET["gubun"] == "yoyangsaReg"){
		?>
			<script>
				_centerYoyReg('reg','<?=$_GET["mCode"];?>','<?=$_GET["mKind"];?>','','','<?=$_GET["mKey"];?>','','');
			</script>
		<?
		}else if($_GET["gubun"] == "yoyangsaList"){
		?>
			<script>
				_centerYoyList();
			</script>
		<?
		}else if($_GET["gubun"] == "sugupjaReg"){
		?>
			<script>
				_sugupjaReg('reg','<?=$_GET["mCode"];?>','<?=$_GET["mKind"];?>','<?=$_GET["mJumin"];?>');
			</script>
		<?
		}else if($_GET["gubun"] == "sugupjaList"){
		?>
			<script>
				_sugupjaList();
			</script>
		<?
		}else{
			if ($_SESSION["userLevel"] == 'A'){
				echo '<script>location.replace("../center/center.php?gubun=search");</script>';
			}else{
				echo '<script>location.replace("../work/work_real.php?mian=true");</script>';
			}
		}
	}else{
	?>
		<style type="text/css">
		*{
				margin:0px;  padding: 0px; border:0px  border-collapse:collapse;
				font-family: "돋움",Dotum,"굴림",Gulim,AppleGothic,Sans-serif;
				font-size: 12px;
				LINE-HEIGHT: 1.5em;
		}

		ol,ul{list-style-type:none; margin:0; padding:0;}
		h1,h2,h3,h4,h5,h6{font-size:12px ;font-weight:normal; margin:0; padding:0;}
		a{color:#565f6b;text-decoration:none;}
		a:hover{color:#208f9fc;}

		select, input
		{
			MARGIN:0px; padding-left:4px; padding-right:4px;  height:1.5em;
			border-top: 1px solid #b7b7b7;
			border-right: 1px solid #d4d4d4;
			border-bottom: 1px solid #d4d4d4;
			border-left: 1px solid #b7b7b7;
		}

		/* 센터정렬
		================================== */

		body {
		  text-align: center;
		  min-width: 1024px;
			background-color: #f5f5f5;
			color:#565f6b;
		}

		img{border:0}

		#wrapper {
		  width: 475px;
			height: 358px;
		  margin: 0 auto;
		  text-align: left;
			background: #f5f5f5 url(../image/bg.gif) no-repeat top;
			margin-top: 78px;
		}

		/* 스크롤바색상
		================================== */

		body{
				scrollbar-face-color: #ffffff;			/*바 표면색*/
				scrollbar-shadow-color: #e4e4e4;		/*바 오른쪽과아래색*/
				scrollbar-highlight-color: #e4e4e4;		/*바 왼쪽과 위쪽색*/
				scrollbar-3dlight-color: #ffffff;
				scrollbar-darkshadow-color: #ffffff;
				scrollbar-track-color: #ffffff;			/*트랙*/
				scrollbar-arrow-color: #e4e4e4;			/*화살표*/
		}

		/*---------------------------------*/

		#c_login {
			width:264px;
			height:53px;
			position:relative; top:207px; left:124px;
		}

		#c_login ul{margin:0;}
		#c_login  li {list-style-type:none;}
		#c_login  li label {padding-right:11px;}

		#c_login a.care_ok
		{
			width:62px;
			height:46px;
			background: url(../image/ok_btn.gif) no-repeat left top;
			display:block;
		}
		#c_login a.care_ok:hover { background-image: url(../image/ok_btn_a.gif); }

		#login_info{width:350px; margin:0 auto; margin-top:290px; }
		#login_info li{display:inline; padding-left:8px;}
		#login_info li strong{color:#75723c}
		#login_info li a { background: url(../image/txt_icon.gif) no-repeat left center; padding-left:8px;}
		#login_info li a:hover{ text-decoration:underline;}
		</style>
		<script language="javascript">
		<!--
		function _Login(){
			if (document.login.uCode.value == ''){
				alert('아이디를 입력하여 주십시오.');
				document.login.uCode.focus();
				return;
			}

			if (document.login.uPass.value == ''){
				alert('비밀번호를 입력하여 주십시오.');
				document.login.uPass.focus();
				return;
			}

			//__setCookie('menuIndex', '', 1);
			//__setCookie('menuSeq', '', 1);

			document.login.action = 'login_ok.php';
			document.login.submit();
		}
		//-->
		</script>
		<div id="wrapper">
			<form name="login" method="post">
			<table id="c_login" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td style="border:none;">
						<ul>
							<li style="padding-bottom:3px;"><label for="tel"><img src="../image/tel_txt.gif" alt="전화번호" /></label><input name="uCode" type="text" maxlength="15" onKeyPress="if(event.keyCode == 13 && this.value != ''){document.login.uPwd.focus();}"></li>
							<li><label for="pw"><img src="../image/pw_txt.gif" alt="비밀번호" /></label><input name="uPass" type="password" maxlength="15" onKeyPress="if(event.keyCode == 13 && this.value != ''){_Login();}"></li>
						</ul>
					</td>
					<td style="border:none;"><a href="#" class="care_ok" onclick="_Login();"></a></td>
				</tr>
			</table>
			</form>
		</div>
		<script language='javascript'>
			window.onload = function(){
				document.login.uCode.focus();
			}
		</script>
<?
	}
	include("../inc/_footer.php");
?>