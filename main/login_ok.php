<?
	include_once("../inc/_db_open.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	echo '<script src="../js/script.js" type="text/javascript"></script>';

	$mCode = $_REQUEST["mCode"];
	$uCode = trim($_REQUEST["uCode"]);
	$uPass = trim($_REQUEST["uPass"]);
	$loc   = $_REQUEST['loc'];
	$today = Date('Ymd');

	$host   = $myF->host();
	$domain = $myF->_get_domain();

	/*
	if ($debug){
		echo $ed->de('%A5F%E7%81%D3%E2%9Bt%AC%04%D9').'/'.$ed->de($uCode).'<br>';
		echo $ed->de('%A7F%E6%85%D7%93%F8%00').'/'.$ed->de($uPass).'<br>';
		print_r($_POST);
		exit;
	}
	*/


	# 아이디를 소문자로 치환한다.
	$uCode = strtolower($uCode);

	$sql = 'SELECT	*
			FROM	han_member
			WHERE	id	= \''.$uCode.'\'
			AND		pswd= \''.$uPass.'\'
			';
	$row = $conn->get_array($sql);

	if (is_array($row)){
		$_SESSION['adminFlag'] = $row['admin_yn'];

		if ($_SESSION['adminFlag'] == 'Y'){
			$_SESSION["userGubun"] = "관리자";
		}else{
			$_SESSION["userGubun"] = "사용자";
		}

		if ($row['mem_cd'] == ''){
			$_SESSION['userLevel'] = 'C';
		}else{
			$_SESSION['userLevel'] = 'P';
		}

		$_SESSION["userCode"] = $uCode;
		$_SESSION["userName"] = $row["mname"];
		$_SESSION["userCenterCode"] = $row["org_no"];
		$_SESSION['userArea'] = $row['area_cd'];
		$_SESSION['userGroup'] = $row['group_cd'];

		if ($_SESSION['adminFlag'] != 'Y'){
			$sql = 'SELECT	m00_code1, m00_store_nm
					FROM	m00center
					WHERE	m00_mcode = \''.$_SESSION["userCenterCode"].'\'
					';
			$row = $conn->get_array($sql);

			$_SESSION["userCenterGiho"] = $row["m00_code1"];
			$_SESSION["userCenterName"] = stripslashes($row["m00_store_nm"]);
		}

		$_SESSION['userTypeSR'] = 'S';
	}else{
		echo out_msg(11);
		exit;
	}

	/*********************************************************

		도메인에 따른 이미지 경로

	*********************************************************/
		$_SESSION['imgPath'] = $myF->_get_host_image_path();
	/********************************************************/

	echo "<script>
			location.replace('../main/main.php');
		  </script>";

	include_once("../inc/_db_close.php");

	function out_msg($idx){
		$msg = "<script>
				alert('$idx.아이디 및 비밀번호 오류입니다. 확인 후 다시 시도하여 주십시오.');
				location.replace('../index.html');
				</script>";
		return $msg;
	}
?>