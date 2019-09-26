<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$gbn	= $_POST['gbn'];
	$name	= $_POST['name'];
	$amt	= str_replace(',','',$_POST['amt']);

	$sql = 'SELECT	IFNULL(MAX(seq),0)+1
			FROM	ie_bm_other_in
			WHERE	org_no	= \''.$orgNo.'\'
			AND		yymm	= \''.$year.$month.'\'';

	$seq = $conn->get_data($sql);

	$sql = 'INSERT INTO ie_bm_other_in (
			 org_no
			,yymm
			,seq
			,gbn
			,subject
			,amt
			,insert_id
			,insert_dt) VALUES (
			 \''.$orgNo.'\'
			,\''.$year.$month.'\'
			,\''.$seq.'\'
			,\''.$gbn.'\'
			,\''.$name.'\'
			,\''.$amt.'\'
			,\''.$_SESSION['userCode'].'\'
			,NOW()
			)';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->close();
		 echo 9;
		 exit;
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>