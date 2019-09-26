<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;

	$sql = 'SELECT	pop_yn, pop_day, stop_yn, stop_dt
			FROM	cv_acct_pop_set
			WHERE	acct_ym = \''.$yymm.'\'';

	$row = $conn->get_array($sql);

	echo 'popYn='.$row['pop_yn']
		.'&popDay='.$row['pop_day']
		.'&stopYn='.$row['stop_yn']
		.'&stopDt='.$row['stop_dt'];

	Unset($row);

	include_once('../inc/_db_close.php');
?>