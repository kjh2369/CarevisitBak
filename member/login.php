
<script language="javascript">

	//기관코드,아이디,비밀번호 입력체크
	//login_ok.php로 넘겨 기관코드,아이디,비밀번호 맞는지 틀린지 유무체크

	function Login(){
		if (document.login.mCode.value == ''){
			alert('기관코드를 입력하여 주십시오.');
			document.login.mCode.focus();
			return;
		}
		
		_Login();
	}

	// 기관코드 찾기
	function find_centerCode(p_code){
		var modal = showModalDialog('../inc/_find_center.php?join=YES', window, 'dialogWidth:800px; dialogHeight:600px; dialogHide:yes; scroll:no; status:yes');

		if (modal == undefined){
			return false;
		}

		var mcode = __getObject(p_code);

		mcode.value = modal[0];

		return true;
	}
</script>
<script type="text/javascript" src="../js/member.js"></script>
<link href="../css/style.css" rel="stylesheet" type="text/css">
<link href="../css/login1.css" rel="stylesheet" type="text/css">
<form name="login" method="post">
<div id="login_box">
	<div id="log_box">
		<div style="width:768px; height:201px;">
			<!--left_login-->
			<div id="log_box2">
				<div id="log_login">
					<ul>
						<li style="margin-bottom:10px;"><label><img src="../member/img/login/txt_code.gif"><input type="text" name="mCode" value="<?=$mCode?>" style="ime-mode:inactive;"></input></label></li>
						<li><label><img src="../member/img/login/txt_id.gif"><input type="text" name="uCode" value="<?=$id_check == 'Y' ? $uCode : '';?>" style="ime-mode:inactive;" onkeydown="if(event.keyCode == 13 && this.value != ''){document.getElementById('uPass').focus();}" ></input></label></li>
						<li><label><img src="../member/img/login/txt_pw.gif"><input type="password" name="uPass" onkeydown="if(event.keyCode == 13 && this.value != ''){Login();}" ></input></label></li>
					</ul>
					<div class="log_btn">
						<a href="#" style="margin-bottom:12px;" onclick="find_centerCode('mCode');"><img src="../member/img/login/btn_code.gif"></a>
						<a href="#" onclick="Login();"><img src="../member/img/login/btn_login.gif"></a>
					</div>
					<div class="id_check">
						<label><input type="checkbox" name="id_check" id="id_check" value="Y"><img src="../member/img/login/txt_id_s.gif"></label>
					</div>
				</div>
			</div>
			<!--right-->
			<div id="log_box3"><img src="../member/img/login/img_ip.gif" border="0" usemap="#Map" />
				<map name="Map" id="Map">
				<area shape="rect" coords="183,10,293,31" href="#" onclick="location.href='../member/id_pwd_find.php?join=YES'"/>
				<area shape="rect" coords="184,54,244,74" href="#" onclick="_mem_join(document.login);"/>
				</map>
			</div>
		</div>
		<!--copy-->
		<div style="width:638px; margin-top:14px; margin-left:64px;"><img src="../member/img/login/img_copy.gif" border="0" usemap="#Map2" />
			<map name="Map2" id="Map2"><area shape="rect" coords="480,0,637,21" href="../img/login/IE8-WindowsXP-x86-KOR.exe" /></map>
		</div>
	</div>
</div>
</form>
