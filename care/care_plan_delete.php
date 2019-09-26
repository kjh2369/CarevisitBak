<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['sr'];
	$year	= $_POST['year'];
	$sugaCd	= $_POST['code'];

	$sql = 'DELETE
			FROM	care_year_plan
			WHERE	org_no		= \''.$orgNo.'\'
			AND		plan_year	= \''.$year.'\'
			AND		plan_sr		= \''.$SR.'\'
			AND		plan_cd		= \''.$sugaCd.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>