<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);

	$annuityYn	= $_POST['a'];
	$healthYn	= $_POST['h'];
	$employYn	= $_POST['e'];
	$sanjeYn	= $_POST['s'];
	$PAYEYn		= $_POST['p'];

	$fromDt = $_POST['from'];
	$toDt	= $_POST['to'];
	$reReg	= $_POST['reReg'];
	
	$sql = 'SELECT	COUNT(*)
			FROM	ltcf_insu_hist
			WHERE	org_no	 = \''.$code.'\'
			AND		ipin	 = \''.$jumin.'\'
			AND		from_dt != \''.$orgDt.'\'
			AND		del_flag = \'N\'
			AND		CASE WHEN from_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1
						 WHEN to_dt BETWEEN \''.$fromDt.'\' AND \''.$toDt.'\' THEN 1
						 WHEN \''.$fromDt.'\' BETWEEN from_dt AND to_dt THEN 1
						 WHEN \''.$toDt.'\' BETWEEN from_dt AND to_dt THEN 1 ELSE 0 END = 1';
	
	
	if ($conn->get_data($sql) > 0){
		$conn->close();
		echo '입력하신 적용기간이 기존의 적용기간과 중복됩니다.\n확인하여 주십시오.';
		exit;
	}

	$conn->begin();
	
	if (!$orgDt){
		$sql = 'SELECT	COUNT(*)
				FROM	ltcf_insu_hist
				WHERE	org_no	= \''.$code.'\'
				AND		ipin	= \''.$jumin.'\'
				AND     from_dt = \''.$fromDt.'\'';

		if ($conn->get_data($sql) > 0){
			$orgDt = $fromDt;
		}
	}

	if ($orgDt){
		$sql = 'UPDATE	ltcf_insu_hist
				SET		from_dt		= \''.$fromDt.'\'
				,		to_dt		= \''.$toDt.'\'
				,		nps_flag	= \''.$annuityYn.'\'
				,		nhic_flag	= \''.$healthYn.'\'
				,		ei_flag		= \''.$employYn.'\'
				,		lai_flag	= \''.$sanjeYn.'\'
				,		income_tax_off_flag		= \''.$PAYEYn.'\'
				WHERE	org_no	= \''.$code.'\'
				AND		ipin	= \''.$jumin.'\'
				AND     from_dt = \''.$fromDt.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '9';
			 exit;
		}

		$conn->commit();
		$conn->close();
		exit;
	}else {
		$sql = 'INSERT INTO ltcf_insu_hist (
				 org_no
				,ipin
				,from_dt
				,to_dt
				,nps_flag
				,nhic_flag
				,ei_flag
				,lai_flag
				,income_tax_off_flag) VALUES (
				 \''.$code.'\'
				,\''.$jumin.'\'
				,\''.$fromDt.'\'
				,\''.$toDt.'\'
				,\''.$annuityYn.'\'
				,\''.$healthYn.'\'
				,\''.$employYn.'\'
				,\''.$sanjeYn.'\'
				,\''.$PAYEYn.'\'
				)';
		
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo '9';
			 exit;
		}

		$conn->commit();
	}



	

	include_once('../inc/_db_close.php');
?>