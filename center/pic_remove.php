<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");

	$orgNo	= $_SESSION['userCenterCode'];
	$path	= '../mem_picture/'.$_POST['file'];
	$gbn	= $_POST['gbn'];

	$sql = 'UPDATE	m00center
			SET		';

	if ($gbn == 'ICON'){
		$sql .= '	m00_icon = \'\'';
	}else if ($gbn == 'JIKIN'){
		$sql .= '	m00_jikin = \'\'';
	}else{
		exit;
	}

	$sql .= '
			WHERE	m00_mcode = \''.$orgNo.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();

	if (is_file($path)){
		@unlink($path);
	}

	echo 1;

	include_once("../inc/_db_close.php");
?>