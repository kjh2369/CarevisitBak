<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $ed->de($_POST['orgNo']);
	$fromDt = str_replace('-', '', $_POST['fromDt']);
	$toDt = str_replace('-', '', $_POST['toDt']);
	$billGbn = $_POST['billGbn'];
	$bill_kind = $_POST['bill_kind'];
	$orgFromDt = $_POST['orgFromDt'];
	$orgBillGbn = $_POST['orgBillGbn'];

	if ($billGbn == '1'){
		$cmsno = $_POST['cmsno'];
		$cmsCom = $_POST['cmsCom'];
	}else{
		$cmsno = '';
		$cmsCom = '';
	}

	if (!$orgFromDt) $orgFromDt = $fromDt;
	if (!$orgBillGbn) $orgBillGbn = $billGbn;


	$sql = 'SElECT	COUNT(*)
			FROM	cv_bill_info
			WHERE	org_no	= \''.$orgNo.'\'
			AND		bill_gbn= \''.$orgBillGbn.'\'
			AND		from_dt	= \''.$orgFromDt.'\'';

	$cnt = $conn->get_data($sql);


	if ($cnt > 0){
		$sql = 'UPDATE	cv_bill_info
				SET		from_dt		= \''.$fromDt.'\'
				,		bill_gbn	= \''.$billGbn.'\'
				,		bill_kind	= \''.$bill_kind.'\'
				,		cms_no		= \''.$cmsno.'\'
				,		cms_com		= \''.$cmsCom.'\'
				,		to_dt		= \''.$toDt.'\'
				,		del_flag	= \'N\'
				,		update_id	=\''.$_SESSION['userCode'].'\'
				,		update_dt	=NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		bill_gbn= \''.$orgBillGbn.'\'
				AND		from_dt	= \''.$orgFromDt.'\'';
	}else{
		$sql = 'INSERT INTO cv_bill_info (org_no,bill_gbn,from_dt,bill_kind,cms_no,cms_com,to_dt,insert_id,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$billGbn.'\'
				,\''.$fromDt.'\'
				,\''.$bill_kind.'\'
				,\''.$cmsno.'\'
				,\''.$cmsCom.'\'
				,\''.$toDt.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();

		 echo $conn->error_msg;
		 exit;
	}

	$conn->commit();

	include_once('../inc/_db_close.php');
?>