<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');


	/*********************************************************

		파라메타

	*********************************************************/
	$code		= $_POST['code'];
	$mode		= $_POST['mode'];
	$year		= $_POST['year'];
	$month		= $_POST['month'];
	$svcGbn		= $_POST['svcGbn'];		//급여종류
	
	parse_str($_POST['param'], $para);

	if (is_numeric($month)){
		$month = (intval($month) < 10 ? '0' : '').intval($month);
	}

	$target = $mode;
	
	$tableBorderStyle = '';

	echo '<div id=\'tblList\'>';

	if (Is_File('./sw_work_emp_list_sub.php')){
		include_once('./sw_work_emp_list_sub.php');
	}else{
		echo 'ERROR';
	}

	echo '</div>';

	include_once('../inc/_db_close.php');
?>