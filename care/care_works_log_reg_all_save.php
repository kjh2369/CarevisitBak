<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['svcCd'];
	$date	= $_POST['date'];
	$time	= $_POST['time'];
	$suga	= $_POST['suga'];
	$res	= $_POST['res'];
	$mem	= $ed->de($_POST['mem']);
	$request = $_POST['request'];


	if (!$SR || !$date || !$suga || !$res){
		include_once('../inc/_db_close.php');
		exit;
	}

	$tmpCd = '';

	/*
		$sql = 'SELECT	t01_jumin AS jumin, a.jumin AS log_jumin
				FROM	t01iljung
				LEFT	JOIN	care_works_log AS a
						ON		a.org_no		= t01_ccode
						AND		a.org_type		= t01_mkind
						AND		a.date			= t01_sugup_date
						AND		a.jumin			= t01_jumin
						AND		a.suga_cd		= t01_suga_code1
						AND		a.resource_cd	= t01_yoyangsa_id1
						AND		a.mem_cd		= t01_yoyangsa_id2
				WHERE	t01_ccode		 = \''.$orgNo.'\'
				AND		t01_mkind		 = \''.$SR.'\'
				AND		t01_sugup_date	 = \''.$date.'\'
				AND		t01_sugup_fmtime = \''.$time.'\'
				AND		t01_suga_code1	 = \''.$suga.'\'
				AND		t01_yoyangsa_id1 = \''.$res.'\'
				AND		t01_yoyangsa_id2 = \''.$mem.'\'
				AND		t01_del_yn		 = \'N\'';

		$conn->query($sql);
		$conn->fetch();

		$rowCnt = $conn->row_count();

		for($i=0; $i<$rowCnt; $i++){
			$row = $conn->select_row($i);

			if ($row['log_jumin']){
				$sql = 'UPDATE	care_works_log
						SET		contents	= \''.AddSlashes($_POST['cont']).'\'
						,		update_id	= \''.$_SESSION['userCode'].'\'
						,		update_dt	= NOW()
						WHERE	org_no		= \''.$orgNo.'\'
						AND		org_type	= \''.$SR.'\'
						AND		date		= \''.$date.'\'
						AND		jumin		= \''.$row['log_jumin'].'\'
						AND		suga_cd		= \''.$suga.'\'
						AND		resource_cd	= \''.$res.'\'
						AND		mem_cd		= \''.$mem.'\'';
			}else{
				$sql = 'INSERT INTO care_works_log (
						 org_no
						,org_type
						,date
						,jumin
						,suga_cd
						,resource_cd
						,mem_cd
						,contents
						,insert_id
						,insert_dt) VALUES (
						 \''.$orgNo.'\'
						,\''.$SR.'\'
						,\''.$date.'\'
						,\''.$row['jumin'].'\'
						,\''.$suga.'\'
						,\''.$res.'\'
						,\''.$mem.'\'
						,\''.AddSlashes($_POST['cont']).'\'
						,\''.$_SESSION['userCode'].'\'
						,NOW()
						)';
			}

			if (!StrPos($tmpCd.'/', $row['jumin'].'/')){
				$tmpCd .= $row['jumin'].'/';
				$query[] = $sql;
			}
		}

		$conn->row_free();
	 */
	$sql = 'SELECT	t01_jumin AS jumin
			FROM	t01iljung
			WHERE	t01_ccode		 = \''.$orgNo.'\'
			AND		t01_mkind		 = \''.$SR.'\'
			AND		t01_sugup_date	 = \''.$date.'\'
			AND		t01_sugup_fmtime = \''.$time.'\'
			AND		t01_suga_code1	 = \''.$suga.'\'
			AND		t01_yoyangsa_id1 = \''.$res.'\'
			AND		t01_yoyangsa_id2 = \''.$mem.'\'
			AND		t01_request		 = \''.$request.'\'
			AND		t01_del_yn		 = \'N\'
			';

	$conn->query($sql);
	$conn->fetch();

	$rowCnt = $conn->row_count();

	for($i=0; $i<$rowCnt; $i++){
		$row = $conn->select_row($i);
		$jumin[] = $row['jumin'];
	}

	$conn->row_free();

	for($i=0; $i<count($jumin); $i++){
		$sql = 'SELECT	jumin
				FROM	care_works_log
				WHERE	org_no		= \''.$orgNo.'\'
				AND		org_type	= \''.$SR.'\'
				AND		date		= \''.$date.'\'
				AND		suga_cd		= \''.$suga.'\'
				AND		resource_cd	= \''.$res.'\'
				AND		mem_cd		= \''.$mem.'\'
				AND		jumin		= \''.$jumin[$i].'\'
				';
		$log_jumin = $conn->get_data($sql);

		if ($log_jumin){
			$sql = 'UPDATE	care_works_log
					SET		contents	= \''.AddSlashes($_POST['cont']).'\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no		= \''.$orgNo.'\'
					AND		org_type	= \''.$SR.'\'
					AND		date		= \''.$date.'\'
					AND		jumin		= \''.$log_jumin.'\'
					AND		suga_cd		= \''.$suga.'\'
					AND		resource_cd	= \''.$res.'\'
					AND		mem_cd		= \''.$mem.'\'';
		}else{
			$sql = 'INSERT INTO care_works_log (
					 org_no
					,org_type
					,date
					,jumin
					,suga_cd
					,resource_cd
					,mem_cd
					,contents
					,insert_id
					,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$SR.'\'
					,\''.$date.'\'
					,\''.$jumin[$i].'\'
					,\''.$suga.'\'
					,\''.$res.'\'
					,\''.$mem.'\'
					,\''.AddSlashes($_POST['cont']).'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';
		}
		
		
		if (!StrPos($tmpCd.'/', $jumin[$i].'/')){
			$tmpCd .= $jumin[$i].'/';
			$query[] = $sql;
		}
		
	}	

	if (is_array($query)){
		$conn->begin();

		foreach($query as $sql){
			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->close();

				 if ($debug){
					 echo $conn->error_msg.chr(13).chr(10).$conn->error_query;
				 }else{
					 echo '데이타 처리 중 오류가 발생하였습니다. 잠시 후 다시 시도하여 주십시오.';
				 }
				 exit;
			}
		}

		$conn->commit();
	}

	include_once('../inc/_db_close.php');
?>