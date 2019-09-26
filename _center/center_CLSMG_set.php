<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];
	$month	= IntVal($_POST['month']);
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$clsYn	= $_POST['yn'];

	$sql = 'REPLACE INTO cv_close_set VALUES (\''.$yymm.'\',\''.$clsYn.'\')';

	$conn->begin();

	if ($conn->execute($sql)){
		$conn->commit();
	}else{
		$conn->rollback();
	}

	include_once('../inc/_db_close.php');
?>