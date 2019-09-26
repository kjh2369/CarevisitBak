<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$type  = $_GET['type'];
	$value = $_GET['value'];

	switch($type){
		case 1:
			$value = $ed->en($value);
			break;

		case 2:
			$value = $ed->de($value);
			break;
	}

	echo $value;

	include_once('../inc/_db_close.php');
?>