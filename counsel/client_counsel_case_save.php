<?
	$case_code = $_POST['code'];
	$case_yymm = $_POST['yymm'];
	$case_seq  = ($_POST['case_seq'] != '' ? $_POST['case_seq'] : $_POST['seq']);

	if (!empty($_POST['case_run_cd'])){
		$reg_id = $_SESSION['userCode'];
		$reg_dt = date('Y-m-d', mktime());

		if ($case_seq == 0){
			$case_yymm = substr(str_replace('-', '', $_POST['case_dt']), 0, 6);
			//$case_yymm = date('Ym', mktime());

			$sql = 'select ifnull(max(case_seq), 0) + 1
					  from counsel_client_case
					 where org_no    = \''.$case_code.'\'
					   and case_yymm = \''.$case_yymm.'\'';

			$case_seq = $conn->get_data($sql);

			$sql = 'insert into counsel_client_case (
					 org_no
					,case_yymm
					,case_seq
					,case_c_cd
					,insert_dt
					,insert_id) values (
					 \''.$case_code.'\'
					,\''.$case_yymm.'\'
					,\''.$case_seq.'\'
					,\''.$ed->de($_POST['case_ssn']).'\'
					,\''.$reg_dt.'\'
					,\''.$reg_id.'\')';

			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
			$add_sql = '';
		}else{
			$add_sql = ', update_dt = \''.$reg_dt.'\'
						, update_id = \''.$reg_id.'\'';
		}

		$case_run_cd = $ed->de($_POST['case_run_cd']);
		$case_run_nm = $conn->member_name($case_code, $case_run_cd);

		$case_m_cd     = $ed->de($_POST['case_m_cd']);

		if (!empty($case_m_cd)){
			$case_m_nm     = $conn->member_name($case_code, $case_m_cd);
			$case_m_age    = $myF->issToAge($case_m_cd);
			$case_m_gender = $myF->issToGender($case_m_cd);
		}

		$care_svc_kind = '';

		if (is_array($_POST['case_use_svc'])){
			foreach($_POST['case_use_svc'] as $care_i => $care_svc_cd){
				$care_svc_kind .= $care_svc_cd.'/';
			}
		}

		$sql = 'update counsel_client_case
				   set case_dt           = \''.$_POST['case_dt'].'\'
				,      case_run_cd       = \''.$case_run_cd.'\'
				,      case_run_nm       = \''.$case_run_nm.'\'
				,      case_present_nm   = \''.$_POST['case_present_nm'].'\'
				,      case_svc_kind     = \''.$care_svc_kind.'\'
				,      case_use_from     = \''.$_POST['case_use_from'].'\'
				,      case_use_to       = \''.$_POST['case_use_to'].'\'
				,      case_m_cd         = \''.$case_m_cd.'\'
				,      case_m_nm         = \''.$case_m_nm.'\'
				,      case_m_age        = \''.$case_m_age.'\'
				,      case_m_gender     = \''.$case_m_gender.'\'
				,      case_m_career     = \''.$_POST['case_m_career'].'\'
				,      case_economy      = \''.addslashes($_POST['case_economy']).'\'
				,      case_family       = \''.addslashes($_POST['case_family']).'\'
				,      case_soul         = \''.addslashes($_POST['case_soul']).'\'
				,      case_body         = \''.addslashes($_POST['case_body']).'\'
				,      case_other        = \''.addslashes($_POST['case_other']).'\'
				,      case_main_quest   = \''.addslashes($_POST['case_main_quest']).'\'
				,      case_present_talk = \''.addslashes($_POST['case_present_talk']).'\'
				,      case_later_plan   = \''.addslashes($_POST['case_later_plan']).'\'
				,      case_proc_period  = \''.addslashes($_POST['case_proc_period']).'\'
				,      case_after_plan   = \''.addslashes($_POST['case_after_plan']).'\''.$add_sql.'
				 where org_no    = \''.$case_code.'\'
				   and case_yymm = \''.$case_yymm.'\'
				   and case_seq  = \''.$case_seq.'\'';

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}

		if (Is_Array($_FILES)){
			$counselId	= '0040';	//아이디
			$sql = 'SELECT	IFNULL(MAX(no),0)+1
					FROM	counsel_file
					WHERE	org_no		= \''.$case_code.'\'
					AND		counsel_id	= \''.$counselId.'\'
					AND		yymm		= \''.$case_yymm.'\'
					AND		seq			= \''.$case_seq.'\'';

			$counselNo	= $conn->get_data($sql);
			$counselIdx	= 0;

			foreach($_FILES as $f => $file){
				$pic	= $file;
				$upload	= false;

				if ($pic['tmp_name'] != ''){
					$tmpInfo	= pathinfo($pic['name']);
					$expNm		= strtolower($tmpInfo['extension']);
					$attchNm	= $counselNm.'_'.$counselIdx;

					if ($expNm == 'jpg' ||
						$expNm == 'png' ||
						$expNm == 'gif' ||
						$expNm == 'bmp'){
						$picNm = $case_code.$counselId.$case_yymm.$case_seq.$counselNo.'.'.$expNm;
					}else{
						$picNm = '';
					}

					if (!empty($picNm)){
						if (move_uploaded_file($pic['tmp_name'], '../file/0040/'.$picNm)){
							// 업로드 성공
							$upload = true;
						}
					}
				}

				if ($upload){
					$sql = 'INSERT INTO counsel_file (
							 org_no
							,counsel_id
							,yymm
							,seq
							,no
							,subject
							,file_name
							,file_size
							,file_type
							,file_attch) VALUES (
							 \''.$case_code.'\'
							,\''.$counselId.'\'
							,\''.$case_yymm.'\'
							,\''.$case_seq.'\'
							,\''.$counselNo.'\'
							,\''.$_POST[Str_Replace('attachFile_','attachStr_',$f)].'\'
							,\''.$pic['name'].'\'
							,\''.$pic['size'].'\'
							,\''.$pic['type'].'\'
							,\''.$picNm.'\')';

					$conn->execute($sql);

					$counselNo ++;
				}
			}
		}
	}
?>