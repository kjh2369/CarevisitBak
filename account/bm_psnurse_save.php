<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['orgNo'];
	$seq	= $_POST['seq'];
	$fromDt	= str_replace('-','',$_POST['fromDt']);
	$toDt	= str_replace('-','',$_POST['toDt']);
	$cnt	= str_replace(',','',$_POST['cnt']);


	if ($cnt == 'NULL'){
		$sql = 'DELETE
				FROM	ie_bm_psnurse
				WHERE	org_no	= \''.$orgNo.'\'
				AND		seq		= \''.$seq.'\'';

		$conn->begin();

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}

		$conn->commit();
		$conn->close();
		echo 1;
		exit;
	}


	$sql = 'SELECT	COUNT(*)
			FROM	ie_bm_psnurse
			WHERE	org_no = \''.$orgNo.'\'
			AND		CASE WHEN from_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1 ELSE 0 END +
					CASE WHEN to_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1 ELSE 0 END > 0';
	$chkNo = $conn->get_data($sql);

	if ($chkNo > 0){
		$conn->close();
		echo 7;
		exit;
	}


	$sql = 'SELECT	IFNULL(MAX(seq),0)+1
			FROM	ie_bm_psnurse
			WHERE	org_no = \''.$orgNo.'\'';
	$seq = $conn->get_data($sql);


	$sql = 'INSERT INTO ie_bm_psnurse (
			 org_no
			,seq
			,from_dt
			,to_dt
			,psn_cnt
			,insert_id
			,insert_dt) VALUES (
			 \''.$orgNo.'\'
			,\''.$seq.'\'
			,\''.$fromDt.'\'
			,\''.$toDt.'\'
			,\''.$cnt.'\'
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