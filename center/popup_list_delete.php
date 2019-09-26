<?
	include_once('../inc/_db_open.php');
	include_once("../inc/_http_uri.php");
	include_once('../inc/_myFun.php');

	$popId	= $_POST['popId'];

	$sql = 'DELETE
			FROM	center_popup
			WHERE	pop_id = \''.$popId.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>