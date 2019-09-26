<?
	/*
		입력한 데이터값을 post로 받아 회원DB에 데이터를 저장하고 로그인 페이지로 이동
	*/
	include_once("../inc/_ed.php");
	//include_once("../inc/_myFun.php");
	include_once("../inc/_db_open.php");


	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

	$user_id = $_POST['user_id'];
	$user_pw_chk = $_POST['user_pw_chk'];
	$user_name = $_POST['yname'];
	$jumin = $ed->de($_POST['yjumin']);
	$user_tel = str_replace('-','', $_POST['user_tel']);
	$user_mobile = str_replace('-','', $_POST['user_mobile']);
	$post1 = $_POST['post1'];
	$post2 = $_POST['post2'];
	$addr1 = $_POST['addr1'];
	$addr2 = $_POST['addr2'];
	$email_nm = addslashes($_POST['email_nm']);
	$email_host = addslashes($_POST['email_host']);
	$email = $email_nm.'@'.$email_host;
	$org_no = $_POST['center_code'];

	$conn->begin();
	/*
	$sql = "update m02yoyangsa
			   set m02_dept_cd = '".$dept_cd."'
			 where m02_ccode = '".$org_no."'
			   and m02_yjumin = '".$jumin."'";
	$conn -> execute($sql);
	*/
	$sql = "insert into member (org_no, code, pswd, name, jumin, tel, mobile, email, postno, addr, addr_dtl, insert_dt)
				  values (
				  '".$org_no."'
				, '".$user_id."'
				, '".$user_pw_chk."'
				, '".$user_name."'
				, '".$jumin."'
				, '".$user_tel."'
				, '".$user_mobile."'
				, '".$email."'
				, '".$post1.$post2."'
				, '".$addr1."'
				, '".$addr2."'
				,now())";

	$conn -> execute($sql);
	$conn->commit();
	?>

	<script language="javascript">
		alert('회원가입을 진심으로 축하합니다.');
		location.href = "../main/logout_ok.php";
	</script>
<?
	include_once("../inc/_db_close.php");
?>
