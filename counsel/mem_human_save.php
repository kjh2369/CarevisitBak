<?
	$human_code = $_POST['human_code'];
	$human_ssn  = $ed->de($_POST['human_ssn']);
	$human_mode = $_POST['human_mode'];
	$human_type = 'M_HUMAN';



	/**************************************************

		교육이수 데이타 삭제

	**************************************************/
	$sql = 'delete
			  from counsel_edu
			 where org_no   = \''.$human_code.'\'
			   and edu_ssn  = \''.$human_ssn.'\'
			   and edu_type = \''.$human_type.'\'';

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}

	/**************************************************

		입사전기록 데이타 삭제

	**************************************************/
	$sql = 'delete
			  from counsel_record
			 where org_no   = \''.$human_code.'\'
			   and record_ssn  = \''.$human_ssn.'\'
			   and record_type = \''.$human_type.'\'';

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}

	/**************************************************

		자격 데이타 삭제

	**************************************************/
	$sql = 'delete
			  from counsel_license
			 where org_no       = \''.$human_code.'\'
			   and license_ssn  = \''.$human_ssn.'\'
			   and license_type = \''.$human_type.'\'';

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}


	/**************************************************

		상벌 데이타 삭제

	**************************************************/
	$sql = 'delete
			  from counsel_rnp
			 where org_no   = \''.$human_code.'\'
			   and rnp_ssn  = \''.$human_ssn.'\'
			   and rnp_type = \''.$human_type.'\'';

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}



	/**************************************************

		교육이수 데이타 저장

	**************************************************/
	$human_edu_cnt = sizeof($_POST['edu_human_gbn']);

	if ($human_edu_cnt > 0){
		#$sql = 'insert into counsel_edu (org_no,edu_type,edu_ssn,edu_seq,edu_gbn,edu_center,edu_nm,edu_time) values ';

		$sql = '';

		for($i=0; $i<$human_edu_cnt; $i++){
			if (!empty($_POST['edu_human_center'][$i])){
				#$sql .= ($i > 0 ? ',' : '');
				$sql .= (!empty($sql) > 0 ? ',' : '');
				$sql .= '(\''.$human_code.'\'
						 ,\''.$human_type.'\'
						 ,\''.$human_ssn.'\'
						 ,\''.$i.'\'
						 ,\''.$_POST['edu_human_gbn'][$i].'\'
						 ,\''.$_POST['edu_human_center'][$i].'\'
						 ,\''.$_POST['edu_human_name'][$i].'\'
						 ,\''.$_POST['edu_human_from_date'][$i].'\'
						 ,\''.$_POST['edu_human_to_date'][$i].'\'
						 ,\''.$_POST['edu_human_date'][$i].'\')';
			}
		}

		if (!empty($sql)){
			$sql = 'insert into counsel_edu (org_no,edu_type,edu_ssn,edu_seq,edu_gbn,edu_center,edu_nm,edu_from_dt, edu_to_dt, edu_time) values '.$sql;
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
		}
	}
	
	/**************************************************

		2012.08.30 김주완 추가
		입사면담기록 데이타 저장

	**************************************************/
	$human_rec_cnt = sizeof($_POST['rec_human_job_nm']);

	if ($human_rec_cnt > 0){
		
		$sql = '';

		for($i=0; $i<$human_rec_cnt; $i++){
			if (!empty($_POST['rec_human_job_nm'][$i])){
				#$sql .= ($i > 0 ? ',' : '');
				$sql .= (!empty($sql) > 0 ? ',' : '');
				$sql .= '(\''.$human_code.'\'
						 ,\''.$human_type.'\'
						 ,\''.$human_ssn.'\'
						 ,\''.$i.'\'
						 ,\''.$_POST['rec_human_fm_dt'][$i].'\'
						 ,\''.$_POST['rec_human_to_dt'][$i].'\'
						 ,\''.$_POST['rec_human_job_nm'][$i].'\'
						 ,\''.$_POST['rec_human_position'][$i].'\'
						 ,\''.$_POST['rec_human_task'][$i].'\'
						 ,\''.$_POST['rec_human_salary'][$i].'\')';
			}
		}

		if (!empty($sql)){
			$sql = 'insert into counsel_record (org_no,record_type,record_ssn,record_seq,record_fm_dt,record_to_dt,record_job_nm,record_position, record_task, record_salary) values '.$sql;
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
		}
	}


	/**************************************************

		자격 데이타 저장

	**************************************************/
	$human_lcs_cnt = sizeof($_POST['lcs_human_type']);

	if ($human_lcs_cnt > 0){
		#$sql = 'insert into counsel_license (org_no,license_type,license_ssn,license_seq,license_gbn,license_no,license_center,license_dt) values ';

		$sql = '';

		for($i=0; $i<$human_lcs_cnt; $i++){
			if (!empty($_POST['lcs_human_type'][$i])){
				#$sql .= ($i > 0 ? ',' : '');
				$sql .= (!empty($sql) > 0 ? ',' : '');
				$sql .= '(\''.$human_code.'\'
						 ,\''.$human_type.'\'
						 ,\''.$human_ssn.'\'
						 ,\''.$i.'\'
						 ,\''.$_POST['lcs_human_type'][$i].'\'
						 ,\''.$_POST['lcs_human_no'][$i].'\'
						 ,\''.$_POST['lcs_human_center'][$i].'\'
						 ,\''.$_POST['lcs_human_date'][$i].'\')';
			}
		}

		if (!empty($sql)){
			$sql = 'insert into counsel_license (org_no,license_type,license_ssn,license_seq,license_gbn,license_no,license_center,license_dt) values '.$sql;
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
		}
	}


	/**************************************************

		상벌 데이타 저장

	**************************************************/
	$human_rnp_cnt = sizeof($_POST['rnp_human_date']);

	if ($human_rnp_cnt > 0){
		#$sql = 'insert into counsel_rnp (org_no,rnp_type,rnp_ssn,rnp_seq,rnp_date,rnp_gbn,rnp_comment) values ';

		$sql = '';

		for($i=0; $i<$human_rnp_cnt; $i++){
			if (!empty($_POST['rnp_human_date'][$i])){
				#$sql .= ($i > 0 ? ',' : '');
				$sql .= (!empty($sql) > 0 ? ',' : '');
				$sql .= '(\''.$human_code.'\'
						 ,\''.$human_type.'\'
						 ,\''.$human_ssn.'\'
						 ,\''.$i.'\'
						 ,\''.$_POST['rnp_human_date'][$i].'\'
						 ,\''.$_POST['rnp_human_kind_'.($i + 1)].'\'
						 ,\''.$_POST['rnp_human_cont'][$i].'\')';
			}
		}

		if (!empty($sql)){
			$sql = 'insert into counsel_rnp (org_no,rnp_type,rnp_ssn,rnp_seq,rnp_date,rnp_gbn,rnp_comment) values '.$sql;
			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
		}
	}
?>