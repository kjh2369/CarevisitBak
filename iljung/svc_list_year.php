<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];

	$sql = 'SELECT	MAX(CASE WHEN RIGHT(yymm,2) = \'01\' THEN reg_dt ElSE \'\' END) AS d01
			,		MAX(CASE WHEN RIGHT(yymm,2) = \'02\' THEN reg_dt ElSE \'\' END) AS d02
			,		MAX(CASE WHEN RIGHT(yymm,2) = \'03\' THEN reg_dt ElSE \'\' END) AS d03
			,		MAX(CASE WHEN RIGHT(yymm,2) = \'04\' THEN reg_dt ElSE \'\' END) AS d04
			,		MAX(CASE WHEN RIGHT(yymm,2) = \'05\' THEN reg_dt ElSE \'\' END) AS d05
			,		MAX(CASE WHEN RIGHT(yymm,2) = \'06\' THEN reg_dt ElSE \'\' END) AS d06
			,		MAX(CASE WHEN RIGHT(yymm,2) = \'07\' THEN reg_dt ElSE \'\' END) AS d07
			,		MAX(CASE WHEN RIGHT(yymm,2) = \'08\' THEN reg_dt ElSE \'\' END) AS d08
			,		MAX(CASE WHEN RIGHT(yymm,2) = \'09\' THEN reg_dt ElSE \'\' END) AS d09
			,		MAX(CASE WHEN RIGHT(yymm,2) = \'10\' THEN reg_dt ElSE \'\' END) AS d10
			,		MAX(CASE WHEN RIGHT(yymm,2) = \'11\' THEN reg_dt ElSE \'\' END) AS d11
			,		MAX(CASE WHEN RIGHT(yymm,2) = \'12\' THEN reg_dt ElSE \'\' END) AS d12
			,		SUM(CASE WHEN RIGHT(yymm,2) = \'01\' THEN 1 ElSE 0 END) AS m01
			,		SUM(CASE WHEN RIGHT(yymm,2) = \'02\' THEN 1 ElSE 0 END) AS m02
			,		SUM(CASE WHEN RIGHT(yymm,2) = \'03\' THEN 1 ElSE 0 END) AS m03
			,		SUM(CASE WHEN RIGHT(yymm,2) = \'04\' THEN 1 ElSE 0 END) AS m04
			,		SUM(CASE WHEN RIGHT(yymm,2) = \'05\' THEN 1 ElSE 0 END) AS m05
			,		SUM(CASE WHEN RIGHT(yymm,2) = \'06\' THEN 1 ElSE 0 END) AS m06
			,		SUM(CASE WHEN RIGHT(yymm,2) = \'07\' THEN 1 ElSE 0 END) AS m07
			,		SUM(CASE WHEN RIGHT(yymm,2) = \'08\' THEN 1 ElSE 0 END) AS m08
			,		SUM(CASE WHEN RIGHT(yymm,2) = \'09\' THEN 1 ElSE 0 END) AS m09
			,		SUM(CASE WHEN RIGHT(yymm,2) = \'10\' THEN 1 ElSE 0 END) AS m10
			,		SUM(CASE WHEN RIGHT(yymm,2) = \'11\' THEN 1 ElSE 0 END) AS m11
			,		SUM(CASE WHEN RIGHT(yymm,2) = \'12\' THEN 1 ElSE 0 END) AS m12
			FROM	lg2cv
			WHERE	org_no	= \''.$orgNo.'\'
			AND		use_yn	= \'Y\'
			AND		LEFT(yymm, 4) = \''.$year.'\'';

	$row = $conn->get_array($sql);

	for($i=1; $i<=12; $i++){
		$col1 = 'm'.($i < 10 ? '0' : '').$i;
		$col2 = 'd'.($i < 10 ? '0' : '').$i;
		$data .= ($data ? '&' : '').'m'.$i.'='.$row[$col1];
		$data .= ($data ? '&' : '').'d'.$i.'='.$row[$col2];
	}

	echo $data;

	include_once('../inc/_db_close.php');
?>