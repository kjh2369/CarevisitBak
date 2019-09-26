<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo	= $_SESSION['userCenterCode'];
	$SR		= $_POST['SR'];
	$sugaCd	= $_POST['sugaCd'];
	$data	= Explode('?',$_POST['data']);

	if (!is_array($data)){
		$conn->close();
		echo 9;
		exit;
	}

	$sql = 'SELECT	IFNULL(MAX(seq),0) + 1
			FROM	care_work_log_item
			WHERE	org_no	= \''.$orgNo.'\'
			AND		org_type= \''.$SR.'\'
			AND		suga_cd	= \''.$sugaCd.'\'';

	$newSeq = $conn->get_data($sql);

	foreach($data as $idx => $row){
		parse_str($row, $col);

		if ($col['seq'] > 0){
			$sql = 'UPDATE	care_work_log_item
					SET		name		= \''.$col['name'].'\'
					,		order_no	= \''.$col['orderNo'].'\'
					,		from_dt		= \''.$col['fromDt'].'\'
					,		to_dt		= \''.$col['toDt'].'\'
					,		prt_yn		= \''.$col['prtYn'].'\'
					,		update_id	= \''.$_SESSION['userCode'].'\'
					,		update_dt	= NOW()
					WHERE	org_no		= \''.$orgNo.'\'
					AND		org_type	= \''.$SR.'\'
					AND		suga_cd		= \''.$sugaCd.'\'
					AND		seq			= \''.$col['seq'].'\'';

			$query[] = $sql;
		}else{
			$sql = 'INSERT INTO care_work_log_item (
					 org_no
					,org_type
					,suga_cd
					,seq
					,name
					,order_no
					,prt_yn
					,from_dt
					,to_dt
					,insert_id
					,insert_dt) VALUES (
					 \''.$orgNo.'\'
					,\''.$SR.'\'
					,\''.$sugaCd.'\'
					,\''.$newSeq.'\'
					,\''.$col['name'].'\'
					,\''.$col['orderNo'].'\'
					,\''.$col['prtYn'].'\'
					,\''.$col['fromDt'].'\'
					,\''.$col['toDt'].'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW()
					)';

			$query[] = $sql;
			$newSeq ++;
		}
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