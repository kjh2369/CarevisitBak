<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$no		= $_POST['no'];
	$stat	= $_POST['stat'];

	parse_str($_POST['para'],$var);

	$jumin = $ed->de($var['jumin']);

	if ($stat != '1'){
		//실적완료처리
		$sql = 'UPDATE	t01iljung
				SET		t01_status_gbn	= \'1\'
				WHERE	t01_ccode		= \''.$orgNo.'\'
				AND		t01_mkind		= \''.$SR.'\'
				AND		t01_jumin		= \''.$jumin.'\'
				AND		t01_sugup_date	= \''.$var['date'].'\'
				AND		t01_sugup_fmtime= \''.$var['time'].'\'
				AND		t01_sugup_seq	= \''.$var['seq'].'\'';

		$query[SizeOf($query)] = $sql;
	}


	//내용 저장
	$sql = 'SELECT	COUNT(*)
			FROM	care_result
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		date	= \''.$var['date'].'\'
			AND		time	= \''.$var['time'].'\'
			AND		seq		= \''.$var['seq'].'\'
			AND		no		= \''.$no.'\'';

	$resultCnt = $conn->get_data($sql);

	if ($resultCnt > 0){
		$sql = 'UPDATE	care_result
				SET		content		= \''.AddSlashes($_POST['content']).'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		date	= \''.$var['date'].'\'
				AND		time	= \''.$var['time'].'\'
				AND		seq		= \''.$var['seq'].'\'
				AND		no		= \''.$no.'\'';
	}else{
		$sql = 'SELECT	IFNULL(MAX(no),0)+1
				FROM	care_result
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		jumin	= \''.$jumin.'\'
				AND		date	= \''.$var['date'].'\'
				AND		time	= \''.$var['time'].'\'
				AND		seq		= \''.$var['seq'].'\'';

		$no = $conn->get_data($sql);

		$sql = 'INSERT INTO care_result(
				 org_no
				,org_type
				,jumin
				,date
				,time
				,seq
				,no
				,content
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$SR.'\'
				,\''.$jumin.'\'
				,\''.$var['date'].'\'
				,\''.$var['time'].'\'
				,\''.$var['seq'].'\'
				,\''.$no.'\'
				,\''.AddSlashes($_POST['content']).'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW())';
	}

	$query[SizeOf($query)] = $sql;

	$conn->begin();

	foreach($query as $sql){
		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->close();
			 echo 'ERROR';
			 exit;
		}
	}

	$conn->commit();

	$sql = 'SELECT	MAX(no)
			FROM	care_result
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		date	= \''.$var['date'].'\'
			AND		time	= \''.$var['time'].'\'
			AND		seq		= \''.$var['seq'].'\'
			AND		del_flag= \'N\'';

	$newNo = $conn->get_data($sql);

	//마지막 작성내용외 삭제처리
	$sql = 'UPDATE	care_result
			SET		del_flag= \'Y\'
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		date	= \''.$var['date'].'\'
			AND		time	= \''.$var['time'].'\'
			AND		seq		= \''.$var['seq'].'\'
			AND		no		< \''.$newNo.'\'';

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->close();
		 echo 'ERROR';
		 exit;
	}

	$conn->commit();

	echo $newNo;

	include_once('../inc/_db_close.php');
?>