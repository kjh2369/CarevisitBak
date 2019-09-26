<?
	ob_start();

	/*
		기관코드,아이디,비번을 받아서 유무체크하고 다 맞다면 메인페이지로 이동
	*/
	include_once("../inc/_db_open.php");
	include_once('../inc/_myFun.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	echo '<script src="../js/script.js" type="text/javascript"></script>';

	$m_code = $_POST['m_code'];
	$user_id = $_POST['user_id'];
	$user_pass = $_POST['user_pass'];
	$id_check = $_POST['id_check'];

	$sql = "select count(*)
			  from member
			 where org_no = '".$m_code."'";
	$lgn_code = $conn -> get_data($sql);

	//기관코드 유무체크
	if($lgn_code[0] > 0){
		$sql = "select count(*)
			  from member
			 where org_no = '".$m_code."'
			   and   code = '".$user_id."'";
		$lgn_id = $conn -> get_data($sql);

		//아이디 유무체크
		if($lgn_id[0] > 0){
			$sql = "select count(*)
			  from member
			 where org_no = '".$m_code."'
			   and   code = '".$user_id."'
			   and   pswd = '".$user_pass."'";
			$lgn_pass = $conn -> get_data($sql);

			//비번 유무체크
			if($lgn_pass[0] > 0){
				setcookie("code", $m_code, 0, "/");
				$_SESSION['user_id'] = $user_id;
				$_SESSION['user_pass'] = $user_pass;
			}else {
			echo "<script language='javascript'>
					alert('비밀번호가 맞지않습니다. 확인 후 다시 시도해주십시오.');
					location.replace('../member/login.php?join=YES&m_code=$m_code&user_id=$user_id&id_check=$id_check');
				  </script>";
			}

		}else {
			echo "<script language='javascript'>
					alert('등록되있는 아이디가 없습니다. 확인 후 다시 시도해주십시오.');
					location.replace('../member/login.php?join=YES&m_code=$m_code');
				  </script>";
		}
	}else {
		echo "<script language='javascript'>
				alert('등록되있는 기관코드가 없습니다. 확인 후 다시 시도해주십시오.');
				location.replace('../member/login.php?join=YES');
			  </script>";
	}
?>
	<script language='javascript'>
		location.replace('../member/main.php?join=YES');
	</script>
<?
	include_once("../inc/_db_close.php");
?>

