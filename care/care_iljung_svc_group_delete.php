<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once("../inc/_page_list.php");
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$grpCd	= $_POST['grpCd'];

	#$sql = 'UPDATE	care_svc_iljung
	#		SET		del_flag	= \'Y\'
	#		,		update_id	= \''.$_SESSION['userCode'].'\'
	#		,		update_dt	= NOW()
	#		WHERE	org_no		= \''.$orgNo.'\'
	#		AND		org_type	= \''.$SR.'\'
	#		AND		grp_cd		= \''.$grpCd.'\'';
	#$query[] = $sql;

	//묶음 삭제시 일정삭제는 맊음(임시)
	#$sql = 'DELETE
	#		FROM	t01iljung
	#		WHERE	t01_ccode	= \''.$orgNo.'\'
	#		AND		t01_mkind	= \''.$SR.'\'
	#		AND		t01_request	= \''.$grpCd.'\'';
	#$query[] = $sql;
	
	$sql = 'UPDATE t01iljung
			SET	   t01_del_yn	= \'Y\'
			WHERE  t01_ccode	= \''.$orgNo.'\'
			AND	   t01_mkind	= \''.$SR.'\'
			AND	   t01_request	= \''.$grpCd.'\'';

	$sql = 'SELECT	tg_info
			FROM	care_svc_iljung
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		grp_cd	= \''.$grpCd.'\'';

	$tmp = Explode('?',$conn->get_data($sql));
	foreach($tmp as $t)if ($t) $tg[] = $t;

	$sql = 'UPDATE	care_svc_iljung
			SET		del_flag	= \'Y\'
			,		update_id	= \''.$_SESSION['userCode'].'\'
			,		update_dt	= NOW()
			WHERE	org_no		= \''.$orgNo.'\'
			AND		org_type	= \''.$SR.'\'
			AND		grp_cd		= \''.$grpCd.'\'';

	$query[] = $sql;

	foreach($tg as $row){
		parse_str($row,$val);

		$jumin = $ed->de($val['jumin']);

		if (!$jumin){
			$tmp = 'SELECT	m03_jumin
					FROM	m03sugupja
					WHERE	m03_ccode	= \''.$orgNo.'\'
					AND		m03_mkind	= \'6\'
					AND		m03_key		= \''.$val['key'].'\'';

			$jumin = $conn->get_data($tmp);
		}

		#$sql = 'DELETE
		#		FROM	t01iljung
		#		WHERE	t01_ccode		= \''.$orgNo.'\'
		#		AND		t01_mkind		= \''.$SR.'\'
		#		AND		t01_jumin		= \''.$jumin.'\'
		#		AND		t01_request		= \''.$grpCd.'\'';

		$sql = 'UPDATE	t01iljung
				SET		t01_del_yn = \'Y\'
				WHERE	t01_ccode	= \''.$orgNo.'\'
				AND		t01_mkind	= \''.$SR.'\'
				AND		t01_jumin	= \''.$jumin.'\'
				AND		t01_sugup_date = \''.$val['date'].'\'
				AND		t01_request	= \''.$grpCd.'\'';
		$query[] = $sql;
	}

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 9;
			 exit;
		}
	}

	$conn->commit();
	echo 1;

	include_once('../inc/_db_close.php');
?>