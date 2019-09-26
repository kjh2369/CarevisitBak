<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code	= $_SESSION['userCenterCode'];
	$jumin	= $ed->de($_POST['jumin']);
	$seq	= $_POST['seq'];

	$annuityYn	= $_POST['a'];
	$healthYn	= $_POST['h'];
	$employYn	= $_POST['e'];
	$sanjeYn	= $_POST['s'];
	$PAYEYn		= $_POST['p'];

	$fromDt = $_POST['from'];
	$toDt	= $_POST['to'];
	$reReg	= $_POST['reReg'];

	if ($reReg == 'Y'){
		$sql = 'SELECT	COUNT(*)
				FROM	mem_insu
				WHERE	org_no	 = \''.$code.'\'
				AND		jumin	 = \''.$jumin.'\'
				AND		CASE WHEN from_dt >= \''.$fromDt.'\' AND from_dt <= \''.$toDt.'\' THEN 1 ELSE 0 END +
						CASE WHEN to_dt >= \''.$fromDt.'\' AND to_dt <= \''.$toDt.'\' THEN 1 ELSE 0 END > 1';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			echo '적용일 이전의 등록일이 존재합니다.';
			exit;
		}
	}

	/*
	$sql = 'SELECT	COUNT(*)
			FROM	mem_insu
			WHERE	org_no	 = \''.$code.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		to_dt	 > \''.$toDt.'\'
			AND		seq		!= \''.$seq.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		echo '종료일 이전의 등록일이 존재합니다.';
		exit;
	}

	$sql = 'SELECT	COUNT(*)
			FROM	mem_insu
			WHERE	org_no	 = \''.$code.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		from_dt	<= \''.$fromDt.'\'
			AND		to_dt	>= \''.$fromDt.'\'
			AND		seq		!= \''.$seq.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		echo '적용일 이전의 등록일이 존재합니다.';
		exit;
	}

	$sql = 'SELECT	COUNT(*)
			FROM	mem_insu
			WHERE	org_no	 = \''.$code.'\'
			AND		jumin	 = \''.$jumin.'\'
			AND		from_dt	<= \''.$toDt.'\'
			AND		to_dt	>= \''.$toDt.'\'
			AND		seq		!= \''.$seq.'\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		echo '종료일 이전의 등록일이 존재합니다.';
		exit;
	}
	*/

	$conn->begin();

	if ($reReg == 'Y'){
		$seq ++;
	}else{
		$sql = 'SELECT	COUNT(*)
				FROM	mem_insu
				WHERE	org_no	= \''.$code.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		seq		= \''.$seq.'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$sql = 'UPDATE	mem_insu
					SET		from_dt		= \''.$fromDt.'\'
					,		to_dt		= \''.$toDt.'\'
					,		annuity_yn	= \''.$annuityYn.'\'
					,		health_yn	= \''.$healthYn.'\'
					,		employ_yn	= \''.$employYn.'\'
					,		sanje_yn	= \''.$sanjeYn.'\'
					,		paye_yn		= \''.$PAYEYn.'\'
					WHERE	org_no	= \''.$code.'\'
					AND		jumin	= \''.$jumin.'\'
					AND		seq		= \''.$seq.'\'';

			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();
				 echo '9';
				 exit;
			}

			$conn->commit();
			$conn->close();
			exit;
		}
	}

	if ($seq < 1) $seq = 1;

	$sql = 'INSERT INTO mem_insu (
			 org_no
			,jumin
			,seq
			,from_dt
			,to_dt
			,annuity_yn
			,health_yn
			,employ_yn
			,sanje_yn
			,paye_yn) VALUES (
			 \''.$code.'\'
			,\''.$jumin.'\'
			,\''.$seq.'\'
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

	include_once('../inc/_db_close.php');
?>