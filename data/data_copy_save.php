<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_http_uri.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	$code1 = $_POST['code1'];
	$code2 = $_POST['code2'];

	if (Empty($code1) || Empty($code2)){
		echo 9;
		exit;
	}

	$quitYn = $_POST['quitYn']; //퇴사처리여부
	$quitDt = Str_Replace('-','',$_POST['quitDt']); //퇴사일자
	$joinYn = $_POST['joinYn']; //입사처리여부
	$joinDt = Str_Replace('-','',$_POST['joinDt']); //입사일자

	$endYn = $_POST['endYn']; //중지여부
	$endDt = Str_Replace('-','',$_POST['endDt']); //중지일자
	$useYn = $_POST['useYn']; //이용여부
	$useDt =  Str_Replace('-','',$_POST['useDt']); //이용일자

	$nowCYn = $_POST['nowCYn']; //일정삭제여부
	$nowCDt = Str_Replace('-','',$_POST['nowCDt']); //일정삭제일자

	$newCYn = $_POST['newCYn']; //일정복사여부
	$newCDt = Str_Replace('-','',$_POST['newCDt']); //일정복사일자

	/*********************************************************/
	//직원 정보
	$sql = 'SELECT *
			  FROM m02yoyangsa
			 WHERE m02_ccode = \''.$code1.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		//직원정보
		$sql = 'REPLACE INTO m02yoyangsa (
				 m02_ccode
				,m02_mkind
				,m02_yjumin
				,m02_key
				,m02_yname
				,m02_mem_no
				,m02_ytel
				,m02_ytel2
				,m02_email
				,m02_ypostno
				,m02_yjuso1
				,m02_yjuso2
				,m02_yjikjong
				,m02_ybank_name
				,m02_ybank_holder
				,m02_ygyeoja_no
				,m02_y4bohum_umu
				,m02_ygobohum_umu
				,m02_ysnbohum_umu
				,m02_ygnbohum_umu
				,m02_ykmbohum_umu
				,m02_ygongjeja_no
				,m02_ygongjejaye_no
				,m02_ykuksin_mpay
				,m02_health_mpay
				,m02_employ_mpay
				,m02_sanje_mpay
				,m02_jikwon_gbn
				,m02_ygoyong_kind

				,m02_ygoyong_stat
				,m02_yipsail
				,m02_ytoisail

				,m02_ygunmu_mon
				,m02_ygunmu_tue
				,m02_ygunmu_wed
				,m02_ygunmu_thu
				,m02_ygunmu_fri
				,m02_ygunmu_sat
				,m02_weekly_holiday
				,m02_bipay_yn
				,m02_bipay_rate
				,m02_ygunmu_sun
				,m02_ygupyeo_kind
				,m02_pay_type
				,m02_ygibonkup
				,m02_ysuga_yoyul
				,m02_yfamcare_umu
				,m02_yfamcare_pay
				,m02_yfamcare_type
				,m02_pay_step
				,m02_bnpay_yn
				,m02_family_pay_yn

				,m02_rank_pay
				,m02_add_payrate
				,m02_holiday_payrate_yn
				,m02_holiday_payrate
				,m02_ma_yn
				,m02_ma_dt
				,m02_stnd_work_time
				,m02_stnd_work_pay
				,m02_dept_cd
				,m02_memo
				,m02_mobile_kind
				,m02_rfid_yn
				,m02_paye_yn
				,m02_picture
				,m02_meal_pay
				,m02_car_pay
				,m02_del_yn) VALUES (

				 \''.$code2.'\'
				,\''.$row['m02_mkind'].'\'
				,\''.$row['m02_yjumin'].'\'
				,\''.$row['m02_key'].'\'
				,\''.$row['m02_yname'].'\'
				,\''.$row['m02_mem_no'].'\'
				,\''.$row['m02_ytel'].'\'
				,\''.$row['m02_ytel2'].'\'
				,\''.$row['m02_email'].'\'
				,\''.$row['m02_ypostno'].'\'
				,\''.addslashes($row['m02_yjuso1']).'\'
				,\''.addslashes($row['m02_yjuso2']).'\'
				,\''.$row['m02_yjikjong'].'\'
				,\''.$row['m02_ybank_name'].'\'
				,\''.$row['m02_ybank_holder'].'\'
				,\''.$row['m02_ygyeoja_no'].'\'
				,\''.$row['m02_y4bohum_umu'].'\'
				,\''.$row['m02_ygobohum_umu'].'\'
				,\''.$row['m02_ysnbohum_umu'].'\'
				,\''.$row['m02_ygnbohum_umu'].'\'
				,\''.$row['m02_ykmbohum_umu'].'\'
				,\''.$row['m02_ygongjeja_no'].'\'
				,\''.$row['m02_ygongjejaye_no'].'\'
				,\''.$row['m02_ykuksin_mpay'].'\'
				,\''.$row['m02_health_mpay'].'\'
				,\''.$row['m02_employ_mpay'].'\'
				,\''.$row['m02_sanje_mpay'].'\'
				,\''.$row['m02_jikwon_gbn'].'\'
				,\''.$row['m02_ygoyong_kind'].'\'';

		if ($joinYn == 'Y'){
			if ($row['m02_ygoyong_stat'] == '1'){
				$sql .=',\'1\'
						,\''.($joinDt > $row['m02_yipsail'] ? $joinDt : $row['m02_yipsail']).'\'
						,\'\'';
			}else{
				$sql .=',\''.$row['m02_ygoyong_stat'].'\'
						,\''.$row['m02_yipsail'].'\'
						,\''.$row['m02_ytoisail'].'\'';
			}
		}else{
			$sql .=',\''.$row['m02_ygoyong_stat'].'\'
					,\''.$row['m02_yipsail'].'\'
					,\''.$row['m02_ytoisail'].'\'';
		}

		$sql .=',\''.$row['m02_ygunmu_mon'].'\'
				,\''.$row['m02_ygunmu_tue'].'\'
				,\''.$row['m02_ygunmu_wed'].'\'
				,\''.$row['m02_ygunmu_thu'].'\'
				,\''.$row['m02_ygunmu_fri'].'\'
				,\''.$row['m02_ygunmu_sat'].'\'
				,\''.$row['m02_weekly_holiday'].'\'
				,\''.$row['m02_bipay_yn'].'\'
				,\''.$row['m02_bipay_rate'].'\'
				,\''.$row['m02_ygunmu_sun'].'\'
				,\''.$row['m02_ygupyeo_kind'].'\'
				,\''.$row['m02_pay_type'].'\'
				,\''.$row['m02_ygibonkup'].'\'
				,\''.$row['m02_ysuga_yoyul'].'\'
				,\''.$row['m02_yfamcare_umu'].'\'
				,\''.$row['m02_yfamcare_pay'].'\'
				,\''.$row['m02_yfamcare_type'].'\'
				,\''.$row['m02_pay_step'].'\'
				,\''.$row['m02_bnpay_yn'].'\'
				,\''.$row['m02_family_pay_yn'].'\'

				,\''.$row['m02_rank_pay'].'\'
				,\''.$row['m02_add_payrate'].'\'
				,\''.$row['m02_holiday_payrate_yn'].'\'
				,\''.$row['m02_holiday_payrate'].'\'
				,\''.$row['m02_ma_yn'].'\'
				,\''.$row['m02_ma_dt'].'\'
				,\''.$row['m02_stnd_work_time'].'\'
				,\''.$row['m02_stnd_work_pay'].'\'
				,\''.$row['m02_dept_cd'].'\'
				,\''.$row['m02_memo'].'\'
				,\''.$row['m02_mobile_kind'].'\'
				,\''.$row['m02_rfid_yn'].'\'
				,\''.$row['m02_paye_yn'].'\'
				,\''.$row['m02_picture'].'\'
				,\''.$row['m02_meal_pay'].'\'
				,\''.$row['m02_car_pay'].'\'
				,\''.$row['m02_del_yn'].'\')';


		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->row_free();
			 echo '901'.chr(13).chr(10);
			 echo $conn->error_msg.chr(13).chr(10).$conn->error_query;
			 exit;
		}

		//기존 직원수정
		if ($quitYn == 'Y'){
			if ($row['m02_ygoyong_stat'] == '1'){
				$sql = 'UPDATE m02yoyangsa
						   SET m02_ygoyong_stat = \'9\'
						,      m02_ytoisail     = \''.$quitDt.'\'
						 WHERE m02_ccode  = \''.$row['m02_ccode'].'\'
						   AND m02_mkind  = \''.$row['m02_mkind'].'\'
						   AND m02_yjumin = \''.$row['m02_yjumin'].'\'';

				if (!$conn->execute($sql)){
					 $conn->rollback();
					 $conn->row_free();
					 echo 902;
					 exit;
				}
			}
		}
	}

	$conn->row_free();

	/*********************************************************/

	//직원변경내역
	$sql = 'SELECT *
			  FROM mem_his AS his
			 WHERE org_no = \''.$code1.'\'
			 ORDER BY jumin, seq DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpJumin != $row['jumin']){
			$tmpJumin  = $row['jumin'];

			$sql = 'REPLACE INTO mem_his (
					 org_no
					,jumin
					,seq
					,leave_from
					,leave_to
					,com_no
					,mem_id
					,employ_type
					,employ_stat
					,join_dt
					,quit_dt
					,weekly
					,bank_acct
					,bank_no
					,bank_nm
					,prolong_rate
					,holiday_rate_gbn
					,holiday_rate
					,ins_yn
					,annuity_yn
					,health_yn
					,sanje_yn
					,employ_yn
					,paye_yn
					,annuity_amt
					,insu_yn
					,compare_yn
					,compare_jobs
					,compare_jobstr) VALUES (
					 \''.$code2.'\'
					,\''.$row['jumin'].'\'
					,\'1\'';

			if (!Empty($row['leave_from']) && !Empty($row['leave_to'])){
				$sql .= ',\''.$row['leave_from'].'\'
						 ,\''.$row['leave_to'].'\'';
			}else{
				$sql .= ',NULL
						 ,NULL';
			}

			$sql .=',\''.$row['com_no'].'\'
					,\''.$row['mem_id'].'\'
					,\''.$row['employ_type'].'\'';

			$lsEmployStat = $row['employ_stat'];
			$lsJoinDt = $row['join_dt'];
			$lsQuitDt = $row['quit_dt'];

			if ($joinYn == 'Y'){
				if ($row['employ_stat'] == '1'){
					if ($myF->dateStyle($joinDt) > $row['join_dt']){
						$lsJoinDt = $myF->dateStyle($joinDt);
					}

					$lsQuitDt = '';
				}
			}

			$sql .=',\''.$lsEmployStat.'\'
					,\''.$lsJoinDt.'\'';

			if (!Empty($lsQuitDt)){
				$sql .=',\''.$lsQuitDt.'\'';
			}else{
				$sql .= ',NULL';
			}

			$sql .=',\''.$row['weekly'].'\'
					,\''.$row['bank_acct'].'\'
					,\''.$row['bank_no'].'\'
					,\''.$row['bank_nm'].'\'
					,\''.$row['prolong_rate'].'\'
					,\''.$row['holiday_rate_gbn'].'\'
					,\''.$row['holiday_rate'].'\'
					,\''.$row['ins_yn'].'\'
					,\''.$row['annuity_yn'].'\'
					,\''.$row['health_yn'].'\'
					,\''.$row['sanje_yn'].'\'
					,\''.$row['employ_yn'].'\'
					,\''.$row['paye_yn'].'\'
					,\''.$row['annuity_amt'].'\'
					,\''.$row['insu_yn'].'\'
					,\''.$row['compare_yn'].'\'
					,\''.$row['compare_jobs'].'\'
					,\''.$row['compare_jobstr'].'\'
					)';

			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->row_free();
				 echo $conn->error_msg;
				 echo 903;
				 exit;
			}

			//기존 데이타수정
			if ($quitYn == 'Y'){
				if ($row['employ_stat'] == '1'){
					$sql = 'UPDATE mem_his
							   SET employ_stat = \'9\'
							,      quit_dt     = \''.$myF->dateStyle($quitDt).'\'
							 WHERE org_no = \''.$row['org_no'].'\'
							   AND jumin  = \''.$row['jumin'].'\'
							   AND seq    = \''.$row['seq'].'\'';

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->row_free();
						 echo 904;
						 exit;
					}
				}
			}
		}
	}

	$conn->row_free();

	UnSet($tmpJumin);

	/*********************************************************/


	//직원보험 이력
	$sql = 'SELECT	*
			FROM	mem_insu
			WHERE	org_no	 = \''.$code1.'\'
			AND		from_dt <= \''.$joinDt.'\'
			AND		to_dt	>= \''.$joinDt.'\'
			ORDER	BY jumin, seq DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpJumin != $row['jumin']){
			$tmpJumin  = $row['jumin'];

			$lsFromDt	= $row['from_dt'];
			$lsToDt		= $row['to_dt'];

			if ($joinDt > $row['from_dt']){
				$lsFromDt = $joinDt;
			}

			$sql = 'REPLACE INTO mem_insu(
					 org_no
					,jumin
					,seq
					,from_dt
					,to_dt
					,annuity_yn
					,health_yn
					,employ_yn
					,sanje_yn
					,monthly
					,paye_yn) VALUES (
					 \''.$code2.'\'
					,\''.$row['jumin'].'\'
					,\'1\'
					,\''.$lsFromDt.'\'
					,\''.$lsToDt.'\'
					,\''.$row['annuity_yn'].'\'
					,\''.$row['health_yn'].'\'
					,\''.$row['employ_yn'].'\'
					,\''.$row['sanje_yn'].'\'
					,\''.$row['monthly'].'\'
					,\''.$row['paye_yn'].'\'
					)';

			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->row_free();
				 echo $conn->error_msg;
				 echo 903;
				 exit;
			}
		}
	}

	$conn->row_free();

	UnSet($tmpJumin);


	//기타정보
	$sql = 'SELECT *
			  FROM mem_extra
			 WHERE org_no = \''.$code1.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$sql = 'REPLACE INTO mem_extra (
				 org_no
				,jumin
				,extra500_1
				,extra500_2
				,extra500_3
				,extra800_1
				,extra800_2
				,extra800_3) VALUES (
				 \''.$code2.'\'
				,\''.$row['jumin'].'\'
				,\''.$row['extra500_1'].'\'
				,\''.$row['extra500_2'].'\'
				,\''.$row['extra500_3'].'\'
				,\''.$row['extra800_1'].'\'
				,\''.$row['extra800_2'].'\'
				,\''.$row['extra800_3'].'\')';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->row_free();
			 echo 905;
			 exit;
		}
	}

	$conn->row_free();

	/*********************************************************/

	$sql = 'SELECT *
			  FROM mem_option
			 WHERE org_no = \''.$code1.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$sql = 'REPLACE INTO mem_option (
				 org_no
				,mo_jumin
				,mo_extrapay_yn
				,mo_salary_yn
				,family_yn
				,extratime_yn
				,insu_yn
				,insert_id
				,insert_dt) VALUES (
				 \''.$code2.'\'
				,\''.$row['mo_jumin'].'\'
				,\''.$row['mo_extrapay_yn'].'\'
				,\''.$row['mo_salary_yn'].'\'
				,\''.$row['family_yn'].'\'
				,\''.$row['extratime_yn'].'\'
				,\''.$row['insu_yn'].'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW())';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->row_free();
			 echo 906;
			 exit;
		}
	}

	/*********************************************************/

	$sql = 'SELECT *
			  FROM mem_hourly
			 WHERE org_no = \''.$code1.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$sql = 'REPLACE INTO mem_hourly (
				 org_no
				,mh_jumin
				,mh_svc
				,mh_seq
				,mh_kind
				,mh_type
				,mh_hourly
				,mh_vary_hourly_1
				,mh_vary_hourly_2
				,mh_vary_hourly_3
				,mh_vary_hourly_4
				,mh_vary_hourly_5
				,mh_vary_hourly_6
				,mh_vary_hourly_7
				,mh_vary_hourly_8
				,mh_vary_hourly_9
				,mh_hourly_rate
				,mh_fixed_pay
				,mh_extra_yn
				,mh_from_dt
				,mh_to_dt
				,del_flag
				,insert_id
				,insert_dt) VALUES (
				 \''.$code2.'\'
				,\''.$row['mh_jumin'].'\'
				,\''.$row['mh_svc'].'\'
				,\''.$row['mh_seq'].'\'
				,\''.$row['mh_kind'].'\'
				,\''.$row['mh_type'].'\'
				,\''.$row['mh_hourly'].'\'
				,\''.$row['mh_vary_hourly_1'].'\'
				,\''.$row['mh_vary_hourly_2'].'\'
				,\''.$row['mh_vary_hourly_3'].'\'
				,\''.$row['mh_vary_hourly_4'].'\'
				,\''.$row['mh_vary_hourly_5'].'\'
				,\''.$row['mh_vary_hourly_6'].'\'
				,\''.$row['mh_vary_hourly_7'].'\'
				,\''.$row['mh_vary_hourly_8'].'\'
				,\''.$row['mh_vary_hourly_9'].'\'
				,\''.$row['mh_hourly_rate'].'\'
				,\''.$row['mh_fixed_pay'].'\'
				,\''.$row['mh_extra_yn'].'\'
				,\''.$row['mh_from_dt'].'\'
				,\''.$row['mh_to_dt'].'\'
				,\''.$row['del_flag'].'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW())';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->row_free();
			 echo 907;
			 exit;
		}
	}

	/*********************************************************/

	$sql = 'SELECT *
			  FROM mem_salary
			 WHERE org_no = \''.$code1.'\'';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		$sql = 'REPLACE INTO mem_salary (
				 org_no
				,ms_jumin
				,ms_seq
				,ms_salary
				,ms_extra_yn
				,ms_care_yn
				,ms_from_dt
				,ms_to_dt
				,del_flag
				,insert_id
				,insert_dt) VALUES (
				 \''.$code2.'\'
				,\''.$row['ms_jumin'].'\'
				,\''.$row['ms_seq'].'\'
				,\''.$row['ms_salary'].'\'
				,\''.$row['ms_extra_yn'].'\'
				,\''.$row['ms_care_yn'].'\'
				,\''.$row['ms_from_dt'].'\'
				,\''.$row['ms_to_dt'].'\'
				,\''.$row['del_flag'].'\'
				,\''.$_SESSION['userCode'].'\'
				,NOW())';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->row_free();
			 echo 908;
			 exit;
		}
	}

	/*********************************************************/

	$sql = 'REPLACE INTO counsel_mem (
			 org_no
			,mem_ssn
			,mem_nm
			,mem_picture
			,mem_marry
			,mem_phone
			,mem_mobile
			,mem_email
			,mem_postno
			,mem_addr
			,mem_addr_dtl
			,mem_edu_lvl
			,mem_gbn
			,mem_abode
			,mem_religion
			,mem_rel_other
			,mem_hobby
			,mem_dis_lvl
			,mem_dis_nm
			,mem_app_path
			,mem_app_other
			,mem_svc_work
			,mem_svc_other
			,mem_hope_work
			,mem_hope_other
			,mem_work_time
			,mem_salary
			,mem_hourly
			,mem_talker_id
			,mem_talker_nm
			,mem_counsel_gbn
			,mem_counsel_dt
			,mem_counsel_content
			,mem_counsel_action
			,mem_counsel_result
			,mem_counsel_other
			,mem_insert_id
			,mem_insert_dt
			,del_flag)
			SELECT \''.$code2.'\' AS org_no
			,      mem_ssn
			,      mem_nm
			,      mem_picture
			,      mem_marry
			,      mem_phone
			,      mem_mobile
			,      mem_email
			,      mem_postno
			,      mem_addr
			,      mem_addr_dtl
			,      mem_edu_lvl
			,      mem_gbn
			,      mem_abode
			,      mem_religion
			,      mem_rel_other
			,      mem_hobby
			,      mem_dis_lvl
			,      mem_dis_nm
			,      mem_app_path
			,      mem_app_other
			,      mem_svc_work
			,      mem_svc_other
			,      mem_hope_work
			,      mem_hope_other
			,      mem_work_time
			,      mem_salary
			,      mem_hourly
			,      mem_talker_id
			,      mem_talker_nm
			,      mem_counsel_gbn
			,      mem_counsel_dt
			,      mem_counsel_content
			,      mem_counsel_action
			,      mem_counsel_result
			,      mem_counsel_other
			,      \''.$_SESSION['userCode'].'\' AS mem_insert_id
			,      NOW() AS mem_insert_dt
			,      del_flag
			  FROM counsel_mem
			 WHERE org_no   = \''.$code1.'\'
			   AND del_flag = \'N\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo $conn->error_msg;
		 echo 909;
		 exit;
	}

	/*********************************************************/

	$sql = 'REPLACE INTO counsel_family (org_no,family_type,family_ssn,family_seq,family_nm,family_rel,family_age,family_job,family_with,family_monthly)
			SELECT \''.$code2.'\',family_type,family_ssn,family_seq,family_nm,family_rel,family_age,family_job,family_with,family_monthly
			  FROM counsel_family
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 910;
		 exit;
	}

	/*********************************************************/

	$sql = 'REPLACE INTO counsel_edu (org_no,edu_type,edu_ssn,edu_seq,edu_gbn,edu_center,edu_nm,edu_from_dt,edu_to_dt,edu_time)
			SELECT \''.$code2.'\',edu_type,edu_ssn,edu_seq,edu_gbn,edu_center,edu_nm,edu_from_dt,edu_to_dt,edu_time
			  FROM counsel_edu
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 911;
		 exit;
	}

	/*********************************************************/

	$sql = 'REPLACE INTO counsel_license (org_no,license_type,license_ssn,license_seq,license_gbn,license_no,license_center,license_dt)
			SELECT \''.$code2.'\',license_type,license_ssn,license_seq,license_gbn,license_no,license_center,license_dt
			  FROM counsel_license
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 912;
		 exit;
	}

	/*********************************************************/

	$sql = 'REPLACE INTO counsel_stress (org_no,stress_ssn,stress_seq,stress_dt,stress_talker_ssn,stress_talker_nm,stress_type,stress_work_hard,stress_work_aptitude,stress_work_client,stress_work_day,stress_work_week,stress_work_month,stress_work_hope_time,stress_work_hope_pay,stress_work_change,stress_person_family,stress_person_economy,stress_person_health,stress_person_other,stress_center_meet,stress_center_edu,stress_center_worker,stress_center_person,stress_center_other,stress_self_edu,stress_self_meet,stress_self_other,stress_other,stress_talker_cont,stress_result,del_flag,insert_dt,insert_id,update_dt,update_id)
			SELECT \''.$code2.'\',stress_ssn,stress_seq,stress_dt,stress_talker_ssn,stress_talker_nm,stress_type,stress_work_hard,stress_work_aptitude,stress_work_client,stress_work_day,stress_work_week,stress_work_month,stress_work_hope_time,stress_work_hope_pay,stress_work_change,stress_person_family,stress_person_economy,stress_person_health,stress_person_other,stress_center_meet,stress_center_edu,stress_center_worker,stress_center_person,stress_center_other,stress_self_edu,stress_self_meet,stress_self_other,stress_other,stress_talker_cont,stress_result,del_flag,insert_dt,insert_id,update_dt,update_id
			  FROM counsel_stress
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 913;
		 exit;
	}

	/*********************************************************/

	$sql = 'REPLACE INTO counsel_client_stress (org_no,stress_yymm,stress_seq,stress_c_cd,stress_m_cd,stress_m_nm,stress_dt,stress_rct_kind,stress_rct_kind_family,stress_rct_kind_other,stress_rct_path,stress_rct_path_paper_yn,stress_rct_path_other,stress_cont_kind,stress_cont_text,stress_proc_kind,stress_proc_text,stress_rst_obj,stress_rst_sub,stress_rst_app,stress_rst_otr,stress_after_plan,del_flag,insert_dt,insert_id,update_dt,update_id)
			SELECT \''.$code2.'\',stress_yymm,stress_seq,stress_c_cd,stress_m_cd,stress_m_nm,stress_dt,stress_rct_kind,stress_rct_kind_family,stress_rct_kind_other,stress_rct_path,stress_rct_path_paper_yn,stress_rct_path_other,stress_cont_kind,stress_cont_text,stress_proc_kind,stress_proc_text,stress_rst_obj,stress_rst_sub,stress_rst_app,stress_rst_otr,stress_after_plan,del_flag,insert_dt,insert_id,update_dt,update_id
			  FROM counsel_client_stress
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 914;
		 exit;
	}

	/*********************************************************/

	$sql = 'REPLACE INTO counsel_client_case (org_no,case_yymm,case_seq,case_c_cd,case_dt,case_run_cd,case_run_nm,case_present_nm,case_svc_kind,case_use_from,case_use_to,case_m_cd,case_m_nm,case_m_age,case_m_gender,case_m_career,case_economy,case_family,case_soul,case_body,case_other,case_main_quest,case_present_talk,case_later_plan,case_proc_period,case_after_plan,del_flag,insert_dt,insert_id,update_dt,update_id)
			SELECT \''.$code2.'\',case_yymm,case_seq,case_c_cd,case_dt,case_run_cd,case_run_nm,case_present_nm,case_svc_kind,case_use_from,case_use_to,case_m_cd,case_m_nm,case_m_age,case_m_gender,case_m_career,case_economy,case_family,case_soul,case_body,case_other,case_main_quest,case_present_talk,case_later_plan,case_proc_period,case_after_plan,del_flag,insert_dt,insert_id,update_dt,update_id
			  FROM counsel_client_case
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 915;
		 exit;
	}


	/*********************************************************

		고객등록

	 *********************************************************/

	/* m03sugupja ********************************************************/
	$sql = 'REPLACE INTO m03sugupja (m03_ccode,m03_mkind,m03_jumin,m03_subcd,m03_vlvl,m03_tel,m03_hp,m03_name,m03_juso1,m03_juso2,m03_post_no,m03_gaeyak_fm,m03_gaeyak_to,m03_yccode,m03_ylvl,m03_byungmung,m03_disease_nm,m03_stat_nogood,m03_injung_no,m03_familycare,m03_skind,m03_bonin_yul,m03_kupyeo_max,m03_kupyeo_1,m03_kupyeo_2,m03_injung_from,m03_injung_to,m03_skigwan_name,m03_skigwan_code,m03_yoyangsa1,m03_yoyangsa2,m03_yoyangsa3,m03_yoyangsa4,m03_yoyangsa5,m03_yoyangsa1_nm,m03_yoyangsa2_nm,m03_yoyangsa3_nm,m03_yoyangsa4_nm,m03_yoyangsa5_nm,m03_partner,m03_yboho_name,m03_yboho_juminno,m03_yboho_gwange,m03_yboho_addr,m03_yboho_phone,m03_key,m03_sdate,m03_edate,m03_sugup_status,m03_bath_add_yn,m03_sgbn,m03_stop_reason,m03_overtime,m03_add_time1,m03_add_time2,m03_client_no,m03_memo,m03_bipay1,m03_bipay2,m03_bipay3,m03_expense_yn,m03_expense_pay,m03_baby_svc_cnt,m03_add_pay_gbn,m03_del_yn)
			SELECT \''.$code2.'\',m03_mkind,m03_jumin,m03_subcd,m03_vlvl,m03_tel,m03_hp,m03_name,m03_juso1,m03_juso2,m03_post_no,m03_gaeyak_fm,m03_gaeyak_to,m03_yccode,m03_ylvl,m03_byungmung,m03_disease_nm,m03_stat_nogood,m03_injung_no,m03_familycare,m03_skind,m03_bonin_yul,m03_kupyeo_max,m03_kupyeo_1,m03_kupyeo_2,m03_injung_from,m03_injung_to,m03_skigwan_name,m03_skigwan_code,m03_yoyangsa1,m03_yoyangsa2,m03_yoyangsa3,m03_yoyangsa4,m03_yoyangsa5,m03_yoyangsa1_nm,m03_yoyangsa2_nm,m03_yoyangsa3_nm,m03_yoyangsa4_nm,m03_yoyangsa5_nm,m03_partner,m03_yboho_name,m03_yboho_juminno,m03_yboho_gwange,m03_yboho_addr,m03_yboho_phone,m03_key,m03_sdate,m03_edate,m03_sugup_status,m03_bath_add_yn,m03_sgbn,m03_stop_reason,m03_overtime,m03_add_time1,m03_add_time2,m03_client_no,m03_memo,m03_bipay1,m03_bipay2,m03_bipay3,m03_expense_yn,m03_expense_pay,m03_baby_svc_cnt,m03_add_pay_gbn,m03_del_yn
			  FROM m03sugupja
			 WHERE m03_ccode = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 916;
		 exit;
	}

	/* m31sugupja ********************************************************/
	$sql = 'REPLACE INTO m31sugupja (m31_ccode,m31_mkind,m31_jumin,m31_sdate,m31_edate,m31_level,m31_kind,m31_bonin_yul,m31_kupyeo_max,m31_kupyeo_1,m31_kupyeo_2,m31_status,m31_gaeyak_fm,m31_gaeyak_to,m31_vlvl,m31_sgbn,m31_stop_reason,m31_overtime,m31_add_time1,m31_add_time2,m31_add_pay_gbn)
			SELECT \''.$code2.'\',m31_mkind,m31_jumin,m31_sdate,m31_edate,m31_level,m31_kind,m31_bonin_yul,m31_kupyeo_max,m31_kupyeo_1,m31_kupyeo_2,m31_status,m31_gaeyak_fm,m31_gaeyak_to,m31_vlvl,m31_sgbn,m31_stop_reason,m31_overtime,m31_add_time1,m31_add_time2,m31_add_pay_gbn
			  FROM m31sugupja
			 WHERE m31_ccode = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 917;
		 exit;
	}

	/* client_family ********************************************************/
	$sql = 'REPLACE INTO client_family (org_no,cf_jumin,cf_seq,cf_mem_cd,cf_mem_nm,cf_kind)
			SELECT \''.$code2.'\',cf_jumin,cf_seq,cf_mem_cd,cf_mem_nm,cf_kind
			  FROM client_family
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 918;
		 exit;
	}

	/* client_his_svc ********************************************************/
	$sql = 'SELECT *
			  FROM client_his_svc
			 WHERE org_no = \''.$code1.'\'
			 ORDER BY jumin, svc_cd, seq DESC';

	$conn->query($sql);
	$conn->fetch();

	$rowCount = $conn->row_count();

	for($i=0; $i<$rowCount; $i++){
		$row = $conn->select_row($i);

		if ($tmpKey != $row['jumin'].'_'.$row['svc_cd']){
			$tmpKey  = $row['jumin'].'_'.$row['svc_cd'];

			$sql = 'REPLACE INTO client_his_svc (
					 org_no
					,jumin
					,svc_cd
					,seq
					,from_dt
					,to_dt
					,svc_stat
					,svc_reason
					,app_no
					,insert_id
					,insert_dt) VALUES (
					 \''.$code2.'\'
					,\''.$row['jumin'].'\'
					,\''.$row['svc_cd'].'\'
					,\'1\'';

			if ($useYn == 'Y'){
				if ($myF->dateStyle($useDt) >= $row['from_dt'] &&
					$myF->dateStyle($useDt) <= $row['to_dt']){
					$sql .=',\''.$myF->dateStyle($useDt).'\'';
				}else{
					$sql .=',\''.$row['from_dt'].'\'';
				}

				$sql .=',\''.$row['to_dt'].'\'';
			}else{
				$sql .=',\''.$row['from_dt'].'\'
						,\''.$row['to_dt'].'\'';
			}

			$sql .=',\''.$row['svc_stat'].'\'
					,\''.$row['svc_reason'].'\'
					,\''.$row['app_no'].'\'
					,\''.$_SESSION['userCode'].'\'
					,NOW())';

			if (!$conn->execute($sql)){
				 $conn->rollback();
				 $conn->row_free();
				 echo 919;
				 exit;
			}

			if ($endYn == 'Y'){
				if ($myF->dateStyle($endDt) >= $row['from_dt'] &&
					$myF->dateStyle($endDt) <= $row['to_dt']){
					$sql = 'UPDATE client_his_svc
							   SET svc_stat = \'9\'
							,      to_dt    = \''.$myF->dateStyle($endDt).'\'
							 WHERE org_no = \''.$row['org_no'].'\'
							   AND jumin  = \''.$row['jumin'].'\'
							   AND svc_cd = \''.$row['svc_cd'].'\'
							   AND seq    = \''.$row['seq'].'\'';

					if (!$conn->execute($sql)){
						 $conn->rollback();
						 $conn->row_free();
						 echo 920;
						 exit;
					}
				}
			}
		}
	}

	$conn->row_free();

	UnSet($tmpKey);

	/* client_his_kind ********************************************************/
	$sql = 'REPLACE INTO client_his_kind (org_no,jumin,seq,from_dt,to_dt,kind,rate,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',jumin,seq,from_dt,to_dt,kind,rate,insert_id,insert_dt,update_id,update_dt
			  FROM client_his_kind
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 921;
		 exit;
	}

	/* client_his_lvl ********************************************************/
	$sql = 'REPLACE INTO client_his_lvl (org_no,jumin,svc_cd,seq,from_dt,to_dt,app_no,level,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',jumin,svc_cd,seq,from_dt,to_dt,app_no,level,insert_id,insert_dt,update_id,update_dt
			  FROM client_his_lvl
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 922;
		 exit;
	}

	/* client_his_limit ********************************************************/
	$sql = 'REPLACE INTO client_his_limit (org_no,jumin,seq,from_dt,to_dt,amt,amt_care,amt_bath,amt_nurse,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',jumin,seq,from_dt,to_dt,amt,amt_care,amt_bath,amt_nurse,insert_id,insert_dt,update_id,update_dt
			  FROM client_his_limit
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 923;
		 exit;
	}

	/* client_his_nurse ********************************************************/
	$sql = 'REPLACE INTO client_his_nurse (org_no,jumin,seq,from_dt,to_dt,svc_val,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',jumin,seq,from_dt,to_dt,svc_val,insert_id,insert_dt,update_id,update_dt
			  FROM client_his_nurse
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 924;
		 exit;
	}

	/* client_his_old ********************************************************/
	$sql = 'REPLACE INTO client_his_old (org_no,jumin,seq,from_dt,to_dt,svc_val,svc_tm,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',jumin,seq,from_dt,to_dt,svc_val,svc_tm,insert_id,insert_dt,update_id,update_dt
			  FROM client_his_old
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 925;
		 exit;
	}

	/* client_his_baby ********************************************************/
	$sql = 'REPLACE INTO client_his_baby (org_no,jumin,seq,from_dt,to_dt,svc_val,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',jumin,seq,from_dt,to_dt,svc_val,insert_id,insert_dt,update_id,update_dt
			  FROM client_his_baby
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 926;
		 exit;
	}

	/* client_his_dis ********************************************************/
	$sql = 'REPLACE INTO client_his_dis (org_no,jumin,seq,from_dt,to_dt,svc_val,svc_lvl,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',jumin,seq,from_dt,to_dt,svc_val,svc_lvl,insert_id,insert_dt,update_id,update_dt
			  FROM client_his_dis
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 927;
		 exit;
	}

	/* client_his_other ********************************************************/
	$sql = 'REPLACE INTO client_his_other (org_no,jumin,svc_cd,seq,svc_val,svc_cost,svc_cnt,recom_nm,recom_tel,recom_amt,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',jumin,svc_cd,seq,svc_val,svc_cost,svc_cnt,recom_nm,recom_tel,recom_amt,insert_id,insert_dt,update_id,update_dt
			  FROM client_his_other
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 928;
		 exit;
	}

	/* client_svc_addpay ********************************************************/
	$sql = 'REPLACE INTO client_svc_addpay (org_no,svc_kind,svc_ssn,svc_seq,school_not_cnt,school_not_pay,school_cnt,school_pay,family_cnt,family_pay,home_in_yn,home_in_pay,holiday_pay,del_flag,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',svc_kind,svc_ssn,svc_seq,school_not_cnt,school_not_pay,school_cnt,school_pay,family_cnt,family_pay,home_in_yn,home_in_pay,holiday_pay,del_flag,insert_id,insert_dt,update_id,update_dt
			  FROM client_svc_addpay
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 929;
		 exit;
	}

	/* client_recom ********************************************************/
	$sql = 'REPLACE INTO client_recom (org_no,cr_jumin,cr_kind,cr_name,cr_tel,cr_amt,cr_use_yn,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',cr_jumin,cr_kind,cr_name,cr_tel,cr_amt,cr_use_yn,insert_id,insert_dt,update_id,update_dt
			  FROM client_recom
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 930;
		 exit;
	}

	/* client_option ********************************************************/
	$sql = 'REPLACE INTO client_option (org_no,jumin,limit_yn)
			SELECT \''.$code2.'\',jumin,limit_yn
			  FROM client_option
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 931;
		 exit;
	}

	/* counsel_client ********************************************************/
	$sql = 'REPLACE INTO counsel_client (org_no,client_dt,client_seq,client_ssn,client_nm,client_counsel,client_phone,client_mobile,client_postno,client_addr,client_addr_dtl,client_protect_nm,client_protect_rel,client_protect_tel,client_family_gbn,client_family_other,client_text_1,client_text_2,client_text_3,del_flag,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',client_dt,client_seq,client_ssn,client_nm,client_counsel,client_phone,client_mobile,client_postno,client_addr,client_addr_dtl,client_protect_nm,client_protect_rel,client_protect_tel,client_family_gbn,client_family_other,client_text_1,client_text_2,client_text_3,del_flag,insert_id,insert_dt,update_id,update_dt
			  FROM counsel_client
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 932;
		 exit;
	}

	/* counsel_client_normal ********************************************************/
	$sql = 'REPLACE INTO counsel_client_normal (org_no,client_dt,client_seq,talker_ssn,talker_nm,talker_type,talker_dt,protect_gbn,protect_other,level_gbn,health_sick_nm,health_drug_nm,health_diag_nm,health_dis_nm,health_eye_kind,health_ear_kind,body_activate_kind,nutr_eat_kind,nutr_excreta_kind,nutr_hygiene_kind,talk_mind_kind,talk_mind_other,talk_status,rec_remember_kind,rec_express_kind,center_use_kind,center_use_other,del_flag,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',client_dt,client_seq,talker_ssn,talker_nm,talker_type,talker_dt,protect_gbn,protect_other,level_gbn,health_sick_nm,health_drug_nm,health_diag_nm,health_dis_nm,health_eye_kind,health_ear_kind,body_activate_kind,nutr_eat_kind,nutr_excreta_kind,nutr_hygiene_kind,talk_mind_kind,talk_mind_other,talk_status,rec_remember_kind,rec_express_kind,center_use_kind,center_use_other,del_flag,insert_id,insert_dt,update_id,update_dt
			  FROM counsel_client_normal
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo $conn->error_query;
		 echo 933;
		 exit;
	}

	/* counsel_client_baby ********************************************************/
	$sql = 'REPLACE INTO counsel_client_baby (org_no,client_dt,client_seq,talker_ssn,talker_nm,talker_type,talker_dt,protect_gbn,protect_other,health_delivery_dt,health_delivery_kind,health_dis_lvl,health_dis_kind,health_nurse,health_mind,health_drug,health_body,family_status,family_abode,family_other,hope_service,svc_dt,svc_period,svc_time,svc_use_amt,other,del_flag,insert_id,insert_dt,update_id,update_dt)
			SELECT \''.$code2.'\',client_dt,client_seq,talker_ssn,talker_nm,talker_type,talker_dt,protect_gbn,protect_other,health_delivery_dt,health_delivery_kind,health_dis_lvl,health_dis_kind,health_nurse,health_mind,health_drug,health_body,family_status,family_abode,family_other,hope_service,svc_dt,svc_period,svc_time,svc_use_amt,other,del_flag,insert_id,insert_dt,update_id,update_dt
			  FROM counsel_client_baby
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 934;
		 exit;
	}

	/* counsel_client_state ********************************************************/
	$sql = 'REPLACE INTO counsel_client_state (org_no,jumin,reg_dt,reg_cd,reg_nm,yoy_cd,yoy_nm,stat,take)
			SELECT \''.$code2.'\',jumin,reg_dt,reg_cd,reg_nm,yoy_cd,yoy_nm,stat,take
			  FROM counsel_client_state
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 935;
		 exit;
	}

	/* counsel_client_phone ********************************************************/
	$sql = 'REPLACE INTO counsel_client_phone (org_no,phone_yymm,phone_seq,phone_c_cd,phone_m_cd,phone_m_nm,phone_dt,phone_kind,phone_start,phone_end,phone_contents,phone_result,phone_other,del_flag,insert_dt,insert_id,update_dt,update_id)
			SELECT \''.$code2.'\',phone_yymm,phone_seq,phone_c_cd,phone_m_cd,phone_m_nm,phone_dt,phone_kind,phone_start,phone_end,phone_contents,phone_result,phone_other,del_flag,insert_dt,insert_id,update_dt,update_id
			  FROM counsel_client_phone
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 936;
		 exit;
	}

	/* counsel_client_visit ********************************************************/
	$sql = 'REPLACE INTO counsel_client_visit (org_no,visit_yymm,visit_seq,visit_c_cd,visit_m_cd,visit_m_nm,visit_dt,visit_tm,visit_cont,visit_h_bp,visit_h_nh,visit_h_bf_bs,visit_h_af_time,visit_h_af_bs,visit_h_cf,visit_h_body,visit_h_soul,visit_other,del_flag,insert_dt,insert_id,update_dt,update_id)
			SELECT \''.$code2.'\',visit_yymm,visit_seq,visit_c_cd,visit_m_cd,visit_m_nm,visit_dt,visit_tm,visit_cont,visit_h_bp,visit_h_nh,visit_h_bf_bs,visit_h_af_time,visit_h_af_bs,visit_h_cf,visit_h_body,visit_h_soul,visit_other,del_flag,insert_dt,insert_id,update_dt,update_id
			  FROM counsel_client_visit
			 WHERE org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 937;
		 exit;
	}


	/*********************************************************

		일정복사

	 *********************************************************/
	/* t01iljung ********************************************************/
	if ($newCYn == 'Y'){
		$sql = 'REPLACE INTO t01iljung (t01_ccode,t01_mkind,t01_jumin,t01_sugup_date,t01_sugup_fmtime,t01_sugup_seq,t01_sugup_totime,t01_sugup_soyotime,t01_sugup_proctime,t01_sugup_yoil,t01_wrk_date,t01_wrk_fmtime,t01_wrk_totime,t01_svc_subcode,t01_svc_subcd,t01_status_gbn,t01_svc_name,t01_toge_umu,t01_bipay_umu,t01_time_doub,t01_yoyangsa_id1,t01_yoyangsa_id2,t01_yoyangsa_id3,t01_yoyangsa_id4,t01_yoyangsa_id5,t01_yname1,t01_yname2,t01_yname3,t01_yname4,t01_yname5,t01_suga_code1,t01_suga,t01_suga_over,t01_suga_night,t01_suga_tot,t01_ysigup,t01_plan_work,t01_plan_sudang,t01_plan_cha,t01_act_work,t01_act_sudang,t01_act_cha,t01_del_yn,t01_trans_yn,t01_mobile_yn,t01_modify_yn,t01_modify_pos,t01_car_no,t01_e_time,t01_n_time,t01_ysudang_yn,t01_ysudang,t01_ysudang_yul1,t01_ysudang_yul2,t01_conf_date,t01_conf_fmtime,t01_conf_totime,t01_conf_soyotime,t01_conf_suga_code,t01_conf_suga_value,t01_holiday,t01_gps_x,t01_gps_y,t01_sudang_conf_yn,t01_mem_cd1,t01_mem_cd2,t01_mem_nm1,t01_mem_nm2,t01_be_plan_yn,t01_bipay_kind,t01_bipay1,t01_bipay2,t01_bipay3,t01_expense_yn,t01_expense_pay,t01_not_school_cnt,t01_not_school_cost,t01_not_school_pay,t01_school_cnt,t01_school_cost,t01_school_pay,t01_family_cnt,t01_family_cost,t01_family_pay,t01_home_in_yn,t01_home_in_cost,t01_holiday_cost)
				SELECT \''.$code2.'\',t01_mkind,t01_jumin,t01_sugup_date,t01_sugup_fmtime,t01_sugup_seq,t01_sugup_totime,t01_sugup_soyotime,t01_sugup_proctime,t01_sugup_yoil,t01_wrk_date,t01_wrk_fmtime,t01_wrk_totime,t01_svc_subcode,t01_svc_subcd,t01_status_gbn,t01_svc_name,t01_toge_umu,t01_bipay_umu,t01_time_doub,t01_yoyangsa_id1,t01_yoyangsa_id2,t01_yoyangsa_id3,t01_yoyangsa_id4,t01_yoyangsa_id5,t01_yname1,t01_yname2,t01_yname3,t01_yname4,t01_yname5,t01_suga_code1,t01_suga,t01_suga_over,t01_suga_night,t01_suga_tot,t01_ysigup,t01_plan_work,t01_plan_sudang,t01_plan_cha,t01_act_work,t01_act_sudang,t01_act_cha,t01_del_yn,t01_trans_yn,t01_mobile_yn,t01_modify_yn,t01_modify_pos,t01_car_no,t01_e_time,t01_n_time,t01_ysudang_yn,t01_ysudang,t01_ysudang_yul1,t01_ysudang_yul2,t01_conf_date,t01_conf_fmtime,t01_conf_totime,t01_conf_soyotime,t01_conf_suga_code,t01_conf_suga_value,t01_holiday,t01_gps_x,t01_gps_y,t01_sudang_conf_yn,t01_mem_cd1,t01_mem_cd2,t01_mem_nm1,t01_mem_nm2,t01_be_plan_yn,t01_bipay_kind,t01_bipay1,t01_bipay2,t01_bipay3,t01_expense_yn,t01_expense_pay,t01_not_school_cnt,t01_not_school_cost,t01_not_school_pay,t01_school_cnt,t01_school_cost,t01_school_pay,t01_family_cnt,t01_family_cost,t01_family_pay,t01_home_in_yn,t01_home_in_cost,t01_holiday_cost
				  FROM t01iljung
				 WHERE t01_ccode       = \''.$code1.'\'
				   AND t01_sugup_date >= \''.$newCDt.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->row_free();
			 echo 938;
			 exit;
		}
	}

	if ($nowCYn == 'Y'){
		$sql = 'UPDATE t01iljung
				   SET t01_del_yn = \'Y\'
				 WHERE t01_ccode = \''.$code1.'\'
				   AND t01_sugup_date >= \''.$nowCDt.'\'';

		if (!$conn->execute($sql)){
			 $conn->rollback();
			 $conn->row_free();
			 echo 939;
			 exit;
		}
	}


	/*********************************************************
		재가지원
	 *********************************************************/
	/*
	$sql = 'REPLACE	INTO client_his_care
			SELECT	\''.$code2.'\',jumin,svc_cd,seq,care_cost,care_org_no,care_org_nm,care_no,care_lvl,care_gbn,care_pic_nm,care_telno,insert_id,insert_dt,update_id,update_dt
			FROM	client_his_care
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 940;
		 exit;
	}


	$sql = 'REPLACE	INTO care_client_normal
			SELECT	\''.$code2.'\',normal_sr,normal_seq,jumin,name,postno,addr,addr_dtl,phone,mobile,grd_nm,grd_addr,grd_telno,marry_gbn,cohabit_gbn,edu_gbn,rel_gbn,longcare_lvl,longcare_gbn,link_IPIN,del_flag,insert_dt,insert_id,update_dt,update_id
			FROM	care_client_normal
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 941;
		 exit;
	}


	$sql = 'REPLACE	INTO care_close_day
			SELECT	\''.$code2.'\',close_sr,close_gbn,close_cd,close_date,close_cnt,close_pay,close_per
			FROM	care_close_day
			WHERE	org_no = \''.$code1.'\'';

	if ($useYn == 'Y'){
		$sql .= '
			AND		close_date >= \''.$useDt.'\'';
	}

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 942;
		 exit;
	}


	$sql = 'REPLACE	INTO care_close_month
			SELECT	\''.$code2.'\',close_sr,close_gbn,close_cd,close_yymm,close_cnt,close_pay,close_per
			FROM	care_close_month
			WHERE	org_no = \''.$code1.'\'';

	if ($useYn == 'Y'){
		$sql .= '
			AND		close_yymm >= \''.SubStr($useDt,0,6).'\'';
	}

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 943;
		 exit;
	}


	$sql = 'REPLACE	INTO care_close_person
			SELECT	\''.$code2.'\',close_sr,close_yymm,close_jumin,close_idx,close_suga,close_resource,close_mem_cd,close_cnt,close_pay,close_per
			FROM	care_close_person
			WHERE	org_no = \''.$code1.'\'';

	if ($useYn == 'Y'){
		$sql .= '
			AND		close_yymm >= \''.SubStr($useDt,0,6).'\'';
	}

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 944;
		 exit;
	}


	$sql = 'REPLACE	INTO care_counsel_iljung
			SELECT	\''.$code2.'\',jumin,iljung_sr,iljung_dt,iljung_seq,iljung_jumin,iljung_from,iljung_to,iljung_proc,iljung_stat,del_flag
			FROM	care_counsel_iljung
			WHERE	org_no = \''.$code1.'\'
			AND		del_flag = \'N\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 945;
		 exit;
	}


	$sql = 'REPLACE	INTO care_cust
			SELECT	\''.$code2.'\',cust_cd,cust_nm,cust_gbn,biz_no,manager,status,item,phone,fax,postno,addr,addr_dtl,per_nm,per_phone,support_yn,resource_yn,del_flag,insert_id,insert_dt,update_id,update_dt
			FROM	care_cust
			WHERE	org_no = \''.$code1.'\'
			AND		del_flag = \'N\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 946;
		 exit;
	}


	$sql = 'REPLACE	INTO care_resource
			SELECT	\''.$code2.'\',care_sr,care_svc,care_sub,care_cd,care_cust,care_cost,from_dt,to_dt,del_flag
			FROM	care_resource
			WHERE	org_no = \''.$code1.'\'
			AND		del_flag = \'N\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 947;
		 exit;
	}


	$sql = 'REPLACE	INTO care_result
			SELECT	\''.$code2.'\',org_type,jumin,date,time,seq,no,content,picture,file,del_flag,insert_id,insert_dt,update_id,update_dt
			FROM	care_result
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 948;
		 exit;
	}


	$sql = 'REPLACE	INTO care_suga
			SELECT	\''.$code2.'\',suga_sr,suga_cd,suga_sub,suga_nm,suga_seq,suga_cost,from_dt,to_dt,insert_dt,insert_id,update_dt,update_id
			FROM	care_suga
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 949;
		 exit;
	}


	$sql = 'REPLACE	INTO care_svc_his
			SELECT	\''.$code2.'\',jumin,seq,org_nm,svc_cd,from_dt,to_dt,person_nm,telno
			FROM	care_svc_his
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 950;
		 exit;
	}


	$sql = 'REPLACE	INTO care_year_plan
			SELECT	\''.$code2.'\',plan_year,plan_sr,plan_cd,plan_target,plan_target_gbn,plan_budget,plan_cnt,plan_cont,plan_effect,plan_eval,insert_id,insert_dt,update_id,update_dt
			FROM	care_year_plan
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 951;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_choice
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,chic_seq,chic_dt,income_gbn,income_point,nonfamily_gbn,nonfamily_point,dwelling_gbn,dwelling_point,rental_gbn,rental_point,gross_gbn,gross_point,public_gbn,public_point,help_gbn,help_poing,body_gbn,body_point,body_patient_gbn,body_patient_point,feel_gbn,feel_point,feel_patient_gbn,feel_patient_point,handicap_gbn,handicap_point,handi_dup_gbn,handi_dup_point,handi_2per_gbn,handi_2per_point,adl_gbn,adl_point,care_gbn,care_point,free_gbn,free_point,total_point,comment,choice_rst,insert_id,insert_dt,update_id,update_dt
			FROM	hce_choice
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 952;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_consent_form
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,cont_dt,per_nm,per_jumin,insert_id,insert_dt,update_id,update_dt
			FROM	hce_consent_form
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 953;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_consent_svc
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,cont_seq,svc_nm,content,remark
			FROM	hce_consent_svc
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 954;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_evaluation
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,ev_dt,ev_hm,ever,ever_jumin,quest_1,quest_2,quest_3,quest_4,quest_5,quest_6,quest_7,quest_8,quest_9,quest_10,quest_11,quest_12,quest_13,quest_14,text_1,text_2,text_3,text_4,text_5,insert_id,insert_dt,update_id,update_dt
			FROM	hce_evaluation
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 955;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_family
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,seq,family_rel,family_nm,family_addr,family_age,family_job,family_cohabit,family_monthly,family_remark
			FROM	hce_family
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 956;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_inspection
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,ispt_seq,ispt_dt,ispt_from,ispt_to,counsel_type,iver_nm,iver_jumin,work_amt,live_aid_amt,basic_old_amt,ext_aid_amt,support_amt,support_aid_amt,dwelling_env,dwelling_env_other,elv_yn,house_stat,house_stat_fault,clean_stat,clean_stat_fault,heat_gbn,heat_material,heat_other,toilet_gbn,toilet_type,moving_stat,physical_problem_gbn,physical_problem_other,mental_problem_gbn,mental_problem_other,past_medi_his,curr_medi_his,per_family_cnt,per_cost_gbn,per_medical_gbn,remark,insert_id,insert_dt,update_id,update_dt
			FROM	hce_inspection
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 957;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_inspection_adl
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,ispt_seq,base_door,base_shoes,base_shoes_put,base_chair,per_bath,per_wash,per_groom,per_in_dress,per_out_dress,wc_bedpan,wc_after,wc_feces,wc_urine,eat_spoon,eat_stick,eat_poke,eat_cup,eat_grip_cup,walk_100m,walk_hand,walk_stair,bed_sitdown,bed_standup,bed_lie,bed_turn,bed_tidy,insert_id,insert_dt,update_id,update_dt
			FROM	hce_inspection_adl
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 958;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_inspection_feel
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,ispt_seq,feel1_yn,feel2_yn,feel2_rsn,feel3_yn,feel4_yn,feel4_rsn,feel5_yn,feel6_yn,feel6_eft,feel7_yn,feel7_cnt,feel7_whn,feel7_rsn,insert_id,insert_dt,update_id,update_dt
			FROM	hce_inspection_feel
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 959;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_inspection_iadl
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,ispt_seq,phone,outdoor,buying,eating,homework,cleaning,medicine,money,repair,insert_id,insert_dt,update_id,update_dt
			FROM	hce_inspection_iadl
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 960;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_inspection_mmseds
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,ispt_seq,edu_training,kor_decode,life_job,check_dt,evl_place,Q1,Q2,Q3,Q4,Q5,Q6,Q7,Q8,Q9,Q10,Q11,Q12,Q13,Q14,Q15,Q16,Q17,Q18,Q19,Q20,Q21,Q22,Q23,Q24,Q25,Q26,Q27,Q28,Q29,Q30,insert_id,insert_dt,update_id,update_dt
			FROM	hce_inspection_mmseds
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 961;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_inspection_needs
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,ispt_seq,lifedays,faircopy,dwelling,leisure,interview,local,link,educ,emergency,ext,social_opinion,rough_text,rough_file,insert_id,insert_dt,update_id,update_dt
			FROM	hce_inspection_needs
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 962;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_inspection_sgds
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,ispt_seq,Q1,Q2,Q3,Q4,Q5,Q6,Q7,Q8,Q9,Q10,Q11,Q12,Q13,Q14,Q15,insert_id,insert_dt,update_id,update_dt
			FROM	hce_inspection_sgds
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 963;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_inspection_social
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,ispt_seq,social1,social2,social2_rsn,social3,social4,social4_rsn,social5,social6,social6_rsn,social7,social7_nm,social7_tel,social8,social8_other,social9,social9_other,insert_id,insert_dt,update_id,update_dt
			FROM	hce_inspection_social
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 964;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_interview
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,iver_dt,iver_nm,iver_jumin,income_gbn,income_other,income_monthly,income_main,generation_gbn,generation_other,dwelling_gbn,dwelling_other,house_gbn,house_other,deposit_amt,rental_amt,health_gbn,health_other,disease_gbn,handicap_gbn,handicap_other,device_gbn,device_other,longlvl_gbn,longlvl_other,other_svc_nm,other_org_nm,req_svc_gbn,offer_gbn,nooffer_rsn,svc_rsn_gbn,svc_rsn_other,offer_svc_gbn,req_nm,req_rel,req_telno,req_route_gbn,remark,insert_id,insert_dt,update_id,update_dt
			FROM	hce_interview
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 965;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_map
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,ispt_seq,family_remark,ecomap_remark,remark
			FROM	hce_map
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 967;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_meeting
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,meet_seq,meet_gbn,meet_dt,examiner_jumin,examiner,attendee,life_lvl,req_rsn,decision_gbn,decision_dt,decision_rsn,decision_svc,del_flag,insert_id,insert_dt,update_id,update_dt
			FROM	hce_meeting
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 968;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_monitor
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,mntr_seq,mntr_dt,mntr_gbn,mntr_type,per_nm,per_jumin,inspector_nm,inspector_jumin,schedule_sat,schedule_svc,fullness_sat,fullness_svc,perincharge_sat,perincharge_svc,ability_change,life_env_change,ext_discomfort,monitor_rst,ext_detail,del_flag,insert_id,insert_dt,update_id,update_dt
			FROM	hce_monitor
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 969;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_plan_sheet
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,plan_seq,meet_seq,plan_dt,planer_jumin,planer,needs,problem,goal,svc_period,svc_content,svc_method,remark,del_flag,insert_id,insert_dt,update_id,update_dt
			FROM	hce_plan_sheet
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 970;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_proc
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,hce_seq,rcpt_dt,itvw_dt,ispt_dt,chic_dt,meet_dt1,meet_dt2,meet_dt3,meet_dt4,meet_gbn,plan_dt,cont_dt,cusl_dt,conn_dt,mntr_dt,rest_dt,evln_dt,end_yn
			FROM	hce_proc
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 971;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_proc_counsel
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,proc_seq,counsel_dt,counsel_nm,counsel_jumin,counsel_gbn,counsel_text,counsel_remark,del_flag,insert_id,insert_dt,update_id,update_dt
			FROM	hce_proc_counsel
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 972;
		 exit;
	}



	$sql = 'REPLACE	INTO hce_re_ispt
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,ispt_seq,ispt_dt,per_nm,per_jumin,ispt_gbn,ispt_rsn,client_need_change,svc_offer_problem,wer_opion,ispt_rst,after_plan,del_flag,insert_id,insert_dt,update_id,update_dt
			FROM	hce_re_ispt
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 973;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_receipt
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,rcpt_dt,create_id,create_dt,update_id,update_dt,del_flag,counsel_type,hce_seq,phone,mobile,postno,addr,addr_dtl,grd_nm,grd_rel,grd_tel,grd_addr,reqor_rel,reqor_nm,reqor_telno,rcver_nm,rcver_ssn,marry_gbn,cohabit_gbn,edu_gbn,rel_gbn,counsel_text,end_flag,end_dt,end_rsn
			FROM	hce_receipt
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 974;
		 exit;
	}


	$sql = 'REPLACE	INTO hce_svc_connect
			SELECT	\''.$code2.'\',org_type,IPIN,rcpt_seq,conn_seq,conn_orgno,conn_orgnm,per_nm,per_jumin,req_dt,reqor_nm,reqor_rel,req_rsn,req_text,del_flag,insert_id,insert_dt,update_id,update_dt
			FROM	hce_svc_connect
			WHERE	org_no = \''.$code1.'\'';

	if (!$conn->execute($sql)){
		 $conn->rollback();
		 $conn->row_free();
		 echo 975;
		 exit;
	}
	*/

	$conn->commit();

	echo 1;

	include_once('../inc/_db_close.php');
?>