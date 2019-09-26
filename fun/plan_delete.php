<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code  = $_SESSION['userCenterCode'];//$_POST['code'];
	$jumin = $_POST['jumin'];
	$kind  = $_POST['kind'];
	$svcCd = $_POST['svcCd'];
	$date  = $_POST['date'];
	$time  = $_POST['time'];
	$seq   = $_POST['seq'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);

	$conn->begin();

	//로그삭제
	$sql = 'delete
			  from longcare_log
			 where org_no       = \''.$code.'\'
			   and lc_kind      = \''.$kind.'\'
			   and lc_svc_cd    = \''.$svcCd.'\'
			   and lc_c_cd      = \''.$jumin.'\'
			   and lc_dt        = \''.$date.'\'
			   and lc_plan_from = \''.$time.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 8;
		 exit;
	}

	//기존일정 삭제
	/*
	$sql = 'delete
			  from t01iljung
			 where t01_ccode = \''.$code.'\'
			   and t01_mkind = \''.$kind.'\'
			   and t01_jumin = \''.$jumin.'\'
			   and t01_sugup_date   = \''.$date.'\'
			   and t01_sugup_fmtime = \''.$time.'\'
			   and t01_sugup_seq    = \''.$seq.'\'';
	*/

	$sql = 'UPDATE	t01iljung
			SET		t01_request = \'AUTO\'
			,		t01_del_yn = \'Y\'
			WHERE	t01_ccode = \''.$code.'\'
			AND		t01_mkind = \''.$kind.'\'
			AND		t01_jumin = \''.$jumin.'\'
			AND		t01_sugup_date = \''.$date.'\'
			AND		t01_sugup_fmtime = \''.$time.'\'
			AND		t01_sugup_seq = \''.$seq.'\'';

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