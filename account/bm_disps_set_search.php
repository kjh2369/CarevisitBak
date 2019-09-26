<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];

	$sql = 'SELECT	allot_cnt
			,		allot_amt
			,		employ_cnt
			,		deduct_amt
			,		memo
			FROM	ie_bm_disps
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$year.$month.'\'';

	$row = $conn->get_array($sql);

	echo 'allotCnt='.$row['allot_cnt'].'&allotAmt='.$row['allot_amt'].'&employCnt='.$row['employ_cnt'].'&deductAmt='.$row['deduct_amt'].'&memo='.StripSlashes($row['memo']);

	Unset($row);

	include_once('../inc/_db_close.php');
?>