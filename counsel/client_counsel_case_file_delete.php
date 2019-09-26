<?
	include_once("../inc/_db_open.php");
	include_once("../inc/_http_uri.php");

	$code			= $_SESSION['userCenterCode'];
	$counselId		= $_POST['id'];
	$counselYymm	= $_POST['yymm'];
	$counselSeq		= $_POST['seq'];
	$counselNo		= $_POST['no'];

	$sql = 'DELETE
			FROM	counsel_file
			WHERE	org_no		= \''.$code.'\'
			AND		counsel_id	= \''.$counselId.'\'
			AND		yymm		= \''.$counselYymm.'\'
			AND		seq			= \''.$counselSeq.'\'
			AND		no			= \''.$counselNo.'\'';

	if ($conn->execute($sql)){
		@UnLink('../file/0010/'.$code.$counselId.$counselYymm.$counselSeq.$counselNo);
		echo 1;
	}else{
		echo 9;
	}

	include_once("../inc/_db_close.php");
?>