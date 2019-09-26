<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');

	$orgNo	= $_POST['code'];	//기관
	$svcCd	= $_POST['svcCd'];	//서비스
	$seq	= $_POST['seq'];	//순번
	$fromDt	= $_POST['fromDt'];	//적용일
	$toDt	= $_POST['toDt'];	//종료일
	$acctYn	= $_POST['acctYn'];	//과금여부

	//적용기간 중복여부
	$sql = 'SELECT	from_dt
			,		to_dt
			FROM	sub_svc
			WHERE	org_no	 = \''.$orgNo.'\'
			AND		svc_cd	 = \''.$svcCd.'\'
			AND		seq		!= \''.$seq.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();
	$IsDuplicate = false;

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if (($fromDt >= $row['from_dt'] && $fromDt <= $row['to_dt']) ||
			($toDt >= $row['from_dt'] && $toDt <= $row['to_dt']) ||
			($row['from_dt'] >= $fromDt && $row['from_dt'] <= $toDt) ||
			($row['to_dt'] >= $fromDt && $row['to_dt'] <= $toDt)){
			$IsDuplicate = true;
			break;
		}
	}

	$conn->row_free();

	if ($IsDuplicate){
		$conn->close();
		echo 7;
		exit;
	}

	//수정여부
	$sql = 'SELECT	COUNT(*)
			FROM	sub_svc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		svc_cd	= \''.$svcCd.'\'
			AND		seq		= \''.$seq.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		//수정
		$sql = 'UPDATE	sub_svc
				SET		acct_yn		= \''.$acctYn.'\'
				,		from_dt		= \''.$fromDt.'\'
				,		to_dt		= \''.$toDt.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no		= \''.$orgNo.'\'
				AND		svc_cd		= \''.$svcCd.'\'
				AND		seq			= \''.$seq.'\'';
	}else{
		//신규
		$sql = 'SELECT	IFNULL(MAX(seq),0)+1
				FROM	sub_svc
				WHERE	org_no		= \''.$orgNo.'\'
				AND		svc_cd		= \''.$svcCd.'\'';

		$seq = $conn->get_data($sql);

		$sql = 'INSERT INTO sub_svc (
				 org_no
				,svc_cd
				,seq
				,acct_yn
				,from_dt
				,to_dt
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$svcCd.'\'
				,\''.$seq.'\'
				,\''.$acctYn.'\'
				,\''.$fromDt.'\'
				,\''.$toDt.'\'
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