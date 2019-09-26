<?
	$stress_code = $_POST['code'];
	$stress_yymm = $_POST['yymm'];
	$stress_seq  = ($_POST['stress_seq'] != '' ? $_POST['stress_seq'] : $_POST['seq']);

	if (!Is_Numeric($stress_seq)) $stress_seq = 0;

	if (!empty($_POST['stress_m_cd'])){
		$reg_id = $_SESSION['userCode'];
		$reg_dt = date('Y-m-d', mktime());

		if ($stress_seq == 0){
			$stress_yymm = substr(str_replace('-', '', $_POST['stress_dt']), 0, 6);
			//$stress_yymm = date('Ym', mktime());

			$sql = 'select ifnull(max(stress_seq), 0) + 1
					  from counsel_client_stress
					 where org_no      = \''.$stress_code.'\'
					   and stress_yymm = \''.$stress_yymm.'\'';

			$stress_seq = $conn->get_data($sql);

			$sql = 'insert into counsel_client_stress (
					 org_no
					,stress_yymm
					,stress_seq
					,stress_c_cd
					,insert_dt
					,insert_id) values (
					 \''.$stress_code.'\'
					,\''.$stress_yymm.'\'
					,\''.$stress_seq.'\'
					,\''.$ed->de($_POST['stress_ssn']).'\'
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

		$stress_m_cd = $ed->de($_POST['stress_m_cd']);
		$stress_m_nm = $conn->member_name($stress_code, $stress_m_cd);

		$sql = 'update counsel_client_stress
				   set stress_m_cd              = \''.$stress_m_cd.'\'
				,      stress_m_nm              = \''.$stress_m_nm.'\'
				,      stress_dt                = \''.$_POST['stress_dt'].'\'
				,      stress_rct_kind          = \''.$_POST['stress_kind'].'\'
				,      stress_rct_kind_family   = \''.$_POST['stress_kind_family'].'\'
				,      stress_rct_kind_other    = \''.$_POST['stress_kind_other'].'\'
				,      stress_rct_path          = \''.$_POST['stress_path'].'\'
				,      stress_rct_path_paper_yn = \''.$_POST['stress_path_paper_yn'].'\'
				,      stress_rct_path_other    = \''.$_POST['stress_path_other'].'\'
				,      stress_cont_kind         = \''.$_POST['stress_cont_kind'].'\'
				,      stress_cont_text         = \''.addslashes($_POST['stress_cont_text']).'\'
				,      stress_proc_kind         = \''.$_POST['stress_proc_kind'].'\'
				,      stress_proc_text         = \''.addslashes($_POST['stress_proc_text']).'\'
				,      stress_rst_obj           = \''.addslashes($_POST['stress_rst_obj']).'\'
				,      stress_rst_sub           = \''.addslashes($_POST['stress_rst_sub']).'\'
				,      stress_rst_app           = \''.addslashes($_POST['stress_rst_app']).'\'
				,      stress_rst_otr           = \''.addslashes($_POST['stress_rst_otr']).'\'
				,      stress_after_plan        = \''.addslashes($_POST['stress_after_plan']).'\'
				 where org_no      = \''.$stress_code.'\'
				   and stress_yymm = \''.$stress_yymm.'\'
				   and stress_seq  = \''.$stress_seq.'\'';

		if (!$conn->execute($sql)){
			$conn->rollback();
			echo $conn->err_back();
			if ($conn->mode == 1) exit;
		}
	}
?>