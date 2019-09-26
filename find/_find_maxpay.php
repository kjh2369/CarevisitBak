<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$year = $_REQUEST['year'];
	$lvl  = $_REQUEST['lvl'];

	if (Empty($year)){
		$year = Date('Y');
	}

	$sql = 'SELECT m91_kupyeo
			  FROM m91maxkupyeo
			 WHERE LEFT(m91_sdate,4) <= \''.$year.'\'
			   AND LEFT(m91_edate,4) >= \''.$year.'\'
			   AND m91_code = \''.$lvl.'\'';

	$liPay = $conn->get_data($sql);

	echo $liPay;

	include_once('../inc/_db_close.php');
?>