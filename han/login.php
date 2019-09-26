<style>
ul{list-style-type:none; margin:0; padding:0;}
#login_box {width: 587px;  height:245px;margin: 0 auto; margin-top:150px;border-top:1px solid #039de6;}
#login_box1{position:relative; float:left; width: 587px; height:200px; background:url(../img/admin/img1.gif)  no-repeat  left top;}
#login_box2{position:relative;float:left; width: 587px; height:45px; background:url(../img/admin/img_copy.gif)  no-repeat  left top;}
#login_ctn{width: 322px; height:96px;  position:absolute; top:52px;left:243px;}
#login_ctn li{width: 322px; height:28px; margin-bottom:5px; text-align:left;}
#login_ctn li input{
	float:left; margin:0; padding:0; width: 129px; height:12px; line-height:12px; border:none;
	background-color:transparent; color:#ffffff; margin-left:75px; margin-top:7px; vertical-align:middle;
}
#login_ctn li.l_title{height:30px;}
#login_ctn li.l_id{background:url(../img/admin/bg_id.gif)  no-repeat  left top;}
#login_ctn li.l_pw{background:url(../img/admin/bg_pw.gif)  no-repeat  left top;}
#login_ctn li.l_pw a{width:105px; height:28px; display:block; float:right; background:url(../img/admin/btn_login.gif)  no-repeat  left top;}
</style>
<script type="text/javascript">
	function lfLogin(){
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

		document.login.action = '../han/login_ok.php';
		document.login.submit();
	}
</script>
<form name="login" method="post">
<link href="../accHY/css/style.css" rel="stylesheet" type="text/css">
<div style="padding-left:200px;">
	<fieldset>
		<legend>로그인</legend>
		<div id="login">
			<ul>
			<li style="margin-bottom:3px;"><label for="uCode"><img src="../accHY/img/txt_id.gif"  alt="아이디 입력"/></label><input id="uCode" name="uCode" type="text" style="ime-mode:inactive;"></li>
			<li><label for="uPass"><img src="../accHY/img/txt_pw.gif"  alt="비밀번호 입력"/></label><input id="uPass" name="uPass" type="password"></li>
			</ul>
			<div class="btn_ok"><a style="cursor:default;" onclick="lfLogin();"><img src="../accHY/img/btn_login.gif" title="로그인" alt="로그인" /></a></div>
		</div>
	</fieldset>
</div>
</form>