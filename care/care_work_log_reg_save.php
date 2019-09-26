<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$sugaCd	= $_POST['sugaCd'];
	$jumin	= $ed->de($_POST['jumin']);
	$target	= $ed->de($_POST['target']);
	$date	= str_replace('.','',$_POST['date']);
	$from	= str_replace(':','',$_POST['from']);
	$to		= str_replace(':','',$_POST['to']);
	$data	= Explode('?',$_POST['data']);

	if (!is_array($data)){
		$conn->close();
		echo 9;
		exit;
	}

	$sql = 'SELECT	sub_key
			FROM	care_work_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		suga_cd	= \''.$sugaCd.'\'
			AND		mem_cd	= \''.$jumin.'\'
			AND		date	= \''.$date.'\'
			AND		jumin	= \''.$target.'\'';

	$key = $conn->get_data($sql);

	if (!$key){
		$sql = 'SELECT	IFNULL(MAX(sub_key),0)+1
				FROM	care_work_log
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'';

		$key = $conn->get_data($sql);

		$sql = 'INSERT INTO care_work_log (
				 org_no
				,org_type
				,suga_cd
				,mem_cd
				,date
				,jumin
				,sub_key
				,from_time
				,to_time
				,insert_id
				,insert_dt) VALUES (
				 \''.$orgNo.'\'
				,\''.$SR.'\'
				,\''.$sugaCd.'\'
				,\''.$jumin.'\'
				,\''.$date.'\'
				,\''.$target.'\'
				,\''.$key.'\'
				,\''.$from.'\'
				,\''.$to.'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW()
				)';
	}else{
		$sql = 'UPDATE	care_work_log
				SET		from_time	= \''.$from.'\'
				,		to_time		= \''.$to.'\'
				,		update_id	= \''.$_SESSION['userCode'].'\'
				,		update_dt	= NOW()
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		suga_cd	= \''.$sugaCd.'\'
				AND		mem_cd	= \''.$jumin.'\'
				AND		date	= \''.$date.'\'
				AND		jumin	= \''.$target.'\'';
	}

	$query[] = $sql;

	foreach($data as $idx => $row){
		parse_str($row,$col);

		$sql = 'SELECT	COUNT(*)
				FROM	care_work_log_sub
				WHERE	org_no	= \''.$orgNo.'\'
				AND		org_type= \''.$SR.'\'
				AND		sub_key	= \''.$key.'\'
				AND		seq		= \''.$col['seq'].'\'';

		$cnt = $conn->get_data($sql);

		if ($cnt > 0){
			$sql = 'UPDATE	care_work_log_sub
					SET		contents= \''.AddSlashes($col['contents']).'\'
					WHERE	org_no	= \''.$orgNo.'\'
					AND		org_type= \''.$SR.'\'
					AND		sub_key	= \''.$key.'\'
					AND		seq		= \''.$col['seq'].'\'';
		}else{
			$sql = 'INSERT INTO care_work_log_sub (
					 org_no
					,org_type
					,sub_key
					,seq
					,contents) VALUES (
					 \''.$orgNo.'\'
					,\''.$SR.'\'
					,\''.$key.'\'
					,\''.$col['seq'].'\'
					,\''.AddSlashes($col['contents']).'\'
					)';
		}

		$query[] = $sql;
	}

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
	echo $key;

	include_once('../inc/_db_close.php');
?>