<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*
	 * 게시판 카테고리
	 */

	$type	= $_POST['type'];
	$mode	= $_POST['mode'];
	$cd		= $_POST['cd'];
	$val	= $_POST['val'];

	if ($mode == 'use'){
		$col = 'use_yn';
	}else if ($mode == 'seq'){
		$col = 'seq';
	}else{
		$conn->close();
		exit;
	}

	$sql = 'UPDATE	board_category
			SET		'.$col.'	= \''.$val.'\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	brd_type	= \''.$type.'\'
			AND		dom_id		= \''.$gDomainID.'\'
			AND		cd			= \''.$cd.'\'';

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