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

	echo '1='.$row['compay_1']
		.'&2='.$row['compay_2']
		.'&3='.$row['compay_3']
		.'&4='.$row['compay_4']
		.'&5='.$row['compay_5']
		.'&6='.$row['compay_6']
		.'&7='.$row['compay_7']
		.'&8='.$row['compay_8']
		.'&9='.$row['compay_9']
		.'&10='.$row['compay_10']
		.'&11='.$row['compay_11']
		.'&12='.$row['compay_12'];

	include_once('../inc/_db_close.php');
?>