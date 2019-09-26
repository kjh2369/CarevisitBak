<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$orgNo = $_SESSION['userCenterCode'];
	$jumin = $ed->de($_POST['jumin']);
	$date = $_POST['date'];
	$time = $_POST['time'];
	$userCd = $_SESSION['userCode'];

	if ($date && $time){
		$yymm = SubStr($date,0,6);
	}else{
		$yymm = $_POST['yymm'];
	}

	$sql = 'SELECT	IFNULL(MAX(seq),0) + 1
			FROM	sw_log
			WHERE	org_no	= \''.$orgNo.'\'
			AND		jumin	= \''.$jumin.'\'
			AND		yymm	= \''.$yymm.'\'';

	$seq = $conn->get_data($sql);

	$sql = 'SELECT	*
			FROM	sw_log
			WHERE	org_no = \''.$orgNo.'\'
			AND		jumin  = \''.$jumin.'\'';

	if ($date && $time){
		$sql .= '
				AND	UNIX_TIMESTAMP(CONCAT(SUBSTR(date,1,4),\'-\',SUBSTR(date,5,2),\'-\',SUBSTR(date,7),\' \',SUBSTR(time,1,2),\':\',SUBSTR(time,3,2),\':00\')) <= \''.StrToTime($date.' '.$time).'\'';
	}else{
		$sql .= '
				AND	yymm = \''.$yymm.'\'';
	}

	$sql .= '
			ORDER	BY date DESC, time DESC
			LIMIT	1';

	$row = $conn->get_array($sql);

	$sql = 'INSERT INTO sw_log (
			 org_no
			,jumin
			,yymm
			,seq
			,reg_jumin
			,reg_name
			,date
			,time
			,body_stat
			,body_stat_note
			,disease
			,medication
			,diagnosis
			,disabled
			,disabled_lvl
			,eyesight
			,hearing
			,disease_note
			,memory
			,express
			,memory_note
			,feel_stat
			,comm_other
			,comm_note
			,meal_type
			,water_type
			,intake_type
			,nutrition_note
			,env_note
			,total_note
			,target_note
			,cont_note
			,provide_note
			,check_note
			,action_note
			,notvisit_reason
			,comment
			,insert_id
			,insert_dt) VALUES (
			 \''.$orgNo.'\'
			,\''.$jumin.'\'
			,\''.$yymm.'\'
			,\''.$seq.'\'
			,\''.$row['reg_jumin'].'\'
			,\''.$row['reg_name'].'\'
			,\''.$date.'\'
			,\''.$time.'\'
			,\''.$row['body_stat'].'\'
			,\''.$row['body_stat_note'].'\'
			,\''.$row['disease'].'\'
			,\''.$row['medication'].'\'
			,\''.$row['diagnosis'].'\'
			,\''.$row['disabled'].'\'
			,\''.$row['disabled_lvl'].'\'
			,\''.$row['eyesight'].'\'
			,\''.$row['hearing'].'\'
			,\''.$row['disease_note'].'\'
			,\''.$row['memory'].'\'
			,\''.$row['express'].'\'
			,\''.$row['memory_note'].'\'
			,\''.$row['feel_stat'].'\'
			,\''.$row['comm_other'].'\'
			,\''.$row['comm_note'].'\'
			,\''.$row['meal_type'].'\'
			,\''.$row['water_type'].'\'
			,\''.$row['intake_type'].'\'
			,\''.$row['nutrition_note'].'\'
			,\''.$row['env_note'].'\'
			,\''.$row['total_note'].'\'
			,\''.$row['target_note'].'\'
			,\''.$row['cont_note'].'\'
			,\''.$row['provide_note'].'\'
			,\''.$row['check_note'].'\'
			,\''.$row['action_note'].'\'
			,\''.$row['notvisit_reason'].'\'
			,\''.$row['comment'].'\'
			,\''.$userCd.'\'
			,NOW()
			)';

	Unset($row);

	$conn->begin();

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 echo $conn->error_msg;
		 exit;
	}

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>