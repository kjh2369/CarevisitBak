<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$yymm	= $myF->dateAdd('month', -1, $year.'-'.$month.'-01', 'Ym');

	$sql = 'SELECT	COUNT(*)
			FROM	ie_bm_salary
			WHERE	org_no	= \''.$orgNo.'\'
			and		yymm	= \''.$yymm.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt < 1){
		$conn->close();
		echo 7;
		exit;
	}

	$sql = 'DELETE
			FROM	ie_bm_salary
			WHERE	org_no	= \''.$orgNo.'\'
			and		yymm	= \''.$year.$month.'\'';
	$query[] = $sql;

	$sql = 'INSERT	INTO ie_bm_salary (org_no,yymm,jumin,job,salary,insu_amt,retire_amt,insert_id,insert_dt)
			SELECT	org_no, \''.$year.$month.'\', jumin, job, salary, insu_amt, retire_amt, \''.$_SESSION['userCode'].'\', NOW()
			FROM	ie_bm_salary
			WHERE	org_no	= \''.$orgNo.'\'
			and		yymm	= \''.$yymm.'\'';
	$query[] = $sql;

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>