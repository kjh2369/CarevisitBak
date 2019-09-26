<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$jumin	= $_POST['jumin'];
	$sr		= $_POST['sr'];
	$normalSeq = $_POST['normalSeq'];

	if (!is_numeric($jumin)) $jumin = $ed->de($jumin);
	
	//삭제전 일정등록 내역을 확인한다.
	$sql = 'SELECT	COUNT(*)
			FROM	t01iljung
			WHERE	t01_ccode		= \''.$orgNo.'\'
			AND		t01_mkind		= \''.$sr.'\'
			AND		t01_jumin       = \''.$jumin.'\'
			AND		t01_del_yn		= \'N\'';

	$cnt = $conn->get_data($sql);

	if ($cnt > 0){
		$conn->close();
		echo 'iljung';
		exit;
	}

	if ($normalSeq){
		$sql = 'UPDATE	care_client_normal
				SET		del_flag	= \'Y\'
				WHERE	org_no		= \''.$orgNo.'\'
				AND		normal_sr	= \''.$sr.'\'
				AND		normal_seq	= \''.$normalSeq.'\'';
		$query[SizeOf($query)] = $sql;
	}else{
		$sql = 'SELECT	m03_key
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$orgNo.'\'
				AND		m03_mkind = \'6\'
				AND		m03_jumin = \''.$jumin.'\'';

		$key = $conn->get_data($sql);

		$sql = 'UPDATE	care_client_normal
				SET		link_IPIN	= \'\'
				WHERE	org_no		= \''.$orgNo.'\'
				AND		normal_sr	= \''.$sr.'\'
				AND		link_IPIN	= \''.$key.'\'';
		$query[SizeOf($query)] = $sql;

		$sql = 'DELETE
				FROM	m03sugupja
				WHERE	m03_ccode = \''.$orgNo.'\'
				AND		m03_mkind = \'6\'
				AND		m03_jumin = \''.$jumin.'\'';

		$query[SizeOf($query)] = $sql;

		$sql = 'DELETE
				FROM	client_his_svc
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		svc_cd	= \''.$sr.'\'';

		$query[SizeOf($query)] = $sql;

		$sql = 'DELETE
				FROM	client_his_care
				WHERE	org_no	= \''.$orgNo.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		svc_cd	= \''.$sr.'\'';

		$query[SizeOf($query)] = $sql;

		$sql = 'DELETE
				FROM	mst_jumin
				WHERE	org_no	= \''.$orgNo.'\'
				AND		gbn		= \'1\'
				AND		code	= \''.$jumin.'\'';

		$query[SizeOf($query)] = $sql;
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