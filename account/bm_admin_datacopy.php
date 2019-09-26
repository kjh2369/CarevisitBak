<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$month	= ($month < 10 ? '0' : '').$month;
	$yymm	= $myF->dateAdd('month', -1, $year.'-'.$month.'-01', 'Ym');

	$sql = 'SELECT	close_yn
			FROM	ie_bm_close_yn
			WHERE	yymm = \''.$year.$month.'\'';

	$close = $conn->get_data($sql);

	if ($close == 'Y'){
		$conn->close();
		echo 7;
		exit;
	}

	$sql = 'SELECT	COUNT(*)
			FROM	ie_bm_salary
			WHERE	yymm	= \''.$yymm.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt < 1){
		$conn->close();
		echo 5;
		exit;
	}

	$sql = 'DELETE
			FROM	ie_bm_salary
			WHERE	yymm	= \''.$year.$month.'\'';
	$query[] = $sql;

	$sql = 'INSERT	INTO ie_bm_salary (org_no,yymm,jumin,job,salary,insu_amt,retire_amt,insert_id,insert_dt)
			SELECT	org_no, \''.$year.$month.'\', jumin, job, salary, insu_amt, retire_amt, \''.$_SESSION['userCode'].'\', NOW()
			FROM	ie_bm_salary
			WHERE	yymm	= \''.$yymm.'\'';
	$query[] = $sql;

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 7;
			 exit;
		}
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>