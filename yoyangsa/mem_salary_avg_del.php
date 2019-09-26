<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_login.php");
	include_once("../inc/_http_uri.php");
	include_once("../inc/_myFun.php");
	include_once("../inc/_ed.php");

	$code   = $_SESSION['userCenterCode'];
	$jumin  = $ed->de($_POST['jumin']);
	$year   = $_POST['year'];
	$hourly = $_POST['hourly'];
	$salary = $_POST['salary'];

	$sql = 'SELECT COUNT(*)
			  FROM salary_avg
			 WHERE org_no = \''.$code.'\'
			   AND year   = \''.$year.'\'
			   AND jumin  = \''.$jumin.'\'';
	$liCnt = $conn->get_data($sql);

	$sql = 'DELETE
			  FROM salary_avg
			 WHERE org_no = \''.$code.'\'
			   AND year   = \''.$year.'\'
			   AND jumin  = \''.$jumin.'\'';

	if ($conn->execute($sql)){
		echo 1;
	}else{
		echo 9;
	}

	include_once("../inc/_db_close.php");
?>