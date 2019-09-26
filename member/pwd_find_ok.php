<?
/*
* 주민번호체크
주민번호가 있으면 상세등록화면으로 이동 없으면 일치하는주민번호가없다고 메세지창을 띄우고 리턴.
*/
	include_once('../inc/_db_open.php');
	include_once('../inc/_function.php');
	
	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	
	$code = $_POST['p_code'];
	$id = $_POST['p_id'];
	$name = $_POST['p_name'];
	$jumin1 = $_POST['p_jumin1'];
	$jumin2	= $_POST['p_jumin2'];	
	
	$sql = "select m02_yname, m02_email, member.code
			  from m02yoyangsa
		inner join member
			    on org_no = m02_ccode
			   and jumin = m02_yjumin
			 where m02_ccode = '".$code."'
			   and m02_yjumin = '".$jumin1.$jumin2."'
			   and m02_ygoyong_stat = '1'";
	$yoy = $conn -> get_array($sql);
		
	if($yoy['m02_yname'] != $name){ ?>
		<script language='javascript'> 
			alert('직원등록되지 않았거나 이름과 주민번호가 일치하지 않습니다. 다시 입력해주십시오.');
			parent.document.f.p_name.select();
		</script><?

		return false;
	}

	$sql = "select org_no, code, pswd, email
			  from member
			 where jumin = '".$jumin1.$jumin2."'
			   and org_no = '".$code."'";
	$find_mem = $conn -> get_array($sql);
	
	$email = ($yoy['m02_email'] != '' ? $yoy['m02_email'] : $find_mem['email']);

	//db에 저장되있는데 이름과 입력에서 받아온 이름이 같지 않으면 이름과 주민이 일치하지않다. 
	if($find_mem['org_no'] == ''){ ?>
		<script language='javascript'> 
			alert('가입되지않은 기관입니다.');
			parent.document.f.p_code.select();
		</script><?

		return false;
	}
	
	if($find_mem['code'] != $id){ ?>
		<script language='javascript'> 
			alert('아이디가 맞지 않습니다.');
			parent.document.f.p_id.select();
		</script><?

		return false;
	}
	
	ini_set("SMTP", "115.68.110.24"); // SMTP 서버 IP를 입력합니다. (다른 서버를 이용할 수도 있습니다.)
	ini_set("sendmail_from", "admin@carevisit.net"); // 강제로 php.ini -> smtp의 sendmail_from 설정 

	//비밀번호 메일보내기
	$subject = '안녕하세요. carevisit 입니다.';
	//$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
	$id_pswd = "ID : $find_mem[code]\n  PASSWORD : $find_mem[pswd]"; 

	mailer('케어비지트', 'admin@carevisit.co.kr', $email, $subject, $id_pswd, 1);

	//$success_umu = mail($email, $subject, $id_pswd);

	
	//if($success_umu){
		echo "(<script> alert('성공적으로 메일이 발송되었습니다.') </script>')";
	//}else {
	//	echo "(<script> alert('메일 전송에 실패하였습니다. 다시 시도해 주십시오.') </script>')";	
	//}
	?>
	<script language='javascript'>
		parent.document.f.action = "../member/id_pwd_find.php?join=YES";
		parent.document.f.submit();
	</script>
	
	<?
	include_once('../inc/_db_close.php');
?>
