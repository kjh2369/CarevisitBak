<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*
	 * 게시판
	 */

	$orgNo	= $_SESSION['userCenterCode'];
	$mode	= $_POST['mode'];
	$type	= $_POST['type'];
	$cd		= $_POST['cd'];
	$tmpCd	= $cd;

	$val = '';

	while(true){
		$sql = 'SELECT	name, parent
				FROM	board_category
				WHERE	brd_type= \''.$type.'\'
				AND		dom_id	= \''.$gDomainID.'\'
				AND		cd		= \''.$tmpCd.'\'';

		$row = $conn->get_array($sql);
		$val = $row['name'].'|'.$val;

		if ($row['parent'] < 1) break;

		$tmpCd = $row['parent'];
	}

	$val = SubStr($val,0,StrLen($val)-1);
	$val = str_replace('|',' / ',$val);

	echo $val;

	include_once('../inc/_db_close.php');
?>