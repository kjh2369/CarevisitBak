<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	/*********************************************************

		파라메타

	*********************************************************/
	$code = $_POST['code'];
	$date = $_POST['date'];
	$seq  = $_POST['seq'];

	$min_dt = $_POST['min_dt'];
	$max_dt = $_POST['max_dt'];

	$list = $_POST['check'];

	#if ($debug) $conn->mode = 2;

	/*********************************************************

		서비스

	*********************************************************/
	if (is_array($list)){
		$conn->begin();


		/*********************************************************
			기존데이타 초기화
		*********************************************************/
		$sql = 'update t01iljung
				   set t01_wrk_date   = \'\'
				,      t01_wrk_fmtime = \'\'
				,      t01_wrk_totime = \'\'

				,      t01_conf_date       = \'\'
				,      t01_conf_fmtime     = \'\'
				,      t01_conf_totime     = \'\'
				,      t01_conf_soyotime   = \'0\'
				,      t01_conf_suga_code  = \'\'
				,      t01_conf_suga_value = \'0\'

				,      t01_request = \'\'
				 where t01_ccode       = \''.$code.'\'
				   and t01_sugup_date >= \''.$min_dt.'\'
				   and t01_sugup_date <= \''.$max_dt.'\'
				   and t01_request     = \'TEXT\'
				   and t01_del_yn      = \'N\'';

		if (!$conn->execute($sql)){
			$conn->rollback();
			$conn->err_back();
			if ($conn->mode == 1) exit;
		}

		$sql = '';

		foreach($list as $i => $svc_para){
			/*********************************************************
				파라메타
			*********************************************************/
			parse_str($svc_para, $svc);

			$sql = 'select mst_dt			as mst_dt
					,      mst_seq			as mst_seq
					,      nhic_seq			as nhic_seq
					,      nhic_kind		as kind
					,      nhic_c_cd		as c_cd
					,      nhic_m_cd1		as m_cd1
					,      nhic_m_nm1		as m_nm1
					,      nhic_m_cd2		as m_cd2
					,      nhic_m_nm2		as m_nm2
					,      nhic_dt			as dt
					,      nhic_plan_from	as p_from
					,      nhic_plan_to		as p_to
					,      nhic_plan_time	as p_time
					,      nhic_plan_seq	as p_seq
					,      nhic_conf_from	as c_from
					,      nhic_conf_to		as c_to
					,      nhic_conf_time	as c_time
					,      nhic_work_time	as w_time
					,      nhic_suga_cd		as suga_cd
					,      nhic_suga_cost_t	as suga_cost
					  from nhic_log
					 where org_no        = \''.$code.'\'
					   and mst_dt        = \''.$svc['mst_dt'].'\'
					   and mst_seq       = \''.$svc['mst_seq'].'\'
					   and nhic_seq      = \''.$svc['mst_no'].'\'
					   and nhic_claim_yn = \'Y\'';

			$conn->query($sql);
			$conn->fetch();

			$row_count = $conn->row_count();

			for($r=0; $r<$row_count; $r++){
				$row = $conn->select_row($r);

				$time  = $myF->time2min($row['p_from']);
				$time += $row['w_time'];
				$w_to  = str_replace(':', '', $myF->min2time($time));


				/*********************************************************
					일정 데이타 수정
				*********************************************************/
				$sql = 'update t01iljung
						   set t01_wrk_date   = \''.$row['dt'].'\'
						,      t01_wrk_fmtime = \''.$row['p_from'].'\'
						,      t01_wrk_totime = \''.$w_to.'\'

						,      t01_conf_date       = \''.$row['dt'].'\'
						,      t01_conf_fmtime     = \''.$row['c_from'].'\'
						,      t01_conf_totime     = \''.$row['c_to'].'\'
						,      t01_conf_soyotime   = \''.$row['c_time'].'\'
						,      t01_conf_suga_code  = \''.$row['suga_cd'].'\'
						,      t01_conf_suga_value = \''.$row['suga_cost'].'\'

						,      t01_status_gbn = \'1\'
						,      t01_request    = \'TEXT\'

						 where t01_ccode        = \''.$code.'\'
						   and t01_mkind        = \''.$row['kind'].'\'
						   and t01_jumin        = \''.$row['c_cd'].'\'
						   and t01_yoyangsa_id1 = \''.$row['m_cd1'].'\'
						   and t01_sugup_date   = \''.$row['dt'].'\'
						   and t01_sugup_fmtime = \''.$row['p_from'].'\'
						   and t01_sugup_seq    = \''.$row['p_seq'].'\'';

				if (!$conn->execute($sql)){
					$conn->rollback();
					$conn->err_back();
					if ($conn->mode == 1) exit;
				}



				/*********************************************************
					LOG 등록 플래그 수정
				*********************************************************/
				$sql = 'update nhic_log
						   set nhic_apply_yn = \'Y\'
						 where org_no   = \''.$code.'\'
						   and mst_dt   = \''.$row['mst_dt'].'\'
						   and mst_seq  = \''.$row['mst_seq'].'\'
						   and nhic_seq = \''.$row['nhic_seq'].'\'';

				if (!$conn->execute($sql)){
					$conn->rollback();
					$conn->err_back();
					if ($conn->mode == 1) exit;
				}
			}

			$conn->row_free();
		}

		$conn->commit();
	}

	#if($debug){
	#	echo '<div style=\'overflow-x:hidden; overflow-y:scroll; width:100%; height:300px;\'>';
	#	echo nl2br($conn->error_query);
	#	echo '</div>';
	#}

	include_once('../inc/_db_close.php');


	echo $myF->header_script();
	echo '<form name=\'f\' method=\'post\' target=\'_self\' action=\'../nhic/nhic_apply.php\'>';
	echo '<input name=\'code\'	 type=\'hidden\' value=\''.$code.'\'>';
	echo '<input name=\'date\'	 type=\'hidden\' value=\''.$date.'\'>';
	echo '<input name=\'seq\'	 type=\'hidden\' value=\''.$seq.'\'>';
	echo '<input name=\'min_dt\' type=\'hidden\' value=\''.$min_dt.'\'>';
	echo '<input name=\'max_dt\' type=\'hidden\' value=\''.$max_dt.'\'>';
	echo '</form>';
	echo '<script>';
	echo 'alert(\''.$myF->message('ok', 'N').'\');';

	if ($conn->mode == 1)
		echo 'document.f.submit();';

	echo '</script>';
?>