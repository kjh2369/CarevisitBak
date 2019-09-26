<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	//한재협 로그인
	$id		= StrToUpper($_POST['uCode']);
	$pass	= StrToUpper($_POST['uPass']);

	$sql = 'SELECT	admin_pw
			,		admin_nm
			FROM	mst_admin
			WHERE	admin_gbn	= \'HAN\'
			AND		admin_id	= \''.$id.'\'
			AND		use_yn		= \'Y\'';

	$login	= $conn->get_array($sql);

	if ($pass != $login['admin_pw']){
		$conn->close();?>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript">
			alert('아이디 및 비밀번호 오류입니다. 확인 후 다시 시도하여 주십시오.');
			location.replace('../index.html');
		</script><?
		exit;
	}

	$_SESSION["userCode"]	= $id;
	$_SESSION["userNo"]		= $_SESSION["userCode"];
	$_SESSION['userName']	= $login['admin_nm'];
	$_SESSION["userPass"]	= $pass;
	$_SESSION['userLevel']	= 'HAN';
	$_SESSION["userGubun"]	= '한국재가협회';
	$_SESSION["userBranchCode"]	= 'HAN';
	$_SESSION["userCenterCode"]	= 'HAN';
	$_SESSION["userCenterGiho"]	= 'HAN';
	$_SESSION["userCenterName"]	= '한국재가협회';
	$_SESSION["top_print_name"]	= $_SESSION["userGubun"];

	include_once("../inc/_db_close.php");
?>
<script type="text/javascript">
	location.replace('../main/main.php');
</script>