<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$year	= $_POST['year'];

	$sql = 'SELECT	COUNT(*)
			FROM	ie_bm_compay
			WHERE	domain_id	= \''.$gDomainID.'\'
			AND		year		= \''.$year.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$sql = 'UPDATE	ie_bm_compay
				SET		compay_1	= \''.str_replace(',','',$_POST['1']).'\'
				,		compay_2	= \''.str_replace(',','',$_POST['2']).'\'
				,		compay_3	= \''.str_replace(',','',$_POST['3']).'\'
				,		compay_4	= \''.str_replace(',','',$_POST['4']).'\'
				,		compay_5	= \''.str_replace(',','',$_POST['5']).'\'
				,		compay_6	= \''.str_replace(',','',$_POST['6']).'\'
				,		compay_7	= \''.str_replace(',','',$_POST['7']).'\'
				,		compay_8	= \''.str_replace(',','',$_POST['8']).'\'
				,		compay_9	= \''.str_replace(',','',$_POST['9']).'\'
				,		compay_10	= \''.str_replace(',','',$_POST['10']).'\'
				,		compay_11	= \''.str_replace(',','',$_POST['11']).'\'
				,		compay_12	= \''.str_replace(',','',$_POST['12']).'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	domain_id	= \''.$gDomainID.'\'
				AND		year		= \''.$year.'\'';
	}else{
		$sql = 'INSERT INTO ie_bm_compay (domain_id,year,compay_1,compay_2,compay_3,compay_4,compay_5,compay_6,compay_7,compay_8,compay_9,compay_10,compay_11,compay_12,insert_id,insert_dt) VALUES (
				 \''.$gDomainID.'\'
				,\''.$year.'\'
				,\''.str_replace(',','',$_POST['1']).'\'
				,\''.str_replace(',','',$_POST['2']).'\'
				,\''.str_replace(',','',$_POST['3']).'\'
				,\''.str_replace(',','',$_POST['4']).'\'
				,\''.str_replace(',','',$_POST['5']).'\'
				,\''.str_replace(',','',$_POST['6']).'\'
				,\''.str_replace(',','',$_POST['7']).'\'
				,\''.str_replace(',','',$_POST['8']).'\'
				,\''.str_replace(',','',$_POST['9']).'\'
				,\''.str_replace(',','',$_POST['10']).'\'
				,\''.str_replace(',','',$_POST['11']).'\'
				,\''.str_replace(',','',$_POST['12']).'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

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