<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	$sql = 'DELETE
			FROM	t01iljung
			WHERE	t01_ccode		= \''.$orgNo.'\'
			AND		t01_mkind		= \'0\'
			AND		t01_status_gbn	= \'9\'
			AND		LEFT(t01_sugup_date, 6) = \''.$yymm.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>