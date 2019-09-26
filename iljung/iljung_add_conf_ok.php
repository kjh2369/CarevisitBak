<?
	include_once('../inc/_db_open.php');
	include_once('../inc/_login.php');
	include_once('../inc/_myFun.php');
	include_once('../inc/_ed.php');

	if (empty($_POST)) include_once('../inc/_http_home.php');

	echo $myF->header_script();

	$conn->mode = 1;
	$code		= $_POST['code']; #기관기호
	$svc_id		= $_POST['svc_id'][0]; #선택한 서비스 아이디
	$kind_list	= $conn->kind_list($code, true); #기관에서 사용하는 서비스 리트스
	$kind		= $conn->kind_code($kind_list, $svc_id); #서비스 구분코드
	$c_cd		= $ed->de($_POST['jumin']); #고객
	$m_cd1		= $ed->de($_POST['yoy1']);
	$m_nm1		= $_POST['yoyNm1'];
	$m_cd2		= $ed->de($_POST['yoy2']);
	$m_nm2		= $_POST['yoyNm2'];
	$m_hourly	= str_replace(',', '', $_POST['yoyTA1']); #시급
	$date		= $_POST['year'].'-'.$_POST['month'].'-'.$_POST['day']; #등록일자
	$from_time	= $_POST['ftHour'].$_POST['ftMin'];
	$to_time	= $_POST['ttHour'].$_POST['ttMin'];
	$soyotime	= (intval($_POST['ttHour']) * 60 + intval($_POST['ttMin'])) - (intval($_POST['ftHour']) * 60 + intval($_POST['ftMin'])); #진행시간
	$procTime	= $_POST['procTime']; #진행시간
	$sub_cd		= $_POST['svcSubCode']; #재가 서비스구분
	$sub_cd_sub = (!empty($_POST['svcSubCD']) ? $_POST['svcSubCD'] : '1'); #목욕 차량구분
	$family_yn	= ($_POST['togeUmu'] == 'Y' ? 'Y' : 'N'); #동거여부
	$bipay_yn	= ($_POST['bipayUmu'] == 'Y' ? 'Y' : 'N'); #비급여여부
	$suga_cd	= $_POST['sugaCode']; #수가코드
	$suga_nm	= $_POST['sugaName']; #수가명

	if ($svc_id == 11){
		$suga_amt	= str_replace(',', '', $_POST['sPrice']); #수가
		$suga_over	= str_replace(',', '', $_POST['ePrice']); #연장초가금액
		$suga_night	= str_replace(',', '', $_POST['nPrice']); #야간초가금액
		$suga_tot	= str_replace(',', '', $_POST['tPrice']); #총금액
	}else{
		$suga_amt	= str_replace(',', '', $_POST['sugaCost']); #수가
		$suga_over	= str_replace(',', '', $_POST['sugaCostNight']); #연장초가금액
		$suga_night	= 0; #야간초가금액
		$suga_tot	= str_replace(',', '', $_POST['sugaTot']); #총금액
	}

	$car_no		= $_POST['carNo'];
	$e_time		= $_POST['Etime'];
	$n_time		= $_POST['Ntime'];
	$sd_yn		= ($_POST['visitSudangCheck'] == 'Y' ? 'Y' : 'N');
	$sd_pay		= (!empty($_POST['visitSudang']) ? str_replace(',', '', $_POST['visitSudang']) : 0);
	$sd_rate1	= (!empty($_POST['sudangYul1']) ? $_POST['sudangYul1'] : 0);
	$sd_rate2	= (!empty($_POST['sudangYul2']) ? $_POST['sudangYul2'] : 0);
	$holiday_yn	= 'N';
	$modify_yn	= 'N';
	$weekday	= date('w', strtotime($date)); #요일
	$date		= str_replace('-', '', $date);
	$sugup_date	= '00000000';
	$sugup_time	= '';


	/*****************************************

		휴일여부

	*****************************************/
	if ($weekday == 0) $holiday_yn = 'Y';
	if ($holiday_yn == 'N'){
		/*****************************************

			휴일리스트

			*************************************/
			$sql = "select mdate as dt, ifnull(holiday_name, '') as nm
					  from tbl_holiday
					 where mdate like '".$calYear.$calMonth."%'
					 order by mdate";
			$conn->query($sql);
			$conn->fetch();
			$row_count = $conn->row_count();

			for($l=0; $l<$row_count; $l++){
				$row = $conn->select_row($l);

				if ($row['dt'] == $date){
					$holiday_yn = 'Y';
					break;
				}
			}
			$hd_cnt = sizeof($hd_list);

			$conn->row_free();
		/****************************************/
	}



	/*****************************************

		순번

	*****************************************/
	$sql = 'select ifnull(max(t01_sugup_seq), 0) + 1
			  from t01iljung
			 where t01_ccode        = \''.$code.'\'
			   and t01_mkind        = \''.$kind.'\'
			   and t01_jumin        = \''.$c_cd.'\'
			   and t01_sugup_date   = \''.$date.'\'
			   and t01_sugup_fmtime = \''.$sugup_time.'\'';

	$seq = $conn->get_data($sql);



	/*****************************************

		transaction begin

	*****************************************/
	$conn->begin();



	/*****************************************

		save query

	*****************************************/
	$sql = 'insert into t01iljung (
			 t01_ccode
			,t01_mkind
			,t01_jumin
			,t01_sugup_date
			,t01_sugup_fmtime
			,t01_sugup_seq) values (
			 \''.$code.'\'
			,\''.$kind.'\'
			,\''.$c_cd.'\'
			,\''.$date.'\'
			,\''.$sugup_time.'\'
			,\''.$seq.'\')';

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}


	$sql = 'update t01iljung
			   set t01_sugup_yoil		= \''.$weekday.'\'
			,      t01_svc_subcode		= \''.$sub_cd.'\'
			,      t01_svc_subcd		= \''.$sub_cd_sub.'\'
			,      t01_status_gbn		= \'1\'
			,      t01_toge_umu			= \''.$family_yn.'\'
			,      t01_bipay_umu		= \''.$bipay_yn.'\'

			,      t01_suga_code1       = \''.$suga_cd.'\'
			,      t01_suga             = \''.$suga_amt.'\'
			,      t01_suga_over        = \''.$suga_over.'\'
			,      t01_suga_night       = \''.$suga_night.'\'
			,      t01_suga_tot         = \''.$suga_tot.'\'

			,      t01_yoyangsa_id1		= \''.$m_cd1.'\'
			,      t01_yoyangsa_id2		= \''.$m_cd2.'\'
			,      t01_yname1			= \''.$m_nm1.'\'
			,      t01_yname2			= \''.$m_nm2.'\'
			,      t01_ysigup			= \''.$m_hourly.'\'
			,      t01_modify_yn		= \''.$modify_yn.'\'
			,      t01_car_no			= \''.$car_no.'\'
			,      t01_ysudang_yn		= \''.$sd_yn.'\'
			,      t01_ysudang			= \''.$sd_pay.'\'
			,      t01_ysudang_yul1		= \''.$sd_rate1.'\'
			,      t01_ysudang_yul2		= \''.$sd_rete2.'\'
			,      t01_conf_date		= \''.$date.'\'
			,      t01_conf_fmtime		= \''.$from_time.'\'
			,      t01_conf_totime		= \''.$to_time.'\'
			,      t01_conf_soyotime	= \''.$soyotime.'\'
			,      t01_conf_suga_code	= \''.$suga_cd.'\'
			,      t01_conf_suga_value	= \''.$suga_tot.'\'
			,      t01_holiday			= \''.$holiday_yn.'\'
			,      t01_mem_cd1			= \''.$m_cd1.'\'
			,      t01_mem_cd2			= \''.$m_cd2.'\'
			,      t01_mem_nm1			= \''.$m_nm1.'\'
			,      t01_mem_nm2			= \''.$m_nm2.'\'
			,      t01_be_plan_yn		= \'N\'

			 where t01_ccode			= \''.$code.'\'
			   and t01_mkind			= \''.$kind.'\'
			   and t01_jumin			= \''.$c_cd.'\'
			   and t01_sugup_date		= \''.$date.'\'
			   and t01_sugup_fmtime		= \''.$sugup_time.'\'
			   and t01_sugup_seq		= \''.$seq.'\'';

	if (!$conn->execute($sql)){
		$conn->rollback();
		echo $conn->err_back();
		if ($conn->mode == 1) exit;
	}



	/*****************************************

		transaction commit

	*****************************************/
	$conn->commit();




	include_once('../inc/_db_close.php');


	echo '<script>';
	echo 'alert(\''.$myF->message('ok','N').'\');';

	if ($conn->mode == 1){
		echo 'window.returnValue = \'ok\';
			  window.close();';
	}

	echo '</script>';
?>