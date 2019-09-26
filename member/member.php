<?
	include_once('../inc/_header.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$memNo = $_POST['mem_no'];
	$jumin2	= $_POST['m_jumin2'];
	$user_id = $_POST['user_id'];
	$code = $_POST['code'];
?>

<script language='javascript'>
<!--

function join_ok(){
	var f = document.f;
	var id_pattern = /^[A-Zaa-z0-9]{4,15}$/;
	var mail_pattern = /^(\S+)\@(\S+)\.(\S+)$/;
	
	
	/*
	var code_cnt = document.getElementsByName('center_code').length;
	var center_code = document.getElementsByName('center_code');
	var select_code=0;
	
	for(var i=0; i<code_cnt; i++){
		if(center_code[i].checked){
			select_code= center_code[i].checked;
		}
	}

	if(select_code == 0){
		alert('가입하실 기관을 선택해주십시오.');
		return false;
	}
	*/

	if (!id_pattern.test(f.user_id.value)){
		alert('아이디는 영문과 숫자로 이루어지고 4~15글자수로 입력하여 주십시오.');
		f.user_id.focus();
		return;
	}

	if (f.user_pw.value.length < 4){
		alert('비밀번호를 4~15글자수로 입력하여 주십시오.');
		f.user_pw.focus();
		return;
	}

	if (f.user_pw.value != f.user_pw_chk.value){
		alert('비밀번호확인이 맞지않습니다. 다시입력해주십시오.');
		f.user_pw_chk.value;
		return;
	}

	if (f.user_tel.value == ''){
		alert('전화번호를 입력해 주십시오.');
		f.user_tel.focus();
		return;
	}

	if (f.user_mobile.value == ''){
		alert('휴대폰번호를 입력해 주십시오.');
		f.user_mobile.focus();
		return;
	}

	var mail_addr = f.email_nm.value+'@';

	if (f.email_host_list.value == 'self'){
		mail_addr += f.email_host.value;
	}else{
		mail_addr += f.email_host_list.value;
	}

	if (!mail_pattern.test(mail_addr)){
		alert('메일주소 입력방식이 잘 못되었습니다.');
		f.email_nm.focus();
		return;
	}

	f.action = "../member/member_ok.php";
	f.submit();

}

//아이디중복체크
function id_chk(){
	
	var f = document.f;
	
	document.idChk.user_id.value = f.user_id.value;
	document.idChk.submit();
}

//이메일
function mail_chk() {
	var f = document.f;
	if (f.email_host_list.value == 'self'){
		 f.email_host.value = '';
		 f.email_host.style.display = '';
		 f.email_host.focus();
		 f.email_host_list.selectedIndex = 0;
	} else {
		 f.email_host.value = f.email_host_list.value;
		 f.email_host.style.display='none';
	}
}

//기관코드를 선택했을 시 idChk 폼으로 기관코드값을 넘기고 기존에있던 요양사 전번,주소를 상세정보입력란에 디폴트로 보여줌
function center_info(code, phone, mobile, ypost1, ypost2, yjuso1, yjuso2, email) {
	var f = document.f;

	document.idChk.mCode.value = code;
	f.user_tel.value = phone;
	f.user_mobile.value = mobile;
	f.post1.value = ypost1;
	f.post2.value = ypost2;
	f.addr1.value = yjuso1;
	f.addr2.value = yjuso2;

	var tmp_mail = email.split('@');

	if (tmp_mail.length != 2) return;

	f.email_nm.value = tmp_mail[0];
	f.email_host_list.value = tmp_mail[1];

	if (f.email_host_list.value == ''){
		f.email_host_list.value = 'self';
		f.email_host.value = tmp_mail[1];
	}else{
		mail_chk();
	}
}
-->
</script>
<style>
	.m_title  {font-size:12px; font-weight:bold; background-color: #F7F7F7; text-align:left; padding-left:15px;  }
	.m_title_c {font-size:12px; font-weight:bold; background-color: #F7F7F7; text-align:center; }
</style>
<link href="../css/member.css" rel="stylesheet" type="text/css">
<script language='javascript' src='../js/script.js'></script>
<div id="top_box">
	<div class="top_ci">
		<a href="#" onclick="__go_menu('');"><img src="/admin_img/carevisit/top/ci.png" /></a>
	</div>
</div>
<iframe name="backform" width="0" height="0" frameborder="0" border="0"></iframe>
<form name="f" method="post">
<table style="clear:both; border:none;" width="1024px">
	<tr>
		<td align="center" class="noborder">
			<div style="width:734px; text-align:left;">
				<div style="height:30px; font-size:18; font-weight:bold; color:#1b57b3; margin-top:10px; border-bottom:1px solid #a6c0f3;">회원가입</div>
				<?
					$sql = "select m02_yname
								  ,m02_yjumin
								  ,m02_ycode
								  ,m02_mkind
							  from m02yoyangsa
							 inner join mem_his
						        on mem_his.org_no    = m02_ccode
							   and mem_his.jumin     = m02_yjumin
							   and DATE_FORMAT(mem_his.join_dt,'%Y%m%d') <= DATE_FORMAT(now(),'%Y%m%d')
							   and mem_his.employ_stat = '1'
							 where m02_ccode  = '".$code."'
							   and com_no = '".$memNo."'
							 order by m02_mkind
							 limit 1";
					
					$yoy_info = $conn -> get_array($sql);

					$yname = $yoy_info['m02_yname'];
					$yjumin = $ed->en($yoy_info['m02_yjumin']);
					$kind = $yoy_info['m02_mkind'];


				?>
				<div class="title" style="margin-top:30px;">기본정보</div>
				<table width="100%" style="border:2px solid #a6c0f3;">
					<colgroup>
						<col width="120px">
						<col width="100px;">
						<col width="120px;">
					</colgroup>
					<tr>
						<th class="m_title_c">요양사코드</th>
						<th class="m_title_c">이름</th>
						<th class="m_title_c">생년월일</th>
					</tr>
					<tr>
						<td><?=$yoy_info['m02_ycode'];?></td>
						<td><?=$yoy_info['m02_yname']?></td>
						<td><?=$myF->issToBirthday($yoy_info['m02_yjumin'],'.');?></td>
						<input type="hidden" name="yname" value="<?=$yname;?>">
						<input type="hidden" name="yjumin" value="<?=$yjumin;?>">
					</tr>
				</table>
				<div class="title" style="margin-top:30px;">기관정보</div><!--font color="red">※가입하실 기관코드를 선택해주세요.(선택옵션이 없으면 이미 가입된 기관입니다.)</font-->
				<table width="100%" style="border:2px solid #a6c0f3;">
					<colgroup>
						<col width="100px">
						<col width="120px;">
						<col width="300px;">
					</colgroup>
					<tr>
						<th class="m_title_c">기관코드</th>
						<th class="m_title_c">기관명</th>
						<th class="m_title_c">주소</th>
					</tr><?

					$sql = "select m00_mcode
							      ,m00_mkind
								  ,m00_cname
								  ,m00_caddr1
								  ,m02_ytel
								  ,m02_ytel2
								  ,m02_ypostno
								  ,m02_yjuso1
								  ,m02_yjuso2
								  ,m02_email
								  ,member.code
							  from m02yoyangsa
						inner join m00center
								on m00_mcode = m02_ccode
							   /*and m00_mkind = m02_mkind*/
						inner join mem_his
						        on mem_his.org_no    = m02_ccode
							   and mem_his.jumin     = m02_yjumin
							   and DATE_FORMAT(mem_his.join_dt,'%Y%m%d') <= DATE_FORMAT(now(),'%Y%m%d')
							   and mem_his.employ_stat = '1'
						 left join member
								on member.org_no = m02_ccode
							   and member.jumin = m02_yjumin
							 where m02_ccode  = '".$code."'
							   and com_no	 = '".$memNo."'
							   and m02_mkind = '".$kind."'";
					$mem = $conn -> get_array($sql);
					
					$tel = $myF->phoneStyle($mem['m02_ytel2']);
					$phone = $myF->phoneStyle($mem['m02_ytel']);
					$post = $mem['m02_ypostno'];
					$addr = $mem['m02_yjuso1'];
					$addr_dtl = $mem['m02_yjuso2'];
					$email = $mem['m02_email'];
					$email1 = $email[0];
					$email2 = $email[1];
					
						/*
						$conn->query($sql);
						$conn->fetch();
						$row_count = $conn->row_count();

						for($i=0; $i<$row_count; $i++){
							$row = $conn->select_row($i);

							?>
							<tr><?
								//선택옵션이 없으면 이미 등록된 기관이고, 있으면 등록되지 않은 기관이므로 선택해서 가입을 할수있다.
								if($row['code'] == ''){ ?>
									<td style="text-align:left; padding-left:5px;">
										<input type="radio" class="radio" name="center_code" onclick="center_info('<?=$row['m00_mcode']?>','<?=$myF->phoneStyle($row['m02_ytel2'])?>','<?=$myF->phoneStyle($row['m02_ytel'])?>','<?=substr($row['m02_ypostno'],0,3)?>','<?=substr($row['m02_ypostno'],3,3)?>','<?=$row['m02_yjuso1']?>','<?=$row['m02_yjuso2']?>','<?=$row['m02_email'];?>');" value="<?=$row['m00_mcode'];?>"><?=$row['m00_mcode'];?></td><?
								}else {?>
									<td style="text-align:left; padding-left:27px;"><?=$row['m00_mcode'];?></td><?
								}?>
								<td style="text-align:left; padding-left:5px;"><?=$row['m00_cname'];?></td>
								<td style="text-align:left; padding-left:5px;"><?=$row['m00_caddr1'];?></td>
							</tr><?
						}
						$conn->row_free();
						
						*/


						?>
					<tr><?
						//선택옵션이 없으면 이미 등록된 기관이고, 있으면 등록되지 않은 기관이므로 선택해서 가입을 할수있다.
						?>
						<input type="hidden" name="center_code" value="<?=$mem['m00_mcode'];?>">
						<td style="text-align:left; padding-left:27px;"><?=$mem['m00_mcode'];?></td>
						<td style="text-align:left; padding-left:5px;"><?=$mem['m00_cname'];?></td>
						<td style="text-align:left; padding-left:5px;"><?=$mem['m00_caddr1'];?></td>
					</tr>
				</table>
				<?
					/*
					$sql = "select code
								 , name
								 , jumin
								 , tel
								 , mobile
								 , email
								 , postno
								 , addr
								 , addr_dtl
							  from member
							 where org_no = '".$code."'
							   and code = '".$user_id."'
							   and jumin = '".$yjumin."'";
					$mem = $conn->get_array($sql);
					*/
				?>
				<div class="title" style="margin-top:30px;">상세정보입력</div>
				<table style="border:2px solid #a6c0f3;" width="100%">
					<colgroup>
						<col width="200px">
						<col width="500px">
					</colgroup>
					<tr>
						<th class="m_title">아이디</th>
						<td style="text-align:left; padding-left:10px; border-bottom:1px solid #a6c0f3;">
							<input name="user_id" type="text" value="" onblur="id_chk();" maxlength="15" style="text-align:left; margin-right:0;"> ※ 아이디는 영문과 숫자만 가능하며 4~15글자수까지 입력 가능합니다.
						</td>
					</tr>
					<tr>
						<th class="m_title">비밀번호</th>
						<td style="text-align:left; padding-left:10px; border-bottom:1px solid #a6c0f3;">
							<input name="user_pw" type="password" value="" maxlength="15"> ※ 4~15글자수까지 입력 가능합니다.
						</td>
					</tr>
					<tr>
						<th class="m_title" onblur="">비밀번호 확인</th>
						<td style="text-align:left; padding-left:10px; border-bottom:1px solid #a6c0f3;">
							<input name="user_pw_chk" type="password" value="" maxlength="15" onKeyUp="if(this.value.length == 15){document.f.user_pw.focus();}">
						</td>
					</tr>
					<tr>
						<th class="m_title">전화번호</th>
						<td style="text-align:left; padding-left:10px; border-bottom:1px solid #a6c0f3;">
							<input name="user_tel" type="text" value="<?=$tel?>" maxlength="11" class="phone" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);" onKeyDown="__onlyNumber(this)">
						</td>
					</tr>
					<tr>
						<th class="m_title">휴대폰번호</th>
						<td style="text-align:left; padding-left:10px; border-bottom:1px solid #a6c0f3;">
							<input name="user_mobile" type="text" value="<?=$phone?>" maxlength="11" class="phone" onFocus="__replace(this, '-', '');" onBlur="__getPhoneNo(this);" onKeyDown="__onlyNumber(this)">
						</td>
					</tr>
					<tr>
						<th class="m_title" rowspan="2">주소</th>
						<td style="text-align:left; padding-left:10px; border-bottom:none;">
							<input name="post1" type="text" value="" maxlength="6" class="phone" style="text-align:center;" onKeyDown="__onlyNumber(this)" onFocus="this.select();">
							<!--a href="#" onClick="__helpAddress(document.f.post1, document.f.post2, document.f.addr1, document.f.addr2, 'YES');"><img src="../member/img/m_btn_addr.gif" align="absmiddle" /></a-->
						</td>
						<tr>
							<td style="text-align:left; padding-left:10px; border-top:none; border-bottom:1px solid #a6c0f3;">
								<input name="addr1" type="text" value="<?=$addr?>" maxlength="20" style="width:290px;">
								<input name="addr2" type="text" value="<?=$addr_dtl?>" maxlength="20" style="width:200px; padding-left:10px;">
							</td>
						</tr>
					</tr>
						<th class="m_title">이메일</th>
						<td style="text-align:left; padding-left:10px; border-bottom:1px solid #a6c0f3;">
							<input name="email_nm" type="text" value="<?=stripslashes($email1)?>" maxlength="20" size="15" style="ime-mode:disabled;"> @
							<input name="email_host" type="text" value="<?=stripslashes($email2)?>" maxlength="20" size="15" style="ime-mode:disabled;">
							<select name="email_host_list" style="width:auto;" onChange="mail_chk();">
								<option value="self">직접입력</option>
								<option value="naver.com"> naver.com </option>
								<option value="hanmail.net"> hanmail.net </option>
								<option value="nate.com"> nate.com </option>
								<option value="chol.com"> chol.com </option>
								<option value="cyworld.com"> cyworld.com </option>
								<option value="dreamwiz.com"> dreamwiz.com </option>
								<option value="empal.com"> empal.com </option>
								<option value="empas.com"> empas.com </option>
								<option value="freechal.com"> freechal.com </option>
								<option value="gmail.com"> gmail.com </option>
								<option value="hanafos.com"> hanafos.com </option>
								<option value="hotmail.com"> hotmail.com </option>
								<option value="lycos.co.kr"> lycos.co.kr </option>
								<option value="netian.com"> netian.com </option>
								<option value="netsgo.com"> netsgo.com </option>
								<option value="paran.com"> paran.com </option>
								<option value="yahoo.com"> yahoo.com </option>
								<option value="yahoo.co.kr"> yahoo.co.kr </option>
							</select>
						</td>
					</tr>
				</table>
				<!--가입-->
				<div align="center" style="margin-top:50px;">
					<a onclick="join_ok();"><img src="../member/img/btn_join.gif" /></img></a>
					<a onclick="location.href='../member/join.php?join=YES';"><img src="../member/img/btn_can.gif" /></img></a>
				</div>
			</div>
		</td>
	<tr>
</table>
<div id="main_copy" style="margin-top:30px;">
	<div>COPYRIGHT(C) 2011 GOODEOS ALL RIGHTS RESERVED</div>
</div>
</form>
<form name="idChk" method="post" target="backform" action="id_chk.php">
<input name="user_id" type="hidden" value="">
<input name="mCode" type="hidden" value="<?=$code;?>">
</form>
<?
	include_once('../inc/_footer.php');
?>