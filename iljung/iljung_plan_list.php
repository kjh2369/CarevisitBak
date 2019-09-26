<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code = $_POST['code'];
	$year = $_POST['year'];
	$month = $_POST['month'];

	if (is_numeric($month)){
		$month = (intval($month) < 10 ? '0' : '').intval($month);
	}

	$tableBorderStyle = '';

	echo '<div id=\'tblList\'>';

	if (Is_File('./iljung_plan_detail.php')){
		include_once('./iljung_plan_detail.php');
	}else{
		echo 'ERROR';
	}

	echo '</div>';

	include_once('../inc/_db_close.php');
?>

