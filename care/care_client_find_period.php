<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if (!$IsExcel){
		$orgNo	= $_SESSION['userCenterCode'];
		$SR		= $_POST['SR'];
		$jumin	= $ed->de($_POST['jumin']);
		$today	= Date('Y-m-d');
	}else{
		$jumin = $code;
	}

	$sql = 'SELECT	from_dt
			,		to_dt
			FROM	client_his_svc
			WHERE	org_no	= \''.$orgNo.'\'
			AND		svc_cd	= \''.$SR.'\'
			AND		jumin	= \''.$jumin.'\'
			ORDER	BY from_dt, to_dt';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);

		if ($today > $row['to_dt']){
			$tmpFrom = $row['from_dt'];
			$tmpTo = $row['to_dt'];
		}

		if ($today >= $row['from_dt'] && $today <= $row['to_dt']){
			$fromDt = $row['from_dt'];
			$toDt = $row['to_dt'];
			break;
		}
	}

	$conn->row_free();

	if ($IsExcel){
		if (!$fromDt && !$toDt){
			$fromDt = $tmpFrom;
			$toDt = $tmpTo;
		}
	}else{
		if ($fromDt && $toDt){
			echo 'from='.$myF->dateStyle($fromDt,'.').'&to='.$myF->dateStyle($toDt,'.').'&gbn=1';
		}else{
			echo 'from='.$myF->dateStyle($tmpFrom,'.').'&to='.$myF->dateStyle($tmpTo,'.').'&gbn=2';
		}

		include_once('../inc/_db_close.php');
	}
?>