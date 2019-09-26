<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$gbn	= $_POST['gbn'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$seq	= $_POST['seq'];

	$sql = 'DELETE
			FROM	ie_bm_compay_item
			WHERE	domain_id	= \''.$gDomainID.'\'
			AND		ie_gbn		= \'I\'
			AND		yymm		= \''.$year.($month < 10 ? '0' : '').$month.'\'
			AND		seq			= \''.$seq.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>