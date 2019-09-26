<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];

	$sql = 'SELECT	*
			FROM	ie_bm_target
			WHERE	org_no	= \''.$orgNo.'\'
			AND		year	= \''.$year.'\'';

	$row = $conn->get_array($sql);
	$data = '';

	for($i=1; $i<=12; $i++){
		$data .= ($data ? '&' : '').$i.'='.$row['amt'.$i];
	}

	Unset($row);

	echo $data;

	include_once('../inc/_db_close.php');
?>