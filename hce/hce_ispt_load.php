<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');
	include_once('../inc/_hce.php');

	/*********************************************************
	 *	사정기록지
	 *********************************************************/
	$orgNo = $_SESSION['userCenterCode'];
	$orgType = $hce->SR;

	$IPIN	= $hce->IPIN;
	$rcpt	= $hce->rcpt;

	$menuId = $_POST['menuId'];
	$hcptSeq= $_POST['hcptSeq'];

	if ($menuId == '1'){
		//기본
	}

	include_once('../inc/_db_close.php');
?>