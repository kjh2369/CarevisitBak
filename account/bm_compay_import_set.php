<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$gbn	= $_POST['gbn'];
	$year	= $_POST['year'];
	$month	= $_POST['month'];
	$yymm	= $year.($month < 10 ? '0' : '').$month;
	$name	= $_POST['name'];
	$amt	= str_replace(',','',$_POST['amt']);

	$sql = 'SELECT	IFNULL(MAX(seq),0)+1
			FROM	ie_bm_compay_item
			WHERE	domain_id	= \''.$gDomainID.'\'
			AND		ie_gbn		= \''.$gbn.'\'
			AND		yymm		= \''.$yymm.'\'';

	$seq = $conn->get_data($sql);

	$sql = 'INSERT INTO ie_bm_compay_item (
			 domain_id
			,ie_gbn
			,yymm
			,seq
			,subject
			,amt
			,insert_id
			,insert_dt) VALUES (
			 \''.$gDomainID.'\'
			,\''.$gbn.'\'
			,\''.$yymm.'\'
			,\''.$seq.'\'
			,\''.$name.'\'
			,\''.$amt.'\'
			,\''.$_SESSION['userCode'].'\'
			,NOW()
			)';

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