<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	echo $myF->header_script();

	$conn->mode = 1;

	$code = $_POST['code'];
	$kind = '0';
	$flag = $_POST['flag'];

	$svc_cd		= $_POST['svc_cd'];
	$c_cd		= $_POST['c_cd'];
	$m_cd1		= $_POST['m_cd1'];
	$m_cd2		= $_POST['m_cd2'];
	$m_nm1		= $_POST['m_nm1'];
	$m_nm2		= $_POST['m_nm2'];
	$date		= $_POST['date'];
	$work_from	= $_POST['work_from'];
	$work_to	= $_POST['work_to'];
	$work_time	= $_POST['work_time'];
	$conf_from	= $_POST['conf_from'];
	$conf_to	= $_POST['conf_to'];
	$conf_time	= $_POST['conf_time'];

	$suga_code			= $_POST['suga_code'];
	$suga_name			= $_POST['suga_name'];
	$suga_cost			= $_POST['suga_cost'];
	$suga_evening_cost	= $_POST['suga_evening_cost'];
	$suga_night_cost	= $_POST['suga_night_cost'];
	$suga_total_cost	= $_POST['suga_total_cost'];
	$suga_sudang_pay	= $_POST['suga_sudang_pay'];
	$suga_evening_time	= $_POST['suga_evening_time'];
	$suga_night_time	= $_POST['suga_night_time'];
	$suga_evening_yn	= $_POST['suga_evening_yn'];
	$suga_night_yn		= $_POST['suga_night_yn'];

	$holiday_yn	= $_POST['holiday_yn'];

	$sql = 'select m00_muksu_yul1, m00_muksu_yul2
			  from m00center
			 where m00_mcode = \''.$code.'\'
			   and m00_mkind = \'0\'';

	$suga_sudang_rate = $conn->get_array($sql);

	$cnt = sizeof($svc_cd);

	$year	  = $_POST['year'];
	$year_cnt = sizeof($year);

	for($y=0; $y<$year_cnt; $y++){
		for($m=1; $m<=12; $m++){
			$month[$year[$y]][$m] = $_POST['obj_'.$year[$y].($m<10?'0':'').$m];

			if ($month[$year[$y]][$m] != 'Y')
				$month[$year[$y]][$m] = 'N';
		}
	}





	$conn->begin();

	$yymm = '';

	foreach($month as $year => $m){
		foreach($m as $mon => $yn){
			if ($yn == 'Y'){
				$str_yymm = $year.($mon<10?'0':'').$mon;

				//기존데이타 삭제
				if ($flag == 'Y'){
					$sql = 'update t01iljung
							   set t01_del_yn              = \'Y\'
							,      t01_request             = \'CSV\'
							 where t01_ccode               = \''.$code.'\'
							   and left(t01_sugup_date, 6) = \''.$str_yymm.'\'';

					if (!$conn->execute($sql)){
						$conn->rollback();
						echo $conn->err_back();
						if ($conn->mode == 1) exit;
					}
				}

				$yymm .= $str_yymm.' / ';
			}
		}
	}

	for($i=0; $i<$cnt; $i++){
		if (!is_numeric(strpos($yymm, substr($data[$i],0,6)))){
			$sql = 'select ifnull(max(t01_sugup_seq), 0)
					  from t01iljung
					 where t01_ccode        = \''.$code.'\'
					   and t01_mkind        = \''.$kind.'\'
					   and t01_jumin        = \''.$c_cd[$i].'\'
					   and t01_sugup_date   = \''.$date[$i].'\'
					   and t01_sugup_fmtime = \''.$conf_from[$i].'\'';

			$seq = $conn->get_data($sql);

			if ($seq == 0){
				$seq = 1;
				$add = true;
			}else{
				if ($flag == 'Y') $seq ++;
				$add = false;
			}

			if ($flag == 'Y') $add = true;

			if ($add){
				$sql = 'insert into t01iljung (
						 t01_ccode
						,t01_mkind
						,t01_jumin
						,t01_sugup_date
						,t01_sugup_fmtime
						,t01_sugup_seq) values (
						 \''.$code.'\'
						,\''.$kind.'\'
						,\''.$c_cd[$i].'\'
						,\''.$date[$i].'\'
						,\''.$conf_from[$i].'\'
						,\''.$seq.'\')';
			}

			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $sql;
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}

			$sql = 'update t01iljung
					   set t01_sugup_totime		= \''.$conf_to[$i].'\'
					,      t01_sugup_soyotime	= \''.$conf_time[$i].'\'
					,      t01_sugup_yoil		= \''.date('w', strtotime($myF->dateStyle($date[$i]))).'\'
					,      t01_wrk_date			= \''.$date[$i].'\'
					,      t01_wrk_fmtime		= \''.$work_from[$i].'\'
					,      t01_wrk_totime		= \''.$work_to[$i].'\'
					,      t01_svc_subcode		= \''.$svc_cd[$i].'\'
					,      t01_svc_subcd		= \'1\'
					,      t01_status_gbn		= \'1\'
					,      t01_yoyangsa_id1		= \''.$m_cd1[$i].'\'
					,      t01_yoyangsa_id2		= \''.$m_cd2[$i].'\'
					,      t01_yname1			= \''.$m_nm1[$i].'\'
					,      t01_yname2			= \''.$m_nm2[$i].'\'
					,      t01_suga_code1		= \''.$suga_code[$i].'\'
					,      t01_suga				= \''.$suga_cost[$i].'\'
					,      t01_suga_over		= \''.$suga_evening_cost[$i].'\'
					,      t01_suga_night		= \''.$suga_night_cost[$i].'\'
					,      t01_suga_tot			= \''.$suga_total_cost[$i].'\'
					,      t01_e_time			= \''.$suga_evening_time[$i].'\'
					,      t01_n_time			= \''.$suga_night_time[$i].'\'
					,      t01_ysudang_yn		= \''.($svc_cd[$i] != '200' ? 'Y' : 'N').'\'
					,      t01_ysudang			= \''.$suga_sudang_pay[$i].'\'
					,      t01_ysudang_yul1		= \''.$suga_sudang_rate[0].'\'
					,      t01_ysudang_yul2		= \''.$suga_sudang_rate[1].'\'
					,      t01_conf_date		= \''.$date[$i].'\'
					,      t01_conf_fmtime		= \''.$conf_from[$i].'\'
					,      t01_conf_totime		= \''.$conf_to[$i].'\'
					,      t01_conf_soyotime	= \''.$conf_time[$i].'\'
					,      t01_conf_suga_code	= \''.$suga_code[$i].'\'
					,      t01_conf_suga_value	= \''.$suga_cost[$i].'\'
					,      t01_holiday			= \''.$holiday_yn[$i].'\'
					,      t01_request			= \'CSV\'

					 where t01_ccode			= \''.$code.'\'
					   and t01_mkind			= \''.$kind.'\'
					   and t01_jumin			= \''.$c_cd[$i].'\'
					   and t01_sugup_date		= \''.$date[$i].'\'
					   and t01_sugup_fmtime		= \''.$conf_from[$i].'\'
					   and t01_sugup_seq		= \''.$seq.'\'';

			if (!$conn->execute($sql)){
				$conn->rollback();
				echo $conn->err_back();
				if ($conn->mode == 1) exit;
			}
		}
	}

	$conn->commit();

	include_once('../inc/_db_close.php');

	echo '<script language=\'javascript\'>';
	echo 'alert(\''.$myF->message('ok','N').'\');';

	if ($conn->mode == 1) echo 'self.close();';

	echo '</script>';
?>