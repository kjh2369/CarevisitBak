<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];

	$sql = 'SELECT	*
			FROM	ie_bm_compay
			WHERE	domain_id	= \''.$gDomainID.'\'
			AND		year		= \''.$year.'\'';

	$row = $conn->get_array($sql);

	echo 'E1='.$row['compay_1']
		.'&E2='.$row['compay_2']
		.'&E3='.$row['compay_3']
		.'&E4='.$row['compay_4']
		.'&E5='.$row['compay_5']
		.'&E6='.$row['compay_6']
		.'&E7='.$row['compay_7']
		.'&E8='.$row['compay_8']
		.'&E9='.$row['compay_9']
		.'&E10='.$row['compay_10']
		.'&E11='.$row['compay_11']
		.'&E12='.$row['compay_12'];

	$sql = 'SELECT	CAST(RIGHT(yymm,2) AS unsigned) AS month
			,		SUM(amt) AS amt
			FROM	ie_bm_compay_item
			WHERE	domain_id	= \''.$gDomainID.'\'
			AND		ie_gbn		= \'I\'
			AND		LEFT(yymm,4)= \''.$year.'\'
			GROUP	BY yymm';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		echo '&I'.$row['month'].'='.$row['amt'];
	}

	$conn->row_free();



	$sql = 'SELECT	CAST(RIGHT(yymm,2) AS unsigned) AS month, amt
			FROM	ie_bm_compay_target
			WHERE	domain_id	= \''.$gDomainID.'\'
			AND		LEFT(yymm,4)= \''.$year.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		echo '&T'.$row['month'].'='.$row['amt'];
	}

	$conn->row_free();

	include_once('../inc/_db_close.php');
?>